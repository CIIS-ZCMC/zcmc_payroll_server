<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authenticates the UMIS "period cached" webhook.
 *
 * This is the only thing standing in front of the receiver route: the call is
 * server-to-server, so there is no session and no token for auth.token to read.
 * Trust comes entirely from the HMAC UMIS computes over the request body with a
 * secret both systems share.
 */
class VerifyUmisWebhookSignature
{
    public function handle(Request $request, Closure $next)
    {
        $secret = config('umis.webhook.secret');

        // Fail closed. An unconfigured secret must never be read as "no
        // signature required" — that would leave the import wide open.
        if (empty($secret)) {
            Log::error('UMIS webhook rejected: UMIS_WEBHOOK_SECRET is not configured.');

            return response()->json([
                'message' => 'Webhook receiver is not configured.',
                'success' => false,
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $timestamp = $request->header('X-UMIS-Timestamp');

        if (!is_numeric($timestamp) || abs(time() - (int) $timestamp) > (int) config('umis.webhook.tolerance', 300)) {
            Log::warning('UMIS webhook rejected: stale or missing timestamp', [
                'timestamp' => $timestamp,
            ]);

            return $this->unauthorized();
        }

        $provided = (string) $request->header('X-UMIS-Signature');

        // UMIS signs the exact bytes it puts on the wire, so the digest has to
        // be taken over the raw body. Re-encoding the parsed array would
        // reorder keys and change the escaping, producing a mismatch.
        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), (string) $secret);

        if (!hash_equals($expected, $provided)) {
            Log::warning('UMIS webhook rejected: signature mismatch', [
                'event_id' => $request->header('X-UMIS-Event-Id'),
                'ip' => $request->ip(),
            ]);

            return $this->unauthorized();
        }

        return $next($request);
    }

    /**
     * Deliberately vague: a caller that cannot sign a body has no business
     * learning which half of the check it failed.
     */
    private function unauthorized()
    {
        return response()->json([
            'message' => 'Invalid webhook signature.',
            'success' => false,
        ], Response::HTTP_UNAUTHORIZED);
    }
}
