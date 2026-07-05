<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [

            /* ================= CORE ================= */
            'Core Managment' => [
                'access core',
                'access settings',
                'view reports',
            ],

            /* ================= USER & ROLE ================= */
            'Users & Roles' => [
                'manage users',
                'manage roles',
                'manage permissions',
                'list user',
                'add user',
                'edit user',
                'delete user',
            ],

            /* ================= SETTINGS ================= */
            'Settings' => [
                'edit setting',
            ],

            /* ================= ASSET & STORE ================= */
            'Assets & Store' => [
                'asset management',
                'store management',
            ],

            /* ================= INVENTORY ================= */
            'Inventory Managment' => [
                'manage items',
                'categories',
                'subcategories',
                'items',
                'add categories',
                'edit categories',
                'delete categories',
                'add subcategories',
                'edit subcategories',
                'delete subcategories',
                'add items',
                'edit items',
                'delete items',
            ],

            /* ================= MACHINES & COMPONENTS ================= */
            'Machines Management' => [
                'machines',
                'manage machines',
                'add machines',
                'edit machines',
                'delete machines',
            ],

            'Components Managment' => [
                'components',
                'manage components',
                'add components',
                'edit components',
                'delete components',
            ],

            /* ================= CRM ================= */
            'CRM ' => [
                'crm management',
                'leads',
                'manage leads',
                'view lead',
                'edit lead',
                'delete lead',
                'view followups',
                'view followup',
                'add followup',
                'edit followup',
                'delete followup',
                'leads management',
            ],

            /* ================= ORDERS ================= */
            'Orders' => [
                'orders',
                'manage orders',
                'add order',
                'edit order',
                'view order',
                'print order',
                'delete order',
            ],
            'Permissions & Role' => [
                'add permissions',
                'edit permissions',
                'delete permissions',
                'add role',
                'edit role',
                'delete role'
            ],
            /* ================= QUOTATIONS ================= */
            'Quotation' => [
                'quotation',
                'manage quotation',
                'add quotation',
                'edit quotation',
                'delete quotation',
                'duplicate quotation',
                'view quotation',
                'print quotation',
            ],

            /* ================= COMPANY ================= */
            'Company' => [
                'list company',
                'manage company',
                'manage company status',
                'add company',
                'edit company',
                'delete company',
                'assign users',
                'enter company',
            ],

            /* ================= MASTER DATA ================= */
            'Masters' => [
                'units',
                'view units',
                'add units',
                'edit units',
                'delete units',
                'projects',
                'locations',
                'add locations',
                'employees',
                'departments',
                'conditions',
                'add conditions',
                'suppliers',
                'brands',
                'add brands',
            ],
            /* ================= ACTIONS ================= */
            'Actions' => [
                'actions',
                'add action',
                'edit action',
                'delete action',
            ],

            /* ================= STOCK ================= */
            'Stock' => [
                'stockin',
                'issues',
            ],
        ];

        /* ================= CREATE PERMISSIONS ================= */
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate([
                    'name' => $perm,
                    'guard_name' => 'web',
                ], [
                    'group_name' => $group,
                ]);
            }
        }

        /* ================= ROLES ================= */
        $roles = ['Super Admin', 'Admin', 'Staff'];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        /* ================= ASSIGN ALL TO SUPER ADMIN ================= */
        Role::findByName('Super Admin')->syncPermissions(Permission::all());
    }
}
