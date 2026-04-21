<?php

namespace App\Repositories\Products;

use App\Models\Products\Brand\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BrandRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Brand::query()->orderBy('name');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%'.trim($filters['search']).'%');
        }

        $pageSize = (int) ($filters['page_size'] ?? 10);
        $page = (int) ($filters['page'] ?? 1);

        return $query->paginate($pageSize, ['*'], 'page', $page)->withQueryString();
    }

    public function create(array $data): Brand
    {
        return Brand::query()->create($data);
    }

    public function update(Brand $brand, array $data): Brand
    {
        $brand->fill($data)->save();

        return $brand->refresh();
    }

    public function delete(Brand $brand): void
    {
        $brand->delete();
    }
}