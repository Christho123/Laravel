<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StockMovement\StockMovementIndexRequest;
use App\Services\Inventory\StockMovementService;
use Illuminate\Http\JsonResponse;

class StockMovementController extends Controller
{
    public function __construct(private readonly StockMovementService $stockMovementService)
    {
    }

    public function index(StockMovementIndexRequest $request): JsonResponse
    {
        $result = $this->stockMovementService->paginate($request->validated());

        return response()->json([
            'message' => 'Listado de movimientos de stock obtenido correctamente.',
            'data' => $result->items(),
            'meta' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'page_size' => $result->perPage(),
                'total' => $result->total(),
                'from' => $result->firstItem(),
                'to' => $result->lastItem(),
            ],
        ]);
    }
}
