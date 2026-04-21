<?php

namespace App\Repositories\Products;

use App\Models\Products\Supplier\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SupplierRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Supplier::query()->orderBy('name');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%'.trim($filters['search']).'%');
        }

        $pageSize = (int) ($filters['page_size'] ?? 10);
        $page = (int) ($filters['page'] ?? 1);

        return $query->paginate($pageSize, ['*'], 'page', $page)->withQueryString();
    }

    public function create(array $data): Supplier
    {
        return Supplier::query()->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->fill($data)->save();

        return $supplier->refresh();
    }

    public function delete(Supplier $supplier): void
    {
        $supplier->delete();
    }
}