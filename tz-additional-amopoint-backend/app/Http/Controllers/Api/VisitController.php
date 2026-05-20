<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVisitRequest;
use App\Models\PageVisit;
use App\Services\GeoIpLookup;
use App\Services\VisitSecurity;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class VisitController extends Controller
{
    public function store(StoreVisitRequest $request, VisitSecurity $security, GeoIpLookup $geoIp): JsonResponse
    {
        $validated = $request->validated();
        $ipAddress = $request->ip() ?? '0.0.0.0';
        $userAgent = (string) $request->userAgent();
        $visitorSource = $validated['visitor_id'] ?? $ipAddress.'|'.$userAgent;
        $location = $geoIp->lookup($ipAddress);

        $visit = PageVisit::query()->create([
            'visitor_hash' => $security->fingerprint($visitorSource),
            'ip_address' => $ipAddress,
            'ip_hash' => $security->fingerprint($ipAddress),
            'city' => $security->normalizeText($location['city'] ?? $validated['city'] ?? null),
            'country' => $location['country'] ?? $validated['country'] ?? null,
            'device_type' => $validated['device_type'] ?? 'unknown',
            'browser' => isset($validated['browser']) ? $security->normalizeText($validated['browser'], 120, '') : null,
            'platform' => isset($validated['platform']) ? $security->normalizeText($validated['platform'], 120, '') : null,
            'screen_width' => $validated['screen_width'] ?? null,
            'screen_height' => $validated['screen_height'] ?? null,
            'language' => isset($validated['language']) ? Str::limit($validated['language'], 20, '') : null,
            'timezone' => isset($validated['timezone']) ? Str::limit($validated['timezone'], 80, '') : null,
            'user_agent_hash' => $userAgent !== '' ? $security->fingerprint($userAgent) : null,
            'site_host' => isset($validated['site_host']) ? $security->normalizeText($validated['site_host'], 255, '') : null,
            'page_title' => isset($validated['page_title']) ? $security->normalizeText($validated['page_title'], 255, '') : null,
            'page_url' => $security->sanitizeUrl($validated['page_url'] ?? null),
            'referrer' => $security->sanitizeUrl($validated['referrer'] ?? null),
            'occurred_at' => now(),
        ]);

        return response()->json([
            'data' => [
                'id' => $visit->id,
                'accepted' => true,
            ],
        ], 201)->header('Cache-Control', 'no-store');
    }
}
