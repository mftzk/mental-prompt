<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerboseLogging
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Record start time to calculate execution duration later
        $startTime = microtime(true);

        // Clone or convert needed request data to avoid stream exhaustion
        $requestData = $request->all();

        // Proceed with the request / get response
        $response = $next($request);

        // Execution time in milliseconds
        $duration = round((microtime(true) - $startTime) * 1000, 2);

        // Log request and response details
        Log::info('HTTP Request', [
            'method'   => $request->method(),
            'uri'      => $request->getPathInfo(),
            'ip'       => $request->ip(),
            'user_id'  => $request->user()?->id,
            'payload'  => $requestData,
        ]);

        Log::info('HTTP Response', [
            'status'       => $response->getStatusCode(),
            'duration_ms'  => $duration,
        ]);

        return $response;
    }
} 