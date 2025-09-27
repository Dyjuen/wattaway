<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wattaway Smart Socket',
                'description' => 'Smart energy management socket with real-time monitoring, timer controls, and power limit features. Control your devices remotely and optimize energy usage.',
                'price' => 89.99,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
