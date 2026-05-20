<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageVisit;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class StatisticController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'timezone' => ['nullable', 'timezone'],
            'days' => ['nullable', 'integer', 'min:1', 'max:90'],
            'device_type' => ['nullable', 'string', Rule::in(['desktop', 'mobile', 'tablet', 'bot', 'unknown'])],
        ]);

        $timezone = $validated['timezone'] ?? config('app.timezone', 'UTC');
        $days = (int) ($validated['days'] ?? 1);
        $to = isset($validated['to'])
            ? CarbonImmutable::parse($validated['to'], $timezone)->endOfHour()
            : now($timezone)->toImmutable();
        $from = isset($validated['from'])
            ? CarbonImmutable::parse($validated['from'], $timezone)->startOfHour()
            : $to->subDays($days)->startOfHour();

        if ($from->diffInDays($to) > 90) {
            $from = $to->subDays(90)->startOfHour();
        }

        $query = PageVisit::query()
            ->whereBetween('occurred_at', [$from->utc(), $to->utc()])
            ->when(
                isset($validated['device_type']),
                fn ($query) => $query->where('device_type', $validated['device_type']),
            )
            ->orderBy('occurred_at')
            ->get(['visitor_hash', 'city', 'device_type', 'occurred_at']);

        return response()->json([
            'data' => [
                'period' => [
                    'from' => $from->toIso8601String(),
                    'to' => $to->toIso8601String(),
                    'timezone' => $timezone,
                ],
                'totals' => [
                    'page_views' => $query->count(),
                    'unique_visitors' => $query->pluck('visitor_hash')->unique()->count(),
                    'cities' => $query->pluck('city')->unique()->count(),
                ],
                'hourly' => $this->hourly($query, $from, $to, $timezone),
                'cities' => $this->cities($query),
            ],
        ])->header('Cache-Control', 'no-store');
    }

    /**
     * @param  Collection<int, PageVisit>  $visits
     * @return array<int, array<string, mixed>>
     */
    private function hourly(Collection $visits, CarbonImmutable $from, CarbonImmutable $to, string $timezone): array
    {
        $grouped = $visits->groupBy(function (PageVisit $visit) use ($timezone): string {
            return $visit->occurred_at->setTimezone($timezone)->startOfHour()->toIso8601String();
        });

        $points = [];
        $cursor = $from->startOfHour();

        while ($cursor <= $to) {
            $key = $cursor->toIso8601String();
            $hourVisits = $grouped->get($key, collect());

            $points[] = [
                'hour' => $key,
                'unique_visits' => $hourVisits->pluck('visitor_hash')->unique()->count(),
                'page_views' => $hourVisits->count(),
            ];

            $cursor = $cursor->addHour();
        }

        return $points;
    }

    /**
     * @param  Collection<int, PageVisit>  $visits
     * @return array<int, array<string, mixed>>
     */
    private function cities(Collection $visits): array
    {
        $total = max($visits->count(), 1);

        return $visits
            ->groupBy('city')
            ->map(fn (Collection $items, string $city): array => [
                'city' => $city,
                'page_views' => $items->count(),
                'unique_visits' => $items->pluck('visitor_hash')->unique()->count(),
                'percentage' => round($items->count() / $total * 100, 2),
            ])
            ->sortByDesc('page_views')
            ->values()
            ->all();
    }
}
