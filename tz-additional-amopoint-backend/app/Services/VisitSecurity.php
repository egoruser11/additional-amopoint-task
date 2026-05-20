<?php

namespace App\Services;

use Illuminate\Support\Str;

class VisitSecurity
{
    public function fingerprint(string $value): string
    {
        return hash_hmac('sha256', $value, $this->secret());
    }

    public function normalizeText(?string $value, int $maxLength = 120, string $fallback = 'Unknown'): string
    {
        $normalized = Str::of((string) $value)
            ->stripTags()
            ->squish()
            ->limit($maxLength, '')
            ->toString();

        return $normalized === '' ? $fallback : $normalized;
    }

    public function sanitizeUrl(?string $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        $parts = parse_url($value);

        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $scheme = strtolower($parts['scheme']);

        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower((string) $parts['host']);
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = $parts['path'] ?? '/';

        return Str::limit($scheme.'://'.$host.$port.$path, 2048, '');
    }

    private function secret(): string
    {
        $key = config('app.key');

        return is_string($key) && $key !== '' ? $key : 'local-development-key';
    }
}
