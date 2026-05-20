<?php

namespace Tests\Feature;

use App\Models\PageVisit;
use App\Models\User;
use App\Services\GeoIpLookup;
use App\Services\VisitSecurity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthAndStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_login_issues_sanctum_token(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('StrongPass123!'),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'StrongPass123!',
            'device_name' => 'tests',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'token_type',
                'access_token',
                'expires_at',
                'user' => ['id', 'name', 'email'],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_statistics_requires_authentication(): void
    {
        $this->getJson('/api/statistics/summary')->assertUnauthorized();
    }

    public function test_visit_store_hashes_identifiers_and_strips_url_query(): void
    {
        $this->app->instance(GeoIpLookup::class, new class extends GeoIpLookup
        {
            public function lookup(?string $ipAddress): array
            {
                return [
                    'city' => 'Mountain View',
                    'country' => 'US',
                ];
            }
        });

        $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])->postJson('/api/visits', [
            'visitor_id' => 'browser-visitor-id',
            'site_host' => 'example.test',
            'page_title' => 'Secret page',
            'page_url' => 'https://example.test/page?token=secret#fragment',
            'referrer' => 'https://example.test/ref?session=secret',
            'city' => 'Moscow',
            'country' => 'ru',
            'device_type' => 'desktop',
            'browser' => 'Test Browser',
            'platform' => 'MacIntel',
            'screen_width' => 1440,
            'screen_height' => 900,
            'language' => 'ru-RU',
            'timezone' => 'Europe/Moscow',
        ])->assertCreated();

        $visit = PageVisit::query()->firstOrFail();

        $this->assertSame('https://example.test/page', $visit->page_url);
        $this->assertSame('https://example.test/ref', $visit->referrer);
        $this->assertSame('example.test', $visit->site_host);
        $this->assertSame('Secret page', $visit->page_title);
        $this->assertSame('Mountain View', $visit->city);
        $this->assertSame('US', $visit->country);
        $this->assertNotSame('browser-visitor-id', $visit->visitor_hash);
    }

    public function test_statistics_summary_returns_hourly_and_city_aggregates(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-20 12:45:00', 'UTC'));

        $user = User::factory()->create();
        Sanctum::actingAs($user, ['statistics:read']);

        $security = app(VisitSecurity::class);
        $hour = Carbon::now('UTC')->startOfHour();

        foreach ([
            ['visitor-1', 'Moscow', $hour->copy()->addMinutes(5)],
            ['visitor-1', 'Moscow', $hour->copy()->addMinutes(15)],
            ['visitor-2', 'Kazan', $hour->copy()->addMinutes(30)],
        ] as [$visitor, $city, $occurredAt]) {
            PageVisit::query()->create([
                'visitor_hash' => $security->fingerprint($visitor),
                'ip_address' => '127.0.0.1',
                'ip_hash' => $security->fingerprint('127.0.0.1'),
                'city' => $city,
                'device_type' => 'desktop',
                'occurred_at' => $occurredAt,
            ]);
        }

        $response = $this->getJson('/api/statistics/summary?days=1&timezone=UTC')
            ->assertOk()
            ->assertJsonPath('data.totals.page_views', 3)
            ->assertJsonPath('data.totals.unique_visitors', 2);

        $currentHour = collect($response->json('data.hourly'))
            ->firstWhere('hour', $hour->toIso8601String());

        $this->assertSame(2, $currentHour['unique_visits']);
        $this->assertSame(3, $currentHour['page_views']);
        $this->assertSame('Moscow', $response->json('data.cities.0.city'));
    }
}
