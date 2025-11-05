<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if client_uuid exists in session
        if (!session()->has('client_uuid')) {
            return redirect()->route('login')->with('error', 'Please login to access the dashboard.');
        }

        $clientUuid = session('client_uuid');

        // Validate the UUID exists and is active
        $client = Client::where('uuid', $clientUuid)
                       ->where('is_active', true)
                       ->first();

        if (!$client) {
            // UUID is invalid or inactive, clear session and redirect
            session()->forget('client_uuid');
            return redirect()->route('login')->with('error', 'Your session is invalid or has been deactivated.');
        }

        // Store client in request for controller access
        $request->attributes->set('client', $client);

        return $next($request);
    }
}

