<?php

namespace App\Repositories\Inventory;

use App\Models\Inventory\Sale\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SaleRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Sale::query()
            ->with(['details.product:id,name'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where('tipo_comprobante', 'like', '%'.$search.'%');
        }

        $pageSize = (int) ($filters['page_size'] ?? 10);
        $page = (int) ($filters['page'] ?? 1);

        return $query->paginate($pageSize, ['*'], 'page', $page)->withQueryString();
    }

    public function findWithRelations(Sale $sale): Sale
    {
        return $sale->load([
            'details.product:id,name',
        ]);
    }
}
