<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TrackerScriptController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        return response()->file(public_path('tracker.js'), [
            'Cache-Control' => 'public, max-age=300',
            'Content-Type' => 'application/javascript; charset=UTF-8',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
