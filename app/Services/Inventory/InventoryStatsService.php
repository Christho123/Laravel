<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Purchase\Purchase;
use App\Models\Inventory\Sale\Sale;
use App\Models\Inventory\StockMovement\StockMovement;
use App\Models\Products\Product\Product;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class InventoryStatsService
{
    public function stockInflow(string $range): array
    {
        [$start, $end] = $this->resolveRange($range);

        $byDate = StockMovement::query()
            ->where('type', 'entrada')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('date, COALESCE(SUM(quantity), 0) as total_quantity')
            ->groupBy('date')
            ->pluck('total_quantity', 'date');

        $series = $this->buildSeries($start, $end, static function (string $date) use ($byDate): array {
            return [
                'date' => $date,
                'quantity' => (int) ($byDate[$date] ?? 0),
            ];
        });

        return [
            'range' => $range,
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'summary' => [
                'total_quantity' => (int) $byDate->sum(),
            ],
            'series' => $series,
        ];
    }

    public function purchases(string $range): array
    {
        [$start, $end] = $this->resolveRange($range);

        $byDate = Purchase::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $series = $this->buildSeries($start, $end, static function (string $date) use ($byDate): array {
            return [
                'date' => $date,
                'count' => (int) ($byDate[$date] ?? 0),
            ];
        });

        return [
            'range' => $range,
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'summary' => [
                'total_purchases' => (int) $byDate->sum(),
            ],
            'series' => $series,
        ];
    }

    public function sales(string $range): array
    {
        [$start, $end] = $this->resolveRange($range);

        $byDate = Sale::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $series = $this->buildSeries($start, $end, static function (string $date) use ($byDate): array {
            return [
                'date' => $date,
                'count' => (int) ($byDate[$date] ?? 0),
            ];
        });

        return [
            'range' => $range,
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'summary' => [
                'total_sales' => (int) $byDate->sum(),
            ],
            'series' => $series,
        ];
    }

    public function stockMovements(string $range): array
    {
        [$start, $end] = $this->resolveRange($range);

        $rows = StockMovement::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('date, type, COUNT(*) as total_count, COALESCE(SUM(quantity), 0) as total_quantity')
            ->groupBy('date', 'type')
            ->get();

        $byDate = [];
        $summary = [
            'total_movements' => 0,
            'entries_count' => 0,
            'exits_count' => 0,
            'entries_quantity' => 0,
            'exits_quantity' => 0,
        ];

        foreach ($rows as $row) {
            $date = (string) $row->date;
            $type = (string) $row->type;
            $count = (int) $row->total_count;
            $quantity = (int) $row->total_quantity;

            $byDate[$date] ??= [
                'entries_count' => 0,
                'exits_count' => 0,
                'entries_quantity' => 0,
                'exits_quantity' => 0,
            ];

            if ($type === 'entrada') {
                $byDate[$date]['entries_count'] = $count;
                $byDate[$date]['entries_quantity'] = $quantity;
                $summary['entries_count'] += $count;
                $summary['entries_quantity'] += $quantity;
            } else {
                $byDate[$date]['exits_count'] = $count;
                $byDate[$date]['exits_quantity'] = $quantity;
                $summary['exits_count'] += $count;
                $summary['exits_quantity'] += $quantity;
            }

            $summary['total_movements'] += $count;
        }

        $series = $this->buildSeries($start, $end, static function (string $date) use ($byDate): array {
            return [
                'date' => $date,
                'entries_count' => (int) ($byDate[$date]['entries_count'] ?? 0),
                'exits_count' => (int) ($byDate[$date]['exits_count'] ?? 0),
                'entries_quantity' => (int) ($byDate[$date]['entries_quantity'] ?? 0),
                'exits_quantity' => (int) ($byDate[$date]['exits_quantity'] ?? 0),
            ];
        });

        return [
            'range' => $range,
            'period' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
            'summary' => $summary,
            'series' => $series,
        ];
    }

    public function lowStockAlerts(int $threshold = 15): array
    {
        $products = Product::query()
            ->where('stock', '<', $threshold)
            ->orderBy('stock')
            ->orderBy('name')
            ->get(['id', 'name', 'stock', 'price_purchase', 'price_sale', 'updated_at']);

        return [
            'threshold' => $threshold,
            'total' => $products->count(),
            'items' => $products->map(static function (Product $product): array {
                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'stock' => (int) $product->stock,
                    'price_purchase' => (float) $product->price_purchase,
                    'price_sale' => (float) $product->price_sale,
                    'updated_at' => $product->updated_at?->toISOString(),
                ];
            })->values(),
        ];
    }

    private function resolveRange(string $range): array
    {
        $end = now()->endOfDay();

        $start = match ($range) {
            'daily' => now()->startOfDay(),
            'week' => now()->subDays(6)->startOfDay(),
            'month' => now()->subDays(29)->startOfDay(),
            '3_months' => now()->subDays(89)->startOfDay(),
            '6_months' => now()->subDays(179)->startOfDay(),
            '1_year' => now()->subDays(364)->startOfDay(),
            default => now()->startOfDay(),
        };

        return [$start, $end];
    }

    private function buildSeries(Carbon $start, Carbon $end, callable $mapper): array
    {
        return collect(CarbonPeriod::create($start->copy()->startOfDay(), '1 day', $end->copy()->startOfDay()))
            ->map(static fn (Carbon $date) => $mapper($date->toDateString()))
            ->values()
            ->all();
    }
}
