<?php

namespace App\Repositories\Inventory;

use App\Models\Inventory\StockMovement\StockMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StockMovementRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = StockMovement::query()
            ->with(['product:id,name'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where(function ($builder) use ($search): void {
                $builder->where('type', 'like', '%'.$search.'%')
                    ->orWhere('reference_type', 'like', '%'.$search.'%');
            });
        }

        $pageSize = (int) ($filters['page_size'] ?? 10);
        $page = (int) ($filters['page'] ?? 1);

        return $query->paginate($pageSize, ['*'], 'page', $page)->withQueryString();
    }
}
