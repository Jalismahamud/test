<?php

namespace App\Services\POS;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Services\Inventory\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleService
{
    public function __construct(private StockService $stockService) {}

    public function processSale(array $data, User $cashier): Sale
    {
        return DB::transaction(function () use ($data, $cashier) {
            $sale = Sale::create([
                'uuid'            => $data['uuid'] ?? Str::uuid()->toString(),
                'business_id'     => $cashier->business_id,
                'cashier_id'      => $cashier->id,
                'customer_id'     => $data['customer_id'] ?? null,
                'invoice_number'  => $this->generateInvoiceNumber($cashier->business_id),
                'subtotal'        => $data['subtotal'],
                'tax_amount'      => $data['tax_amount'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'total_amount'    => $data['total_amount'],
                'paid_amount'     => $data['paid_amount'],
                'change_amount'   => $data['change_amount'] ?? 0,
                'payment_method'  => $data['payment_method'],
                'status'          => 'completed',
                'note'            => $data['note'] ?? null,
                'sold_at'         => $data['sold_at'] ?? now(),
                'synced_at'       => isset($data['uuid']) ? now() : null,
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::withoutBusinessScope()->lockForUpdate()->findOrFail($item['product_id']);

                $this->stockService->deductStock($product, (float) $item['quantity'], $sale->id, $cashier->id);

                SaleItem::create([
                    'sale_id'      => $sale->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'cost_price'   => $product->cost_price,
                    'discount'     => $item['discount'] ?? 0,
                    'tax_amount'   => $item['tax_amount'] ?? 0,
                    'total'        => $item['total'],
                ]);
            }

            return $sale->load('items', 'cashier');
        });
    }

    public function refundSale(Sale $sale, User $by): Sale
    {
        if ($sale->status === 'refunded') {
            throw new \RuntimeException('Sale already refunded.');
        }

        return DB::transaction(function () use ($sale, $by) {
            $sale->update(['status' => 'refunded']);
            $this->stockService->reverseForRefund($sale, $by->id);
            return $sale->fresh('items');
        });
    }

    private function generateInvoiceNumber(int $businessId): string
    {
        $count = Sale::withoutBusinessScope()
            ->where('business_id', $businessId)
            ->lockForUpdate()
            ->count() + 1;

        return 'INV-'.date('Ymd').'-'.str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
