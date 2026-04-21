<?php

namespace App\Repositories\Inventory;

use App\Models\Inventory\Purchase\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PurchaseRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Purchase::query()
            ->with(['supplier:id,name'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->whereHas('supplier', function ($builder) use ($search): void {
                $builder->where('name', 'like', '%'.$search.'%');
            });
        }

        $pageSize = (int) ($filters['page_size'] ?? 10);
        $page = (int) ($filters['page'] ?? 1);

        return $query->paginate($pageSize, ['*'], 'page', $page)->withQueryString();
    }

    public function findWithRelations(Purchase $purchase): Purchase
    {
        return $purchase->load([
            'supplier:id,name',
            'details.product:id,name',
        ]);
    }
}
