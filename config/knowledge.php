<?php

// config/knowledge.php
//
// Configuration for the Simple Markdown Retrieval Layer.
// Publish/keep this at: config/knowledge.php

return [

    /*
    |--------------------------------------------------------------------------
    | Knowledge Base Path
    |--------------------------------------------------------------------------
    | Root folder containing your category subfolders (services/, pricing/,
    | faq/, company/, policies/, etc). Each subfolder holds .md files.
    */
    'path' => storage_path('app/knowledge'),

    /*
    |--------------------------------------------------------------------------
    | Max Results
    |--------------------------------------------------------------------------
    | How many top-scoring Markdown documents to inject into the prompt.
    */
    'max_results' => env('KNOWLEDGE_MAX_RESULTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Minimum Score
    |--------------------------------------------------------------------------
    | Documents scoring below this are discarded entirely (prevents noise
    | from being injected when nothing is actually relevant).
    */
    'min_score' => env('KNOWLEDGE_MIN_SCORE', 1),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (minutes)
    |--------------------------------------------------------------------------
    | The parsed index (file contents, titles, headings) is cached under a
    | key derived from a cheap "signature" of the knowledge folder (file
    | paths + mtimes + sizes). If a .md file changes, the signature changes,
    | so a fresh index is built automatically on the next request — no
    | manual cache-clearing needed. This TTL is just a safety net so old
    | signature-keyed entries eventually expire from the cache store.
    */
    'cache_ttl' => env('KNOWLEDGE_CACHE_TTL', 1440), // 24 hours

    /*
    |--------------------------------------------------------------------------
    | Scoring Weights
    |--------------------------------------------------------------------------
    | Tune these to change how much each signal matters.
    */
    'weights' => [
        'filename'           => 10, // keyword found in the filename
        'title'              => 8,  // keyword found in the H1 title
        'heading'            => 5,  // keyword found in any H2-H6 heading
        'category'           => 4,  // keyword found in the folder name
        'keyword_frequency'  => 1,  // per occurrence in the body
        'max_frequency_hits' => 5,  // cap on frequency scoring per keyword
    ],

    /*
    |--------------------------------------------------------------------------
    | Stopwords
    |--------------------------------------------------------------------------
    | Common words stripped out of the user's question before scoring, so
    | they don't dilute relevance matching.
    */
    'stopwords' => [
        'the', 'a', 'an', 'is', 'are', 'do', 'does', 'did', 'how', 'what',
        'when', 'where', 'why', 'who', 'which', 'i', 'you', 'we', 'they',
        'it', 'this', 'that', 'of', 'for', 'to', 'in', 'on', 'with', 'need',
        'want', 'can', 'could', 'would', 'please', 'much', 'many', 'your',
        'my', 'me', 'about', 'and', 'or', 'be', 'have', 'has', 'will',
    ],

];
