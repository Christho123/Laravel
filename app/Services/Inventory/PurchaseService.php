<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Purchase\Purchase;
use App\Models\Inventory\Purchase\PurchaseDetail;
use App\Models\Inventory\Sale\Sale;
use App\Models\Inventory\Sale\SaleDetail;
use App\Models\Inventory\StockMovement\StockMovement;
use App\Models\Products\Product\Product;
use App\Repositories\Inventory\PurchaseRepository;
use App\Repositories\Inventory\SaleRepository;
use App\Repositories\Inventory\StockMovementRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    public function __construct(private readonly PurchaseRepository $purchaseRepository)
    {
    }

    public function paginate(array $filters)
    {
        return $this->purchaseRepository->paginate($filters);
    }

    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data): Purchase {
            $items = $data['items'];
            $date = $data['date'] ?? now()->toDateString();

            $purchase = Purchase::query()->create([
                'supplier_id' => $data['supplier_id'],
                'date' => $date,
                'subtotal' => 0,
                'igv' => 0,
                'total' => 0,
            ]);

            $subtotal = 0.0;

            foreach ($items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $lineSubtotal = round($quantity * $unitPrice, 2);

                PurchaseDetail::query()->create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                ]);

                $product->increment('stock', $quantity);
                $product->forceFill(['price_purchase' => $unitPrice])->save();

                StockMovement::query()->create([
                    'product_id' => $product->id,
                    'type' => 'entrada',
                    'quantity' => $quantity,
                    'reference_type' => 'purchase',
                    'reference_id' => $purchase->id,
                    'date' => $date,
                ]);

                $subtotal += $lineSubtotal;
            }

            $igv = round($subtotal * 0.18, 2);
            $total = round($subtotal + $igv, 2);

            $purchase->fill([
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total,
            ])->save();

            return $this->purchaseRepository->findWithRelations($purchase);
        });
    }

    public function find(Purchase $purchase): Purchase
    {
        return $this->purchaseRepository->findWithRelations($purchase);
    }
}
