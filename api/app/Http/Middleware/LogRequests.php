<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequests
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
        $requestId = uniqid();

        Log::info('REQUEST_START [' . $requestId . ']', [
            'method' => $request->method(),
            'url' => $request->url(),
            'full_url' => $request->fullUrl(),
            'route_name' => $request->route() ? $request->route()->getName() : 'no_route',
            'route_uri' => $request->route() ? $request->route()->uri() : 'no_uri',
            'content_type' => $request->header('Content-Type'),
            'user_agent' => $request->header('User-Agent'),
            'referer' => $request->header('Referer'),
            'request_id' => $requestId,
            'timestamp' => now()->toDateTimeString()
        ]);

        $response = $next($request);

        Log::info('REQUEST_END [' . $requestId . ']', [
            'status_code' => $response->getStatusCode(),
            'request_id' => $requestId,
            'timestamp' => now()->toDateTimeString()
        ]);

        return $response;
    }
}
