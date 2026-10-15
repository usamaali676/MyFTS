<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

/**
 * Turns whatever photo a user uploads into a small square WebP (or JPEG, where
 * WebP is unavailable) avatar:
 * auto-rotated, centre-cropped, resized, and compressed to 100 KB or less —
 * the user never has to resize anything themselves.
 *
 * Files live under User::AVATAR_DIR, written with a path relative to the web
 * root exactly like the app's existing business/ and reports/ uploads.
 */
class AvatarService
{
    public const MAX_BYTES = 102400; // 100 KB

    private const SIZES = [400, 320, 256, 192];
    private const QUALITIES = [85, 75, 65, 55, 45, 35];
    private const MAX_PIXELS = 40_000_000;

    public function store(UploadedFile $file, User $user): string
    {
        if (!$this->hasAvatarColumn()) {
            throw new InvalidArgumentException(
                'Profile photos need a one-time database update on this server (run: php artisan migrate).'
            );
        }

        // getPathname(), not getRealPath(): realpath() returns false on hosts
        // whose upload temp dir is symlinked / behind open_basedir, which
        // surfaced as "Path cannot be empty" even for a perfectly valid upload.
        ['binary' => $binary, 'ext' => $ext] = $this->optimize($file->getPathname());

        $dir = $this->directory();

        if (!File::isDirectory($dir) && !@mkdir($dir, 0775, true) && !File::isDirectory($dir)) {
            throw new InvalidArgumentException(
                "The server could not create the \"{$dir}\" folder for profile photos. Please ask your host to allow writing there."
            );
        }

        if (!is_writable($dir)) {
            throw new InvalidArgumentException(
                "The \"{$dir}\" folder isn't writable by the web server. Please ask your host to allow writing there."
            );
        }

        $name = 'user-' . $user->id . '-' . time() . '.' . $ext;

        if (File::put($dir . '/' . $name, $binary) === false) {
            throw new InvalidArgumentException('The photo could not be written to disk. Please try again.');
        }

        $oldAvatar = $user->avatar;
        $user->avatar = $name;

        try {
            $user->save();
        } catch (\Throwable $e) {
            File::delete($dir . '/' . $name);
            throw $e;
        }

        $this->deleteFile($oldAvatar);

        return $name;
    }

    public function remove(User $user): void
    {
        $this->deleteFile($user->avatar);

        $user->avatar = null;
        $user->save();
    }

