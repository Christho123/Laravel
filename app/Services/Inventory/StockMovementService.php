<?php

namespace App\Services\Inventory;

use App\Repositories\Inventory\StockMovementRepository;

class StockMovementService
{
    public function __construct(private readonly StockMovementRepository $stockMovementRepository)
    {
    }

    public function paginate(array $filters)
    {
        return $this->stockMovementRepository->paginate($filters);
    }
}
