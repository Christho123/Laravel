<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\Category\CategoryIndexRequest;
use App\Http\Requests\Products\Category\CategoryStoreRequest;
use App\Http\Requests\Products\Category\CategoryUpdateRequest;
use App\Models\Products\Category\Category;
use App\Services\Products\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    public function index(CategoryIndexRequest $request): JsonResponse
    {
        $result = $this->categoryService->paginate($request->validated());

        return response()->json([
            'message' => 'Listado de categorias obtenido correctamente.',
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

    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return response()->json([
            'message' => 'Categoría creada correctamente.',
            'data' => $category,
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'data' => $category,
        ]);
    }

    public function update(CategoryUpdateRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->update($category, $request->validated());

        return response()->json([
            'message' => 'Categoría actualizada correctamente.',
            'data' => $category,
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);

        return response()->json([
            'message' => 'Categoría eliminada correctamente.',
        ]);
    }
}