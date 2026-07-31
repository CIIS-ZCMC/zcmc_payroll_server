<?php

namespace App\Http\Controllers\UMIS;

use App\Http\Controllers\Controller;
use App\Http\Requests\UmisWebhookRequest;
use App\Services\FetchEmployeeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Receives UMIS's "a payroll period has finished caching" event.
 *
 * Nothing is fetched over HTTP here. UMIS writes the period into a Redis
 * database this server already reads, so the event is only a signal to run the
 * import FetchEmployeeService performs — the same one the daily
 * command:fetch-employee-time-records runs on a schedule.
 */
class UmisWebhookController extends Controller
{
    /**
     * The only event this receiver acts on. Anything else is acknowledged and
     * dropped rather than retried.
     */
    private const EVENT_PERIOD_CACHED = 'employee_time_records.period_cached';

    public function __construct(private FetchEmployeeService $service)
    {
        // Nothing
    }

    public function store(UmisWebhookRequest $request)
    {
        $event = $request->input('event');
        $eventId = $request->input('event_id');
        $period = $request->input('period');

        $context = ['event' => $event, 'event_id' => $eventId, 'period' => $period];

        // A future UMIS event type must not look like a delivery failure, or it
        // would be retried five times before landing in UMIS's failed_jobs.
        if ($event !== self::EVENT_PERIOD_CACHED) {
            Log::info('UMIS webhook ignored: unhandled event type', $context);

            return response()->json([
                'message' => 'Event ignored.',
                'status' => 'ignored',
                'success' => true,
            ], Response::HTTP_OK);
        }

        // Delivery is at-least-once and UMIS mints event_id precisely so the
        // receiver can tell a retry from a genuinely new build.
        $firstDelivery = Cache::add(
            'umis:webhook:' . $eventId,
            true,
            (int) config('umis.webhook.dedupe_ttl', 86400)
        );

        if (!$firstDelivery) {
            Log::info('UMIS webhook ignored: duplicate delivery', $context);

            return response()->json([
                'message' => 'Event already processed.',
                'status' => 'duplicate',
                'success' => true,
            ], Response::HTTP_OK);
        }

        $year = (int) $period['year'];
        $month = (int) $period['month'];
        $employmentType = $period['employment_type'];
        $periodType = $period['period_type'];

        // The import walks every employee and writes salaries, time records and
        // computed salaries, which takes far longer than the 15 seconds UMIS
        // waits for a response. Running it after the response keeps UMIS from
        // timing out and retrying on top of an import that is already underway.
        dispatch(function () use ($year, $month, $employmentType, $periodType, $eventId, $context) {
            $lockKey = "umis:import:{$year}-{$month}:{$employmentType}:{$periodType}";

            // Cache::add rather than Cache::lock: it behaves the same on every
            // cache driver, and this only needs to stop two events for one
            // period from importing at once.
            if (!Cache::add($lockKey, true, (int) config('umis.webhook.lock_ttl', 1800))) {
                Log::warning('UMIS webhook import skipped: period already importing', $context);

                return;
            }

            try {
                $result = $this->service->getEmployeesForPeriod($year, $month, $employmentType, $periodType);

                if ($result === null) {
                    // Signature checked out but the cache was empty: almost
                    // always UMIS_CACHE_PREFIX drifting from UMIS's cache
                    // version rather than a genuinely missing period.
                    Log::warning('UMIS webhook import found no cached data', $context);

                    return;
                }

                Log::info('UMIS webhook import completed', $context + ['employee_count' => count($result)]);
            } catch (\Throwable $th) {
                // The response is long gone, so this log is the only place the
                // failure can surface. Recover with
                // command:fetch-employee-time-records for this period.
                Log::error('UMIS webhook import failed: ' . $th->getMessage(), $context);
            } finally {
                Cache::forget($lockKey);
            }
        })->afterResponse();

        Log::info('UMIS webhook accepted', $context);

        return response()->json([
            'message' => 'Event accepted. Import running.',
            'status' => 'queued',
            'event_id' => $eventId,
            'success' => true,
        ], Response::HTTP_ACCEPTED);
    }
}
