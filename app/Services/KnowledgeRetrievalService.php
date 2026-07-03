<?php

namespace App\Services;

use App\Services\Knowledge\MarkdownDocument;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * KnowledgeRetrievalService
 *
 * A local, non-AI, non-vector "simple retrieval" layer. It scans
 * storage/app/knowledge/**\/*.md, caches the parsed contents, scores
 * files against a user's question using plain keyword heuristics
 * (filename / title / heading / category / frequency matches), and
 * returns the top N most relevant documents.
 *
 * No embeddings. No vector DB. No external calls. Just PHP + Cache.
 */
class KnowledgeRetrievalService
{
    /** Cache key prefix. The full key includes a content signature so
     *  the cache auto-invalidates whenever any .md file changes. */
    private const CACHE_KEY_PREFIX = 'knowledge_index:';

    /**
     * Retrieve the top-N most relevant Markdown documents for a query.
     *
     * @param  string    $query  The raw user question/message.
     * @param  int|null  $limit  Override for max_results config.
     * @return Collection<int, MarkdownDocument>
     */
    public function retrieve(string $query, ?int $limit = null): Collection
    {
        $keywords = $this->extractKeywords($query);

        if (empty($keywords)) {
            return collect();
        }

        $limit = $limit ?? config('knowledge.max_results', 5);
        $minScore = config('knowledge.min_score', 1);

        return $this->getIndex()
            ->each(function (MarkdownDocument $doc) use ($keywords) {
                $doc->score = $this->scoreDocument($doc, $keywords);
            })
            ->filter(fn (MarkdownDocument $doc) => $doc->score >= $minScore)
            ->sortByDesc(fn (MarkdownDocument $doc) => $doc->score)
            ->take($limit)
            ->values();
    }

    /**
     * Build the plain-text KNOWLEDGE block to inject into the system
     * prompt from a collection of retrieved documents.
     */
    public function buildKnowledgeContext(Collection $documents): string
    {
        if ($documents->isEmpty()) {
            return 'No specific knowledge base articles matched this question. '
                . 'If you are not confident about the answer, say you do not know '
                . 'and suggest the user contact FirmTech directly.';
        }

        return $documents
            ->map(function (MarkdownDocument $doc) {
                $title = $doc->title ?? Str::headline($doc->filename);

                return "### {$title}\n(Source: {$doc->path})\n\n{$doc->content}";
            })
            ->implode("\n\n---\n\n");
    }

    /**
     * Force-drop the currently cached index for the current signature.
     * Not required for normal operation (the signature-based key already
     * auto-invalidates on file changes) — useful for an artisan command
     * or manual debugging.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_PREFIX . $this->computeSignature());
    }

    /*
    |--------------------------------------------------------------------------
    | Index building + caching
    |--------------------------------------------------------------------------
    */

    /**
     * Get the cached, parsed index of all knowledge documents. Rebuilds
     * automatically (once) whenever any .md file is added, edited, or
     * removed, because the cache key is derived from a signature of the
     * folder contents.
     */
    protected function getIndex(): Collection
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $this->computeSignature();
        $ttl = now()->addMinutes(config('knowledge.cache_ttl', 1440));

        return Cache::remember($cacheKey, $ttl, fn () => $this->buildIndex());
    }

    /**
     * Cheap signature of the knowledge folder: file path + mtime + size
     * for every .md file, hashed together. This only performs filesystem
     * *stat* calls (via Symfony Finder / SplFileInfo), never reads file
     * contents, so it's safe to run on every request even with the cache
     * cold-checking logic.
     */
    protected function computeSignature(): string
    {
        $path = config('knowledge.path');

        if (! File::isDirectory($path)) {
            return 'empty';
        }

        $parts = collect(File::allFiles($path))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->map(fn ($file) => $file->getRelativePathname() . ':' . $file->getMTime() . ':' . $file->getSize())
            ->sort()
            ->implode('|');

        return md5($parts);
    }

    /**
     * Actually read + parse every Markdown file. Only runs when the
     * signature-keyed cache entry is missing/expired.
     */
    protected function buildIndex(): Collection
    {
        $path = config('knowledge.path');

        if (! File::isDirectory($path)) {
            return collect();
        }

        return collect(File::allFiles($path))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->map(fn ($file) => $this->parseFile($file))
            ->values();
    }

    protected function parseFile(\Symfony\Component\Finder\SplFileInfo $file): MarkdownDocument
    {
        $content = $file->getContents();
        $relativePath = $file->getRelativePathname();

        // Top-level folder name (services / pricing / faq / company / policies).
        // Falls back to "general" for files placed directly in the knowledge root.
        $category = Str::contains($relativePath, DIRECTORY_SEPARATOR)
            ? Str::before($relativePath, DIRECTORY_SEPARATOR)
            : 'general';

        return new MarkdownDocument(
            path: $relativePath,
            category: $category,
            filename: $file->getFilenameWithoutExtension(),
            title: $this->extractTitle($content),
            headings: $this->extractHeadings($content),
            content: trim($content),
        );
    }

    /** First H1 (# ...) in the document, if any. */
    protected function extractTitle(string $content): ?string
    {
        if (preg_match('/^#\s+(.+)$/m', $content, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /** All H2-H6 headings in the document. */
    protected function extractHeadings(string $content): array
    {
        preg_match_all('/^#{2,6}\s+(.+)$/m', $content, $matches);

        return array_map('trim', $matches[1] ?? []);
    }

    /*
    |--------------------------------------------------------------------------
    | Scoring
    |--------------------------------------------------------------------------
    */

    /**
     * Break the user's question into lowercase, stopword-free keywords.
     */
    protected function extractKeywords(string $query): array
    {
        $stopwords = config('knowledge.stopwords', []);

        $words = Str::of($query)
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]/', ' ') // strip punctuation
            ->squish()
            ->explode(' ');

        return $words
            ->filter(fn ($word) => strlen($word) > 2 && ! in_array($word, $stopwords, true))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Score a single document against the extracted keywords.
     */
    protected function scoreDocument(MarkdownDocument $doc, array $keywords): int
    {
        $weights = config('knowledge.weights');

        $filenameHaystack = Str::lower(str_replace(['-', '_'], ' ', $doc->filename));
        $titleHaystack = Str::lower($doc->title ?? '');
        $headingsHaystack = Str::lower(implode(' ', $doc->headings));
        $categoryHaystack = Str::lower($doc->category);
        $bodyHaystack = Str::lower($doc->content);

        $score = 0;

        foreach ($keywords as $keyword) {
            if (Str::contains($filenameHaystack, $keyword)) {
                $score += $weights['filename'];
            }

            if ($titleHaystack !== '' && Str::contains($titleHaystack, $keyword)) {
                $score += $weights['title'];
            }

            if ($headingsHaystack !== '' && Str::contains($headingsHaystack, $keyword)) {
                $score += $weights['heading'];
            }

            if (Str::contains($categoryHaystack, $keyword)) {
                $score += $weights['category'];
            }

            $frequency = substr_count($bodyHaystack, $keyword);
            if ($frequency > 0) {
                $score += min($frequency, $weights['max_frequency_hits']) * $weights['keyword_frequency'];
            }
        }

        return $score;
    }
}
