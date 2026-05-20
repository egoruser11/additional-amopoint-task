<?php

namespace Database\Seeders;

use App\Models\PageVisit;
use App\Services\VisitSecurity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PageVisitSeeder extends Seeder
{
    /**
     * Seed demo page visits for the statistics dashboard.
     */
    public function run(): void
    {
        $security = app(VisitSecurity::class);
        $cities = [
            ['Moscow', 'RU'],
            ['Saint Petersburg', 'RU'],
            ['Kazan', 'RU'],
            ['Yekaterinburg', 'RU'],
            ['Unknown', null],
        ];
        $devices = ['desktop', 'mobile', 'tablet'];
        $visitors = collect(range(1, 36))->map(fn (int $index): string => (string) Str::uuid().'-'.$index);

        foreach (range(0, 47) as $hourOffset) {
            $visitsInHour = random_int(2, 8);

            foreach (range(1, $visitsInHour) as $visitIndex) {
                [$city, $country] = $cities[array_rand($cities)];
                $visitor = $visitors->random();
                $ipAddress = '10.0.'.random_int(0, 10).'.'.random_int(2, 250);

                PageVisit::query()->create([
                    'visitor_hash' => $security->fingerprint($visitor),
                    'ip_address' => $ipAddress,
                    'ip_hash' => $security->fingerprint($ipAddress),
                    'city' => $city,
                    'country' => $country,
                    'device_type' => $devices[array_rand($devices)],
                    'browser' => 'Seeder Browser',
                    'platform' => 'Seeder Platform',
                    'screen_width' => random_int(1280, 1920),
                    'screen_height' => random_int(720, 1080),
                    'language' => 'ru-RU',
                    'timezone' => 'Europe/Moscow',
                    'user_agent_hash' => $security->fingerprint('Seeder Browser '.$visitIndex),
                    'page_url' => 'https://example.test/articles/'.$visitIndex,
                    'referrer' => 'https://example.test',
                    'occurred_at' => Carbon::now()->subHours($hourOffset)->subMinutes(random_int(0, 59)),
                ]);
            }
        }
    }
}
