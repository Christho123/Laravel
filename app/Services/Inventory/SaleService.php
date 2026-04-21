<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Sale\Sale;
use App\Models\Inventory\Sale\SaleDetail;
use App\Models\Inventory\StockMovement\StockMovement;
use App\Models\Products\Product\Product;
use App\Repositories\Inventory\SaleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function __construct(private readonly SaleRepository $saleRepository)
    {
    }

    public function paginate(array $filters)
    {
        return $this->saleRepository->paginate($filters);
    }

    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data): Sale {
            $items = $data['items'];
            $date = $data['date'] ?? now()->toDateString();

            $sale = Sale::query()->create([
                'date' => $date,
                'tipo_comprobante' => $data['tipo_comprobante'],
                'total' => 0,
            ]);

            $total = 0.0;

            foreach ($items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Stock insuficiente para el producto {$product->name}."],
                    ]);
                }

                $lineSubtotal = round($quantity * $unitPrice, 2);

                SaleDetail::query()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                ]);

                $product->decrement('stock', $quantity);

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'salida',
                    'quantity' => $quantity,
                    'reference_type' => 'sale',
                    'reference_id' => $sale->id,
                    'date' => $date,
                ]);

                $total += $lineSubtotal;
            }

            $sale->fill([
                'total' => round($total, 2),
            ])->save();

            return $this->saleRepository->findWithRelations($sale);
        });
    }

    public function find(Sale $sale): Sale
    {
        return $this->saleRepository->findWithRelations($sale);
    }
}
