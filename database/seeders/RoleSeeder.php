<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
/**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = collect(['Administrator', 'Guest', 'Finance Staff', 'Accounting Staff']);

        $roles->each(function ($role) {
            Role::create(['name' => $role]);
        });

        // Super Admin Permissions
        $administratorRole = Role::findByName('Administrator');
        $administratorRole->givePermissionTo('manage-user');

        // Staff Permissions
        $staffPermissions = [
            'manage-payable',
            'manage-receivable',
            'manage-customer',
            'manage-supplier',
            'manage-bank'
        ];

        // Staff Permissions
        foreach (['Finance Staff', 'Accounting Staff'] as $roleName) {
            $staffrRole = Role::findByName($roleName);
            $staffrRole->givePermissionTo($staffPermissions);
        }
    }
}
