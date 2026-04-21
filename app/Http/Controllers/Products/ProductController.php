<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\Product\ProductIndexRequest;
use App\Http\Requests\Products\Product\ProductStoreRequest;
use App\Http\Requests\Products\Product\ProductUpdateRequest;
use App\Models\Products\Product\Product;
use App\Services\Products\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(ProductIndexRequest $request): JsonResponse
    {
        $result = $this->productService->paginate($request->validated());

        return response()->json([
            'message' => 'Listado de productos obtenido correctamente.',
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

    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return response()->json([
            'message' => 'Producto creado correctamente.',
            'data' => $product,
        ], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'data' => $product->load([
                'brand:id,name',
                'category:id,name',
            ]),
        ]);
    }

    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->update($product, $request->validated());

        return response()->json([
            'message' => 'Producto actualizado correctamente.',
            'data' => $product,
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return response()->json([
            'message' => 'Producto eliminado correctamente.',
        ]);
    }
}
