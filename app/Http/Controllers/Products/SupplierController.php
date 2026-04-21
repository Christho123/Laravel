<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\Supplier\SupplierIndexRequest;
use App\Http\Requests\Products\Supplier\SupplierStoreRequest;
use App\Http\Requests\Products\Supplier\SupplierUpdateRequest;
use App\Models\Products\Supplier\Supplier;
use App\Services\Products\SupplierService;
use Illuminate\Http\JsonResponse;

class SupplierController extends Controller
{
    public function __construct(private readonly SupplierService $supplierService)
    {
    }

    public function index(SupplierIndexRequest $request): JsonResponse
    {
        $result = $this->supplierService->paginate($request->validated());

        return response()->json([
            'message' => 'Listado de proveedores obtenido correctamente.',
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

    public function store(SupplierStoreRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->create($request->validated());

        return response()->json([
            'message' => 'Proveedor creado correctamente.',
            'data' => $supplier,
        ], 201);
    }

    public function show(Supplier $supplier): JsonResponse
    {
        return response()->json([
            'data' => $supplier,
        ]);
    }

    public function update(SupplierUpdateRequest $request, Supplier $supplier): JsonResponse
    {
        $supplier = $this->supplierService->update($supplier, $request->validated());

        return response()->json([
            'message' => 'Proveedor actualizado correctamente.',
            'data' => $supplier,
        ]);
    }

    public function destroy(Supplier $supplier): JsonResponse
    {
        $this->supplierService->delete($supplier);

        return response()->json([
            'message' => 'Proveedor eliminado correctamente.',
        ]);
    }
}