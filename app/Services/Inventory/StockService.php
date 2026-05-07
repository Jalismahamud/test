<?php

namespace App\Services\Inventory;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function getLevel(int $productId): float
    {
        return (float) StockMovement::where('product_id', $productId)
            ->selectRaw('COALESCE(SUM(CASE WHEN type = "in" THEN quantity ELSE -quantity END), 0) as total')
            ->value('total');
    }

    public function deductStock(Product $product, float $qty, int $saleId, int $userId): void
    {
        $current = $this->getLevel($product->id);

        if ($product->track_inventory && $current < $qty) {
            throw new InsufficientStockException($product->name);
        }

        StockMovement::create([
            'product_id'      => $product->id,
            'business_id'     => $product->business_id,
            'reference_id'    => $saleId,
            'reference_type'  => Sale::class,
            'type'            => 'out',
            'quantity'        => $qty,
            'quantity_before' => $current,
            'quantity_after'  => $current - $qty,
            'created_by'      => $userId,
        ]);
    }

    public function addStock(Product $product, float $qty, string $note, int $userId): StockMovement
    {
        $current = $this->getLevel($product->id);

        return StockMovement::create([
            'product_id'      => $product->id,
            'business_id'     => $product->business_id,
            'type'            => 'in',
            'quantity'        => $qty,
            'quantity_before' => $current,
            'quantity_after'  => $current + $qty,
            'note'            => $note,
            'created_by'      => $userId,
        ]);
    }

    public function reverseForRefund(Sale $sale, int $userId): void
    {
        foreach ($sale->items as $item) {
            $product = Product::withoutBusinessScope()->find($item->product_id);
            if ($product) {
                $this->addStock($product, (float) $item->quantity, 'Refund: '.$sale->invoice_number, $userId);
            }
        }
    }
}
