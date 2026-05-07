<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Electronics', 'Food & Beverage', 'Clothing', 'Medicine', 'Stationery', 'Others'];

        foreach ($categories as $name) {
            Category::create(['business_id' => 1, 'name' => $name, 'is_active' => true]);
        }
    }
}
