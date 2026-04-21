<?php

namespace App\Services\Products;

use App\Models\Products\Supplier\Supplier;
use App\Repositories\Products\SupplierRepository;

class SupplierService
{
    public function __construct(private readonly SupplierRepository $supplierRepository)
    {
    }

    public function paginate(array $filters)
    {
        return $this->supplierRepository->paginate($filters);
    }

    public function create(array $data): Supplier
    {
        return $this->supplierRepository->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        return $this->supplierRepository->update($supplier, $data);
    }

    public function delete(Supplier $supplier): void
    {
        $this->supplierRepository->delete($supplier);
    }
}