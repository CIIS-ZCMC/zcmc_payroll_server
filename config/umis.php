<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UMIS Webhook Receiver
    |--------------------------------------------------------------------------
    |
    | Inbound notification sent by UMIS once a payroll period has finished
    | caching. The event itself is thin; the records are read straight out of
    | UMIS's Redis by App\Services\FetchEmployeeService.
    |
    | The names mirror UMIS's own config/payroll.php so both sides of the
    | integration can be reasoned about together.
    |
    */

    'webhook' => [

        // Shared secret used to verify the HMAC UMIS puts in X-UMIS-Signature.
        // Must be byte-identical to UMIS's PAYROLL_WEBHOOK_SECRET. With this
        // unset the receiver rejects everything rather than trusting a body.
        'secret' => env('UMIS_WEBHOOK_SECRET'),

        // Allowed clock skew, in seconds, between the two servers.
        'tolerance' => (int) env('UMIS_WEBHOOK_TOLERANCE', 300),

        // How long a delivered event_id is remembered so that UMIS's
        // at-least-once retries cannot import the same period twice.
        'dedupe_ttl' => (int) env('UMIS_WEBHOOK_DEDUPE_TTL', 86400),

        // Guard held while a period is importing. Must exceed the longest
        // expected import, or a second event could start one alongside it.
        'lock_ttl' => (int) env('UMIS_WEBHOOK_LOCK_TTL', 1800),

    ],

];
