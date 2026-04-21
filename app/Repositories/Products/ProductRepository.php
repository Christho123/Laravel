<?php

namespace App\Repositories\Products;

use App\Models\Products\Product\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Product::query()
            ->with([
                'brand:id,name',
                'category:id,name',
            ])
            ->orderBy('name');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $pageSize = (int) ($filters['page_size'] ?? 10);
        $page = (int) ($filters['page'] ?? 1);

        return $query->paginate($pageSize, ['*'], 'page', $page)->withQueryString();
    }

    public function create(array $data): Product
    {
        return Product::query()->create($data)->load([
            'brand:id,name',
            'category:id,name',
        ]);
    }

    public function update(Product $product, array $data): Product
    {
        $product->fill($data)->save();

        return $product->refresh()->load([
            'brand:id,name',
            'category:id,name',
        ]);
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}