    /**
     * @return array{binary: string, ext: string}
     * @throws InvalidArgumentException with a message safe to show the user
     */
    public function optimize(string $path): array
    {
        if (!extension_loaded('gd') || !function_exists('imagecopyresampled')) {
            throw new InvalidArgumentException('Image processing (the PHP GD extension) is not enabled on this server.');
        }

        if ($path === '' || !is_readable($path)) {
            throw new InvalidArgumentException('The uploaded file could not be read. Please try again.');
        }

        $info = @getimagesize($path);

        if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF], true)) {
            throw new InvalidArgumentException('Please choose a JPG, PNG, WebP or GIF image.');
        }

        if ($info[0] * $info[1] > self::MAX_PIXELS) {
            throw new InvalidArgumentException('That image is too large to process. Please pick a smaller photo.');
        }

        $this->ensureMemoryFor($info[0], $info[1]);

        $loader = match ($info[2]) {
            IMAGETYPE_JPEG => 'imagecreatefromjpeg',
            IMAGETYPE_PNG => 'imagecreatefrompng',
            IMAGETYPE_WEBP => 'imagecreatefromwebp',
            IMAGETYPE_GIF => 'imagecreatefromgif',
        };

        if (!function_exists($loader)) {
            throw new InvalidArgumentException('This server cannot read that image type. Please try a JPG or PNG.');
        }

        $source = @$loader($path);

        if (!$source) {
            throw new InvalidArgumentException('That image could not be read. Please try a different one.');
        }

        if ($info[2] === IMAGETYPE_JPEG) {
            $source = $this->applyExifOrientation($source, $path);
        }

        $square = $this->centreCropSquare($source);
        unset($source);

        // WebP is smallest, but fall back to JPEG on a server whose image
        // library was built without WebP support.
        $webp = $this->supportsWebp();
        $ext = $webp ? 'webp' : 'jpg';

        foreach (self::SIZES as $size) {
            $scaled = $this->resize($square, $size);

            if (!$webp) {
                $scaled = $this->flattenOnWhite($scaled);
            }

            foreach (self::QUALITIES as $quality) {
                ob_start();
                $webp ? imagewebp($scaled, null, $quality) : imagejpeg($scaled, null, $quality);
                $binary = ob_get_clean();

                if ($binary !== false && $binary !== '' && strlen($binary) <= self::MAX_BYTES) {
                    return ['binary' => $binary, 'ext' => $ext];
                }
            }
        }

        throw new InvalidArgumentException('That image could not be compressed small enough. Please try a different one.');
    }

    /**
     * Resizes with imagecopyresampled (always available with GD) rather than
     * imagescale(), keeping any transparency.
     */
    private function resize($image, int $size)
    {
        $out = imagecreatetruecolor($size, $size);
        imagealphablending($out, false);
        imagesavealpha($out, true);
        imagefill($out, 0, 0, imagecolorallocatealpha($out, 0, 0, 0, 127));
        imagecopyresampled($out, $image, 0, 0, 0, 0, $size, $size, imagesx($image), imagesy($image));

        return $out;
    }

    /**
     * GD holds roughly width x height x 4 bytes per copy, and a few copies
     * live at once. Rather than dying with a fatal "memory exhausted" on a
     * small shared host, check up front and ask for a smaller photo.
     */
    private function ensureMemoryFor(int $width, int $height): void
    {
        $needed = (int) ($width * $height * 4 * 3) + 8 * 1024 * 1024;

        if ($this->memoryLimitBytes() !== -1
            && $this->memoryLimitBytes() - memory_get_usage(true) < $needed) {
            @ini_set('memory_limit', (string) ($needed + memory_get_usage(true) + 32 * 1024 * 1024));
        }

        $limit = $this->memoryLimitBytes();

        if ($limit !== -1 && $limit - memory_get_usage(true) < $needed) {
            throw new InvalidArgumentException(
                'That photo is very large for this server to process. Please pick a smaller one (under about 2000 px wide).'
            );
        }
    }

    private function memoryLimitBytes(): int
    {
        $raw = trim((string) ini_get('memory_limit'));

        if ($raw === '' || $raw === '-1') {
            return -1;
        }

        $number = (float) $raw;

        return (int) match (strtolower(substr($raw, -1))) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }

    /**
     * Server self-check for the admin-only /profile/avatar/diagnose page:
     * environment facts plus a real end-to-end run of the compressor on a
     * generated photo, so a failing step is named instead of guessed at.
     */
    public function diagnose(): array
    {
        $dir = $this->directory();
        $gd = function_exists('gd_info') ? gd_info() : [];

        $env = [
            'php_version' => PHP_VERSION,
            'gd_loaded' => extension_loaded('gd'),
            'gd_webp_support' => !empty($gd['WebP Support']),
            'gd_jpeg_support' => !empty($gd['JPEG Support']),
            'gd_png_support' => !empty($gd['PNG Support']),
            'functions' => array_combine(
                $names = ['imagecreatetruecolor', 'imagecopyresampled', 'imagewebp', 'imagejpeg', 'imagecreatefrompng', 'imagecreatefromjpeg', 'exif_read_data'],
                array_map('function_exists', $names)
            ),
            'memory_limit' => ini_get('memory_limit'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'avatar_column_exists' => $this->hasAvatarColumn(),
            'photo_folder' => $dir,
            'photo_folder_exists' => is_dir($dir),
            'photo_folder_writable' => is_dir($dir) ? is_writable($dir) : (is_dir(dirname($dir) ?: '.') && is_writable(dirname($dir) ?: '.')),
            'working_directory' => getcwd(),
        ];

        $test = ['ok' => false];
        $tmp = tempnam(sys_get_temp_dir(), 'avdiag');

        try {
            // A 1600x1200 photo-like image: gradient plus noise (hard to compress).
            $img = imagecreatetruecolor(1600, 1200);
            for ($y = 0; $y < 1200; $y += 2) {
                for ($x = 0; $x < 1600; $x += 2) {
                    $c = imagecolorallocate($img, ($x / 6) % 256, ($y / 5) % 256, ($x + $y + mt_rand(0, 60)) % 256);
                    imagefilledrectangle($img, $x, $y, $x + 1, $y + 1, $c);
                }
            }
            imagepng($img, $tmp, 3);
            unset($img);

            $started = microtime(true);
            $result = $this->optimize($tmp);

            $test = [
                'ok' => true,
                'input_kb' => round(filesize($tmp) / 1024),
                'output_format' => $result['ext'],
                'output_kb' => round(strlen($result['binary']) / 1024, 1),
                'seconds' => round(microtime(true) - $started, 2),
                'peak_memory_mb' => round(memory_get_peak_usage(true) / 1048576, 1),
            ];
        } catch (\Throwable $e) {
            $test = ['ok' => false, 'failed_with' => get_class($e) . ': ' . $e->getMessage()];
        } finally {
            @unlink($tmp);
        }

        return ['environment' => $env, 'compression_self_test' => $test];
    }

    protected function hasAvatarColumn(): bool
    {
        return Schema::hasColumn('users', 'avatar');
    }

    protected function directory(): string
    {
        return User::AVATAR_DIR;
    }

    protected function supportsWebp(): bool
    {
        return function_exists('imagewebp') && !empty(gd_info()['WebP Support']);
    }

    /** JPEG has no transparency, so put transparent areas on white. */
    private function flattenOnWhite($image)
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $flat = imagecreatetruecolor($w, $h);
        imagefill($flat, 0, 0, imagecolorallocate($flat, 255, 255, 255));
        imagealphablending($flat, true);
        imagecopy($flat, $image, 0, 0, 0, 0, $w, $h);

        return $flat;
    }

    private function applyExifOrientation($image, string $path)
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }

        $exif = @exif_read_data($path);
        $angle = match ($exif['Orientation'] ?? 1) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        return $angle ? (imagerotate($image, $angle, 0) ?: $image) : $image;
    }

    private function centreCropSquare($image)
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $side = min($width, $height);

        $square = imagecreatetruecolor($side, $side);
        imagealphablending($square, false);
        imagesavealpha($square, true);
        imagefill($square, 0, 0, imagecolorallocatealpha($square, 0, 0, 0, 127));

        imagecopy($square, $image, 0, 0, intdiv($width - $side, 2), intdiv($height - $side, 2), $side, $side);

        return $square;
    }

    private function deleteFile(?string $name): void
    {
        if ($name) {
            File::delete($this->directory() . '/' . $name);
        }
    }
}
