<?php

namespace App\Services\Products;

use App\Models\Products\Product\Product;
use App\Repositories\Products\ProductRepository;

class ProductService
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    public function paginate(array $filters)
    {
        return $this->productRepository->paginate($filters);
    }

    public function create(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        return $this->productRepository->update($product, $data);
    }

    public function delete(Product $product): void
    {
        $this->productRepository->delete($product);
    }
}
