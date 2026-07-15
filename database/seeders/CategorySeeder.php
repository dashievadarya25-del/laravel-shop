<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Создаем необходимые категории в таблице 'categories'
        $smartphones = Category::firstOrCreate(['slug' => 'smartphones'], ['name' => 'Смартфоны']);
        $laptops = Category::firstOrCreate(['slug' => 'laptops'], ['name' => 'Ноутбуки']);
        $printers = Category::firstOrCreate(['slug' => 'printers'], ['name' => 'Принтеры']);

        // 2. Обновляем уже существующие в базе товары, привязывая их к новым категориям

        // Привязываем смартфоны по их SKU
        Product::whereIn('sku', [
            'MPH-HN-600P', 'MPH-HW-P80', 'MPH-AP-16P', 'MPH-HN-600', 'MPH-HW-PUR', 'MPH-AP-17P'
        ])->update(['category_id' => $smartphones->id]);

        // Привязываем ноутбуки по их SKU
        Product::whereIn('sku', [
            'NB-AP-MBP1', 'NB-AP-MBP2', 'NB-LN-OFFICE'
        ])->update(['category_id' => $laptops->id]);

        // Привязываем принтеры по их SKU
        Product::whereIn('sku', [
            'PRN-KY-4500', 'PRN-KY-500'
        ])->update(['category_id' => $printers->id]);
    }
}
