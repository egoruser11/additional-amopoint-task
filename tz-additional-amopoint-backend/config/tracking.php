<?php

$geoIpDatabasePath = env('GEOIP_DATABASE_PATH');

if (is_string($geoIpDatabasePath) && $geoIpDatabasePath !== '' && ! str_starts_with($geoIpDatabasePath, '/')) {
    $geoIpDatabasePath = base_path($geoIpDatabasePath);
}

return [
    'geoip_database_path' => $geoIpDatabasePath ?: storage_path('app/geoip/GeoLite2-City.mmdb'),
    'geoip_locales' => array_filter(array_map('trim', explode(',', env('GEOIP_LOCALES', 'en,ru')))),
];
