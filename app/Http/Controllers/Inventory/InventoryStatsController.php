<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\Stats\InventoryStatsRequest;
use App\Services\Inventory\InventoryStatsService;
use Illuminate\Http\JsonResponse;

class InventoryStatsController extends Controller
{
    public function __construct(private readonly InventoryStatsService $statsService)
    {
    }

    public function stockInflow(InventoryStatsRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Estadisticas de ingreso de stock obtenidas correctamente.',
            'data' => $this->statsService->stockInflow($this->resolveRange($request)),
        ]);
    }

    public function purchases(InventoryStatsRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Estadisticas de compras obtenidas correctamente.',
            'data' => $this->statsService->purchases($this->resolveRange($request)),
        ]);
    }

    public function sales(InventoryStatsRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Estadisticas de ventas obtenidas correctamente.',
            'data' => $this->statsService->sales($this->resolveRange($request)),
        ]);
    }

    public function stockMovements(InventoryStatsRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Estadisticas de movimientos de stock obtenidas correctamente.',
            'data' => $this->statsService->stockMovements($this->resolveRange($request)),
        ]);
    }

    public function lowStockAlerts(InventoryStatsRequest $request): JsonResponse
    {
        $threshold = (int) ($request->validated()['threshold'] ?? 15);

        return response()->json([
            'message' => 'Alertas de stock bajo obtenidas correctamente.',
            'data' => $this->statsService->lowStockAlerts($threshold),
        ]);
    }

    private function resolveRange(InventoryStatsRequest $request): string
    {
        return (string) ($request->validated()['range'] ?? 'daily');
    }
}
