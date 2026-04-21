<?php

namespace App\Services\Products;

use App\Models\Products\Brand\Brand;
use App\Repositories\Products\BrandRepository;

class BrandService
{
    public function __construct(private readonly BrandRepository $brandRepository)
    {
    }

    public function paginate(array $filters)
    {
        return $this->brandRepository->paginate($filters);
    }

    public function create(array $data): Brand
    {
        return $this->brandRepository->create($data);
    }

    public function update(Brand $brand, array $data): Brand
    {
        return $this->brandRepository->update($brand, $data);
    }

    public function delete(Brand $brand): void
    {
        $this->brandRepository->delete($brand);
    }
}