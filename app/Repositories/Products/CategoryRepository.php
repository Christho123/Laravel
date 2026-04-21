<?php

namespace App\Repositories\Products;

use App\Models\Products\Category\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Category::query()->orderBy('name');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where('name', 'like', '%'.$search.'%');
        }

        $pageSize = (int) ($filters['page_size'] ?? 10);
        $page = (int) ($filters['page'] ?? 1);

        return $query->paginate($pageSize, ['*'], 'page', $page)->withQueryString();
    }

    public function create(array $data): Category
    {
        return Category::query()->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->fill($data)->save();

        return $category->refresh();
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}