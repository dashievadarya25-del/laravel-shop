<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Kyocera PA4500',
            'description' => 'Лазерный принтер',
            'price' => 40299,
        ]);

        Product::create([
            'name' => 'Honor 600 Pro 512Гб',
            'description' => 'Смартфон 8 ядерным процессором',
            'price' => 74999,
        ]);

        Product::create([
            'name' => 'HUAWEI Pura 80',
            'description' => 'Смартфон',
            'price' => 41999,
        ]);
    }
}
