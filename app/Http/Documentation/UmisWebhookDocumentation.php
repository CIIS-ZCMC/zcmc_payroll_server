<?php

namespace App\Http\Documentation;

/**
 * @OA\Tag(name="UMIS Webhook", description="Inbound notifications from UMIS")
 */
class UmisWebhookDocumentation
{
    /**
     * @OA\Post(
     *     path="/api/umis/time-records-cached",
     *     summary="Receive UMIS 'payroll period cached' notification",
     *     description="Called by UMIS (not by a browser) once a payroll period has finished
     *         caching. There is no bearer token: the request is authenticated by an HMAC-SHA256
     *         signature over the raw request body, computed with the secret shared between the two
     *         systems (UMIS PAYROLL_WEBHOOK_SECRET = Payroll UMIS_WEBHOOK_SECRET).
     *         Responds immediately; the import runs after the response is sent, so a 202 means the
     *         event was accepted, not that the records have landed. Delivery is at-least-once, so
     *         a repeated event_id is answered 200 'duplicate' and imported only once.",
     *     tags={"UMIS Webhook"},
     *     @OA\Parameter(
     *         name="X-UMIS-Signature",
     *         in="header",
     *         required=true,
     *         description="sha256=<hex HMAC-SHA256 of the raw request body, keyed with the shared secret>",
     *         @OA\Schema(type="string", example="sha256=6f1c…9ab2")
     *     ),
     *     @OA\Parameter(
     *         name="X-UMIS-Timestamp",
     *         in="header",
     *         required=true,
     *         description="Unix timestamp of the delivery. Rejected when it differs from this
     *             server's clock by more than umis.webhook.tolerance (default 300s).",
     *         @OA\Schema(type="integer", example=1761004800)
     *     ),
     *     @OA\Parameter(
     *         name="X-UMIS-Event-Id",
     *         in="header",
     *         required=false,
     *         description="Mirrors event_id in the body. Logged on rejection; not otherwise used.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Sent verbatim by UMIS. Extra fields (occurred_at, source, employee_count,
     *             pull) are accepted and ignored — Payroll reads the records from UMIS's Redis
     *             rather than from pull.endpoint.",
     *         @OA\JsonContent(
     *             required={"event", "event_id", "period"},
     *             @OA\Property(
     *                 property="event",
     *                 type="string",
     *                 example="employee_time_records.period_cached",
     *                 description="Only this value triggers an import; any other is acknowledged and ignored."
     *             ),
     *             @OA\Property(property="event_id", type="string", format="uuid", example="9f2c1e7a-4b6d-4f0a-9d3e-1c5b8a7e2f10"),
     *             @OA\Property(
     *                 property="period",
     *                 type="object",
     *                 required={"year", "month", "employment_type", "period_type"},
     *                 @OA\Property(property="year", type="integer", minimum=2020, maximum=2030, example=2025),
     *                 @OA\Property(property="month", type="integer", minimum=1, maximum=12, example=10),
     *                 @OA\Property(property="employment_type", type="string", enum={"regular", "job_order"}, example="regular"),
     *                 @OA\Property(property="period_type", type="string", enum={"first_half", "second_half"}, example="first_half")
     *             ),
     *             @OA\Property(property="employee_count", type="integer", example=1420),
     *             @OA\Property(property="occurred_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Accepted. The import has been scheduled to run after this response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Event accepted. Import running."),
     *             @OA\Property(property="status", type="string", example="queued"),
     *             @OA\Property(property="event_id", type="string", example="9f2c1e7a-4b6d-4f0a-9d3e-1c5b8a7e2f10"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Acknowledged without importing — either a replay of an event_id already
     *             seen (status 'duplicate') or an event type this receiver does not handle
     *             (status 'ignored'). Deliberately 2xx so UMIS does not retry.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Event already processed."),
     *             @OA\Property(property="status", type="string", enum={"duplicate", "ignored"}, example="duplicate"),
     *             @OA\Property(property="success", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Signature missing or mismatched, or timestamp outside the tolerance window.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid webhook signature."),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=422, description="Signature valid but the payload failed validation."),
     *     @OA\Response(
     *         response=503,
     *         description="UMIS_WEBHOOK_SECRET is not configured on this server, so no delivery can be verified.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Webhook receiver is not configured."),
     *             @OA\Property(property="success", type="boolean", example=false)
     *         )
     *     )
     * )
     */
    public function store() {}
}
