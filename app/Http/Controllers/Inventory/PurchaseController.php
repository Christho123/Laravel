<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\Purchase\PurchaseIndexRequest;
use App\Http\Requests\Inventory\Purchase\PurchaseStoreRequest;
use App\Models\Inventory\Purchase\Purchase;
use App\Services\Inventory\PurchaseService;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    public function __construct(private readonly PurchaseService $purchaseService)
    {
    }

    public function index(PurchaseIndexRequest $request): JsonResponse
    {
        $result = $this->purchaseService->paginate($request->validated());

        return response()->json([
            'message' => 'Listado de compras obtenido correctamente.',
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

    public function store(PurchaseStoreRequest $request): JsonResponse
    {
        $purchase = $this->purchaseService->create($request->validated());

        return response()->json([
            'message' => 'Compra registrada correctamente.',
            'data' => $purchase,
        ], 201);
    }

    public function show(Purchase $purchase): JsonResponse
    {
        return response()->json([
            'data' => $this->purchaseService->find($purchase),
        ]);
    }
}
