<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeoIpLookup
{
    /**
     * @return array{city: string|null, country: string|null}
     */
    public function lookup(?string $ipAddress): array
    {
        if (! $ipAddress || ! $this->isPublicIp($ipAddress)) {
            return $this->emptyResult();
        }

        $databasePath = (string) config('tracking.geoip_database_path', '');

        if ($databasePath === '' || ! is_file($databasePath)) {
            return $this->emptyResult();
        }

        try {
            $reader = new Reader($databasePath, config('tracking.geoip_locales', ['en']));
            $record = $reader->city($ipAddress);

            return [
                'city' => $record->city->name,
                'country' => $record->country->isoCode,
            ];
        } catch (AddressNotFoundException) {
            return $this->emptyResult();
        } catch (Throwable $exception) {
            Log::warning('GeoIP lookup failed.', [
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            return $this->emptyResult();
        }
    }

    private function isPublicIp(string $ipAddress): bool
    {
        return filter_var(
            $ipAddress,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE,
        ) !== false;
    }

    /**
     * @return array{city: string|null, country: string|null}
     */
    private function emptyResult(): array
    {
        return [
            'city' => null,
            'country' => null,
        ];
    }
}
