<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'Administrator',
            'type' => 'backend',
            'guard_name' => 'web',
        ]);

        Role::create([
            'name' => 'User',
            'type' => 'frontend',
            'guard_name' => 'web', 
        ]);
    }
}
