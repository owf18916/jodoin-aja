<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = collect([
            'manage-payable',
            'manage-receivable',
            'manage-customer',
            'manage-supplier',
            'manage-bank',
            'manage-user'
        ]);

        $permissions->each(function ($permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        });
    }
}
