<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Пользователь',
            'slug' => Role::ROLE_USER,
        ]);

        Role::create([
            'name' => 'Администратор',
            'slug' => Role::ROLE_ADMIN,
        ]);

        Role::create([
            'name' => 'Менеджер',
            'slug' => Role::ROLE_MANAGER,
        ]);
    }
}
