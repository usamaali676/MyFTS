<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

/**
 * Turns whatever photo a user uploads into a small square WebP avatar:
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
        // getPathname(), not getRealPath(): realpath() returns false on hosts
        // whose upload temp dir is symlinked / behind open_basedir, which
        // surfaced as "Path cannot be empty" even for a perfectly valid upload.
        $binary = $this->optimize($file->getPathname());

        File::makeDirectory(User::AVATAR_DIR, 0775, true, true);

        $name = 'user-' . $user->id . '-' . time() . '.webp';
        File::put(User::AVATAR_DIR . '/' . $name, $binary);

        $this->deleteFile($user->avatar);

        $user->avatar = $name;
        $user->save();

        return $name;
    }

    public function remove(User $user): void
    {
        $this->deleteFile($user->avatar);

        $user->avatar = null;
        $user->save();
    }

    /**
     * @throws InvalidArgumentException with a message safe to show the user
     */
    public function optimize(string $path): string
    {
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

        @ini_set('memory_limit', '512M');

        $source = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => @imagecreatefromwebp($path),
            IMAGETYPE_GIF => @imagecreatefromgif($path),
        };

        if (!$source) {
            throw new InvalidArgumentException('That image could not be read. Please try a different one.');
        }

        if ($info[2] === IMAGETYPE_JPEG) {
            $source = $this->applyExifOrientation($source, $path);
        }

        $square = $this->centreCropSquare($source);

        foreach (self::SIZES as $size) {
            $scaled = imagescale($square, $size, $size, IMG_BICUBIC);

            foreach (self::QUALITIES as $quality) {
                ob_start();
                imagewebp($scaled, null, $quality);
                $binary = ob_get_clean();

                if (strlen($binary) <= self::MAX_BYTES) {
                    return $binary;
                }
            }
        }

        throw new InvalidArgumentException('That image could not be compressed small enough. Please try a different one.');
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
            File::delete(User::AVATAR_DIR . '/' . $name);
        }
    }
}
