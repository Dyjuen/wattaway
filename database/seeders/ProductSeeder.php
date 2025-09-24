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
                'name' => 'Smart Energy Monitor',
                'description' => 'Monitor your home energy consumption in real-time',
                'price' => 99.99,
            ],
            [
                'name' => 'Solar Panel Kit',
                'description' => 'Complete solar panel installation kit for residential use',
                'price' => 299.99,
            ],
            [
                'name' => 'Energy Storage Battery',
                'description' => 'High-capacity battery for energy storage and backup',
                'price' => 199.99,
            ],
            [
                'name' => 'Smart Thermostat',
                'description' => 'AI-powered thermostat for optimal energy efficiency',
                'price' => 149.99,
            ],
            [
                'name' => 'LED Lighting Package',
                'description' => 'Complete home LED lighting upgrade package',
                'price' => 79.99,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
