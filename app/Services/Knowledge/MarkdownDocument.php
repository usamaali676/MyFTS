<?php

namespace App\Services\Knowledge;

/**
 * Value object representing a single parsed Markdown knowledge file.
 *
 * This is intentionally a plain DTO (no Eloquent, no framework coupling)
 * so it's cheap to create thousands of instances when scoring, and cheap
 * to cache as part of the index Collection.
 */
class MarkdownDocument
{
    /**
     * Relevance score assigned during retrieval. Mutable because it is
     * computed fresh per-query against an otherwise static document.
     */
    public int $score = 0;

    public function __construct(
        /** Relative path from the knowledge root, e.g. "services/seo.md" */
        public readonly string $path,

        /** Top-level folder name, e.g. "services", "pricing", "faq" */
        public readonly string $category,

        /** Filename without extension, e.g. "seo" */
        public readonly string $filename,

        /** First H1 (# Heading) found in the file, if any */
        public readonly ?string $title,

        /** All H2-H6 headings found in the file */
        public readonly array $headings,

        /** Full raw Markdown content of the file */
        public readonly string $content,
    ) {}
}
