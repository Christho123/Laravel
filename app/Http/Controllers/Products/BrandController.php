<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\Brand\BrandIndexRequest;
use App\Http\Requests\Products\Brand\BrandStoreRequest;
use App\Http\Requests\Products\Brand\BrandUpdateRequest;
use App\Models\Products\Brand\Brand;
use App\Services\Products\BrandService;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    public function __construct(private readonly BrandService $brandService)
    {
    }

    public function index(BrandIndexRequest $request): JsonResponse
    {
        $result = $this->brandService->paginate($request->validated());

        return response()->json([
            'message' => 'Listado de marcas obtenido correctamente.',
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

    public function store(BrandStoreRequest $request): JsonResponse
    {
        $brand = $this->brandService->create($request->validated());

        return response()->json([
            'message' => 'Marca creada correctamente.',
            'data' => $brand,
        ], 201);
    }

    public function show(Brand $brand): JsonResponse
    {
        return response()->json([
            'data' => $brand,
        ]);
    }

    public function update(BrandUpdateRequest $request, Brand $brand): JsonResponse
    {
        $brand = $this->brandService->update($brand, $request->validated());

        return response()->json([
            'message' => 'Marca actualizada correctamente.',
            'data' => $brand,
        ]);
    }

    public function destroy(Brand $brand): JsonResponse
    {
        $this->brandService->delete($brand);

        return response()->json([
            'message' => 'Marca eliminada correctamente.',
        ]);
    }
}