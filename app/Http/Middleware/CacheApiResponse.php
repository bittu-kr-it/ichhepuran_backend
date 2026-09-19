<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

// Caches whole GET responses on disk (see CACHE_STORE=file), keyed by full
// URL. This host's MySQL rejects new connections under load with
// "Operation not permitted" — almost certainly a per-account connection/
// process cap, not a MySQL-side limit — and the frontend's static export
// build fires the same handful of endpoints (settings, per-page
// section-headings, seo-settings) repeatedly across ~23 pages. Serving
// repeat hits from disk instead of opening a fresh PDO connection each
// time is what actually fixes that, not more retries.
class CacheApiResponse
{
    public function handle(Request $request, Closure $next, int $seconds = 60): Response
    {
        if (! $request->isMethod('GET')) {
            return $next($request);
        }

        $key = 'api_response:'.sha1($request->fullUrl());

        $cached = Cache::get($key);
        if ($cached) {
            return response($cached['content'], $cached['status'])
                ->header('Content-Type', $cached['content_type'])
                ->header('X-Api-Cache', 'HIT');
        }

        $response = $next($request);

        if ($response->getStatusCode() < 400) {
            Cache::put($key, [
                'content' => $response->getContent(),
                'status' => $response->getStatusCode(),
                'content_type' => $response->headers->get('Content-Type'),
            ], $seconds);
        }

        return $response;
    }
}
