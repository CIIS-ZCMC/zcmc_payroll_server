<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class StripTags
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $cleanData = [];

        foreach ($request->all() as $key => $value) {
            if (empty($value)) {
                $cleanData[$key] = $value;
                continue;
            }

            if (is_int($value)) {
                $cleanData[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                $cleanData[$key] = $value;
                continue;
            }

            if (strtotime($value)) {
                $datetime = Carbon::parse($value);
                $cleanData[$key] = $datetime->format('Y-m-d'); // Adjust the format as needed
                continue;
            }


            $cleanData[$key] = strip_tags($value);
        }

        $request->merge($cleanData);

        return $next($request);
    }
}
