<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $cashiers = [1, 2, 3];

        for ($i = 0; $i < 50; $i++) {
            $soldAt  = now()->subDays(rand(0, 30))->subHours(rand(0, 12));
            $items   = $products->random(rand(1, 4));
            $subtotal = 0;

            $sale = Sale::create([
                'uuid'           => Str::uuid(),
                'business_id'    => 1,
                'cashier_id'     => $cashiers[array_rand($cashiers)],
                'invoice_number' => 'INV-' . $soldAt->format('Ymd') . '-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'subtotal'       => 0,
                'tax_amount'     => 0,
                'discount_amount'=> 0,
                'total_amount'   => 0,
                'paid_amount'    => 0,
                'change_amount'  => 0,
                'payment_method' => ['cash', 'card', 'mobile'][rand(0, 2)],
                'status'         => 'completed',
                'sold_at'        => $soldAt,
                'synced_at'      => $soldAt,
            ]);

            foreach ($items as $product) {
                $qty   = rand(1, 3);
                $price = (float) $product->selling_price;
                $total = $qty * $price;
                $subtotal += $total;

                SaleItem::create([
                    'sale_id'      => $sale->id,
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'product_sku'  => $product->sku,
                    'quantity'     => $qty,
                    'unit_price'   => $price,
                    'cost_price'   => (float) $product->cost_price,
                    'discount'     => 0,
                    'tax_amount'   => 0,
                    'total'        => $total,
                ]);
            }

            $sale->update([
                'subtotal'     => $subtotal,
                'total_amount' => $subtotal,
                'paid_amount'  => $subtotal + rand(0, 50),
                'change_amount'=> rand(0, 50),
            ]);
        }
    }
}
