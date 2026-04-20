<?php

namespace App\Services\Products;

use App\Models\Products\Category\Category;
use App\Repositories\Products\CategoryRepository;

class CategoryService
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {
    }

    public function paginate(array $filters)
    {
        return $this->categoryRepository->paginate($filters);
    }

    public function create(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        return $this->categoryRepository->update($category, $data);
    }

    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }
}
