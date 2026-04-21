<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\Sale\SaleIndexRequest;
use App\Http\Requests\Inventory\Sale\SaleStoreRequest;
use App\Models\Inventory\Sale\Sale;
use App\Services\Inventory\SaleService;
use Illuminate\Http\JsonResponse;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $saleService)
    {
    }

    public function index(SaleIndexRequest $request): JsonResponse
    {
        $result = $this->saleService->paginate($request->validated());

        return response()->json([
            'message' => 'Listado de ventas obtenido correctamente.',
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

    public function store(SaleStoreRequest $request): JsonResponse
    {
        $sale = $this->saleService->create($request->validated());

        return response()->json([
            'message' => 'Venta registrada correctamente.',
            'data' => $sale,
        ], 201);
    }

    public function show(Sale $sale): JsonResponse
    {
        return response()->json([
            'data' => $this->saleService->find($sale),
        ]);
    }
}
