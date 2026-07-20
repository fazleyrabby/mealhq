<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ─── CMS Permissions ───────────────────────────────────────
        $cmsPermissions = [
            'cms.pages.view',
            'cms.pages.create',
            'cms.pages.update',
            'cms.pages.delete',
            'cms.promotions.view',
            'cms.promotions.manage',
            'cms.gallery.manage',
            'cms.faqs.manage',
            'cms.inquiries.view',
            'cms.inquiries.reply',
        ];

        // ─── Order Permissions ─────────────────────────────────────
        $orderPermissions = [
            'orders.view',
            'orders.create',
            'orders.update',
            'orders.cancel',
            'orders.refund',
        ];

        // ─── Menu Permissions ──────────────────────────────────────
        $menuPermissions = [
            'menu.view',
            'menu.manage',
            'menu.availability.toggle',
        ];

        // ─── Inventory Permissions ─────────────────────────────────
        $inventoryPermissions = [
            'inventory.view',
            'inventory.manage',
            'inventory.adjust',
            'inventory.reports',
        ];

        // ─── KDS Permissions ───────────────────────────────────────
        $kdsPermissions = [
            'kds.view',
            'kds.bump',
            'kds.recall',
            'kds.stations.manage',
            'kds.routing.manage',
        ];

        // ─── Table Permissions ─────────────────────────────────────
        $tablePermissions = [
            'tables.view',
            'tables.session.manage',
            'tables.clean',
            'tables.layout.manage',
        ];

        // ─── POS Permissions ───────────────────────────────────────
        $posPermissions = [
            'pos.open',
            'pos.process',
            'pos.close',
            'pos.reports',
        ];

        // ─── Settings Permissions ──────────────────────────────────
        $settingsPermissions = [
            'settings.view',
            'settings.manage',
            'settings.business-hours',
            'settings.taxes',
        ];

        // ─── Customer Permissions ──────────────────────────────────
        $customerPermissions = [
            'customers.view',
            'customers.manage',
        ];

        // ─── Report Permissions ────────────────────────────────────
        $reportPermissions = [
            'reports.view',
            'reports.sales',
            'reports.inventory',
            'reports.expenses',
        ];

        // Create all permissions
        $allPermissions = array_merge(
            $cmsPermissions,
            $orderPermissions,
            $menuPermissions,
            $inventoryPermissions,
            $kdsPermissions,
            $tablePermissions,
            $posPermissions,
            $settingsPermissions,
            $customerPermissions,
            $reportPermissions,
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ─── Create Roles & Assign Permissions ─────────────────────

        // Owner — full system access
        $owner = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $owner->syncPermissions($allPermissions);

        // Manager — administrative access except system ownership settings
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'cms.pages.view',
            'cms.pages.update',
            'cms.promotions.view',
            'cms.promotions.manage',
            'cms.gallery.manage',
            'cms.faqs.manage',
            'cms.inquiries.view',
            'cms.inquiries.reply',
            'orders.view',
            'orders.create',
            'orders.update',
            'orders.cancel',
            'menu.view',
            'menu.manage',
            'menu.availability.toggle',
            'inventory.view',
            'inventory.manage',
            'inventory.adjust',
            'inventory.reports',
            'kds.view',
            'kds.bump',
            'kds.recall',
            'kds.stations.manage',
            'kds.routing.manage',
            'tables.view',
            'tables.session.manage',
            'tables.clean',
            'tables.layout.manage',
            'pos.open',
            'pos.process',
            'pos.close',
            'pos.reports',
            'settings.view',
            'settings.manage',
            'settings.business-hours',
            'settings.taxes',
            'customers.view',
            'customers.manage',
            'reports.view',
            'reports.sales',
            'reports.inventory',
            'reports.expenses',
        ]);

        // Cashier — POS, orders, menu view
        $cashier = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashier->syncPermissions([
            'cms.promotions.view',
            'orders.view',
            'orders.create',
            'menu.view',
            'menu.availability.toggle',
            'tables.view',
            'tables.session.manage',
            'pos.open',
            'pos.process',
            'pos.close',
            'customers.view',
        ]);

        // Waiter — tables, orders
        $waiter = Role::firstOrCreate(['name' => 'Waiter', 'guard_name' => 'web']);
        $waiter->syncPermissions([
            'orders.view',
            'orders.create',
            'menu.view',
            'tables.view',
            'tables.session.manage',
            'tables.clean',
            'customers.view',
        ]);

        // Chef — KDS, menu view
        $chef = Role::firstOrCreate(['name' => 'Chef', 'guard_name' => 'web']);
        $chef->syncPermissions([
            'menu.view',
            'kds.view',
            'kds.bump',
            'kds.recall',
            'kds.routing.manage',
        ]);

        // Kitchen Staff — KDS view and bump only
        $kitchenStaff = Role::firstOrCreate(['name' => 'Kitchen Staff', 'guard_name' => 'web']);
        $kitchenStaff->syncPermissions([
            'kds.view',
            'kds.bump',
        ]);

        // Inventory Manager — inventory, reports
        $inventoryManager = Role::firstOrCreate(['name' => 'Inventory Manager', 'guard_name' => 'web']);
        $inventoryManager->syncPermissions([
            'inventory.view',
            'inventory.manage',
            'inventory.adjust',
            'inventory.reports',
            'reports.view',
            'reports.inventory',
            'orders.view',
            'menu.view',
        ]);
    }
}
