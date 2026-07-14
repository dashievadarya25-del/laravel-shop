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
            'sku' => 'PRN-KY-4500', // Добавлено
        ]);

        Product::create([
            'name' => 'Honor 600 Pro 512Гб',
            'description' => 'Смартфон 8 ядерным процессором',
            'price' => 74999,
            'sku' => 'MPH-HN-600P', // Добавлено
        ]);

        Product::create([
            'name' => 'HUAWEI Pura 80',
            'description' => 'Смартфон',
            'price' => 41999,
            'sku' => 'MPH-HW-P80', // Добавлено
        ]);

        Product::create([
            'name' => 'iPhone 16 Pro',
            'description' => 'Флагманский смартфон Apple',
            'price' => 120000,
            'sku' => 'MPH-AP-16P', // Добавлено
        ]);

        Product::create([
            'name' => 'MacBook Pro',
            'description' => 'Мощный ноутбук для разработчиков',
            'price' => 250000,
            'sku' => 'NB-AP-MBP1', // Добавлено
        ]);

        Product::create([
            'name' => 'Kyocera PA500',
            'description' => 'Лазерный принтер',
            'price' => 20299,
            'sku' => 'PRN-KY-500', // Добавлено
        ]);

        Product::create([
            'name' => 'Honor 600',
            'description' => 'Смартфон 8 ядерным процессором',
            'price' => 40999,
            'sku' => 'MPH-HN-600', // Добавлено
        ]);

        Product::create([
            'name' => 'HUAWEI Pura',
            'description' => 'Смартфон',
            'price' => 20999,
            'sku' => 'MPH-HW-PUR', // Добавлено
        ]);

        Product::create([
            'name' => 'iPhone 17 Pro',
            'description' => 'Флагманский смартфон Apple',
            'price' => 130000,
            'sku' => 'MPH-AP-17P', // Добавлено
        ]);

        Product::create([
            'name' => 'MacBook Pro',
            'description' => 'Мощный ноутбук для разработчиков',
            'price' => 250000,
            'sku' => 'NB-AP-MBP2', // Добавлено (сделали уникальным)
        ]);

        Product::create([
            'name' => 'Lenovo',
            'description' => 'Ноутбук офисный',
            'price' => 60000,
            'sku' => 'NB-LN-OFFICE', // Добавлено
        ]);
    }
}
