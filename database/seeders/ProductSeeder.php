<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Samsung Galaxy A14', 'sku' => 'EL-001', 'category_id' => 1, 'cost' => 15000, 'price' => 18000, 'stock' => 25],
            ['name' => 'USB-C Charger 65W',  'sku' => 'EL-002', 'category_id' => 1, 'cost' => 500,   'price' => 800,   'stock' => 50],
            ['name' => 'Wireless Earbuds',   'sku' => 'EL-003', 'category_id' => 1, 'cost' => 800,   'price' => 1200,  'stock' => 30],
            ['name' => 'Power Bank 10000mAh','sku' => 'EL-004', 'category_id' => 1, 'cost' => 600,   'price' => 950,   'stock' => 20],
            ['name' => 'Phone Case',         'sku' => 'EL-005', 'category_id' => 1, 'cost' => 80,    'price' => 150,   'stock' => 3],
            ['name' => 'Mineral Water 1L',   'sku' => 'FB-001', 'category_id' => 2, 'cost' => 15,    'price' => 25,    'stock' => 200],
            ['name' => 'Orange Juice 250ml', 'sku' => 'FB-002', 'category_id' => 2, 'cost' => 25,    'price' => 40,    'stock' => 100],
            ['name' => 'Chocolate Bar',      'sku' => 'FB-003', 'category_id' => 2, 'cost' => 30,    'price' => 50,    'stock' => 80],
            ['name' => 'Coffee 3in1',        'sku' => 'FB-004', 'category_id' => 2, 'cost' => 10,    'price' => 18,    'stock' => 150],
            ['name' => 'Energy Drink',       'sku' => 'FB-005', 'category_id' => 2, 'cost' => 45,    'price' => 70,    'stock' => 4],
            ['name' => 'T-Shirt (M)',        'sku' => 'CL-001', 'category_id' => 3, 'cost' => 200,   'price' => 400,   'stock' => 35],
            ['name' => 'Jeans (32)',         'sku' => 'CL-002', 'category_id' => 3, 'cost' => 500,   'price' => 900,   'stock' => 20],
            ['name' => 'Polo Shirt',         'sku' => 'CL-003', 'category_id' => 3, 'cost' => 300,   'price' => 550,   'stock' => 2],
            ['name' => 'Paracetamol 500mg',  'sku' => 'MD-001', 'category_id' => 4, 'cost' => 5,     'price' => 8,     'stock' => 500],
            ['name' => 'Vitamin C 1000mg',   'sku' => 'MD-002', 'category_id' => 4, 'cost' => 80,    'price' => 130,   'stock' => 60],
            ['name' => 'Antacid Tablet',     'sku' => 'MD-003', 'category_id' => 4, 'cost' => 15,    'price' => 25,    'stock' => 3],
            ['name' => 'A4 Paper (Ream)',    'sku' => 'ST-001', 'category_id' => 5, 'cost' => 280,   'price' => 400,   'stock' => 40],
            ['name' => 'Ball Pen (Blue)',    'sku' => 'ST-002', 'category_id' => 5, 'cost' => 5,     'price' => 10,    'stock' => 300],
            ['name' => 'Notebook A5',        'sku' => 'ST-003', 'category_id' => 5, 'cost' => 40,    'price' => 70,    'stock' => 80],
            ['name' => 'Stapler',            'sku' => 'ST-004', 'category_id' => 5, 'cost' => 80,    'price' => 140,   'stock' => 4],
        ];

        foreach ($products as $data) {
            $product = Product::create([
                'uuid'           => Str::uuid(),
                'business_id'    => 1,
                'category_id'    => $data['category_id'],
                'name'           => $data['name'],
                'sku'            => $data['sku'],
                'cost_price'     => $data['cost'],
                'selling_price'  => $data['price'],
                'tax_rate'       => 0,
                'unit'           => 'pcs',
                'is_active'      => true,
                'track_inventory'=> true,
                'alert_quantity' => 5,
            ]);

            StockMovement::create([
                'product_id'      => $product->id,
                'business_id'     => 1,
                'type'            => 'in',
                'quantity'        => $data['stock'],
                'quantity_before' => 0,
                'quantity_after'  => $data['stock'],
                'note'            => 'Initial stock',
                'created_by'      => 1,
            ]);
        }
    }
}
