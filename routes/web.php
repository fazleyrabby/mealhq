<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CmsBannerController;
use App\Http\Controllers\Admin\CmsFaqController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\CmsPromotionController;
use App\Http\Controllers\Admin\ContactInquiryController as AdminContactInquiryController;
use App\Http\Controllers\Admin\IngredientController;
use App\Http\Controllers\Admin\KdsStationController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\ModifierGroupController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PosDrawerController;
use App\Http\Controllers\Admin\PosOrderController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RestaurantTableController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StockAdjustmentController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactInquiryController;
use App\Http\Controllers\PublicWebsiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicWebsiteController::class, 'home'])->name('home');
Route::get('/menu', [PublicWebsiteController::class, 'menu'])->name('public.menu');
Route::get('/about', [PublicWebsiteController::class, 'about'])->name('public.about');
Route::get('/contact', [PublicWebsiteController::class, 'contact'])->name('public.contact');
Route::post('/contact', [ContactInquiryController::class, 'store'])->name('public.contact.store');

/*
|--------------------------------------------------------------------------
| Public website (informational frontend — no customer auth)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Admin Panel (admin guard)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Admin Guest (not logged in)
    Route::middleware('guest.admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'store']);
        Route::get('/demo-login/admin', [LoginController::class, 'demo'])
            ->defaults('role', 'admin')
            ->name('demo.login');
    });

    // Admin Authenticated
    Route::middleware(['auth', 'admin'])->group(function () {

        // POS
        Route::get('pos', [PosOrderController::class, 'index'])->name('pos.index');
        Route::post('pos/order', [PosOrderController::class, 'store'])->name('pos.store');
        Route::put('pos/order/{order}', [PosOrderController::class, 'update'])->name('pos.update');
        Route::get('pos/order/{order}/receipt', [PosOrderController::class, 'receipt'])->name('pos.receipt');
        Route::get('pos/order/{order}', [PosOrderController::class, 'show'])->name('pos.show');
        Route::get('pos/search', [PosOrderController::class, 'searchProducts'])->name('pos.search');
        Route::get('pos/customers', [PosOrderController::class, 'searchCustomers'])->name('pos.customers');
        Route::get('pos/today', [PosOrderController::class, 'todayOrders'])->name('pos.today');
        Route::get('pos/cart', [PosOrderController::class, 'getCart'])->name('pos.cart');
        Route::post('pos/cart/sync', [PosOrderController::class, 'syncCart'])->name('pos.cart.sync');
        Route::post('pos/cart/clear', [PosOrderController::class, 'clearCart'])->name('pos.cart.clear');
        Route::get('pos/recent', [PosOrderController::class, 'recentOrders'])->name('pos.recent');
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/', fn () => redirect()->route('admin.dashboard'));

        // Reports & Analytics
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingsController::class, 'update']);
        Route::post('/settings/hours', [SettingsController::class, 'updateHours'])->name('settings.hours');
        Route::post('/settings/tax-rates', [SettingsController::class, 'storeTaxRate'])->name('settings.tax-rates');

        // CMS Pages
        Route::resource('cms/pages', CmsPageController::class)
            ->names('cms.pages')->parameters(['page' => 'page']);

        // CMS Promotions
        Route::resource('cms/promotions', CmsPromotionController::class)
            ->names('cms.promotions')->parameters(['promotion' => 'promotion']);

        // CMS FAQs
        Route::resource('cms/faqs', CmsFaqController::class)
            ->names('cms.faqs')->parameters(['faq' => 'faq']);

        // CMS Banners / Slider
        Route::resource('cms/banners', CmsBannerController::class)
            ->names('cms.banners')->parameters(['banner' => 'banner']);
        Route::delete('cms/banners/{banner}/image', [CmsBannerController::class, 'deleteImageAction'])
            ->name('cms.banners.image.delete');

        // CMS Contact Inquiries
        Route::get('cms/inquiries', [AdminContactInquiryController::class, 'index'])->name('cms.inquiries.index');
        Route::get('cms/inquiries/{inquiry}', [AdminContactInquiryController::class, 'show'])->name('cms.inquiries.show');
        Route::post('cms/inquiries/{inquiry}/read', [AdminContactInquiryController::class, 'markRead'])->name('cms.inquiries.read');
        Route::post('cms/inquiries/{inquiry}/replied', [AdminContactInquiryController::class, 'markReplied'])->name('cms.inquiries.replied');
        Route::delete('cms/inquiries/{inquiry}', [AdminContactInquiryController::class, 'destroy'])->name('cms.inquiries.destroy');

        // Gallery
        Route::get('cms/gallery', fn () => view('admin.cms.gallery.index'))->name('cms.gallery.index');

        // Menu Categories
        Route::resource('menu/categories', CategoryController::class)
            ->names('menu.categories')->parameters(['category' => 'category']);

        // Menu Items
        Route::resource('menu/items', MenuItemController::class)
            ->names('menu.items')->parameters(['item' => 'item']);
        Route::delete('menu/items/{item}/image', [MenuItemController::class, 'deleteImageAction'])->name('menu.items.image.delete');
        Route::get('menu/items/{item}/variants', [MenuItemController::class, 'variants'])->name('menu.items.variants');
        Route::post('menu/items/{item}/variants', [MenuItemController::class, 'storeVariant'])->name('menu.items.variants.store');
        Route::post('menu/items/{item}/toggle-home-offer', [MenuItemController::class, 'toggleHomeOffer'])->name('menu.items.toggle-home-offer');

        // Special Offers
        Route::get('offers', [OfferController::class, 'index'])->name('offers.index');

        // Staff / Users
        Route::resource('users', UserController::class)
            ->names('users')->parameters(['user' => 'user']);

        // Roles & Permissions
        Route::resource('roles', RoleController::class)
            ->names('roles')->parameters(['role' => 'role']);

        // Modifier Groups
        Route::resource('menu/modifiers', ModifierGroupController::class)
            ->names('menu.modifiers')->parameters(['modifierGroup' => 'modifier_group']);
        Route::post('menu/modifiers/{modifierGroup}/items', [ModifierGroupController::class, 'storeItem'])
            ->name('menu.modifiers.items.store');

        // Inventory - Ingredients
        Route::resource('inventory/ingredients', IngredientController::class)
            ->names('inventory.ingredients')->parameters(['ingredient' => 'ingredient']);

        // Inventory - Recipes
        Route::resource('inventory/recipes', RecipeController::class)
            ->names('inventory.recipes')->parameters(['recipe' => 'recipe']);

        // Inventory - Suppliers
        Route::resource('inventory/suppliers', SupplierController::class)
            ->names('inventory.suppliers')->parameters(['supplier' => 'supplier']);

        // Inventory - Purchase Orders
        Route::resource('inventory/purchase-orders', PurchaseOrderController::class)
            ->names('inventory.purchase-orders')->parameters(['purchaseOrder' => 'purchase_order']);
        Route::post('inventory/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])
            ->name('inventory.purchase-orders.receive');

        // Inventory - Stock Adjustments
        Route::get('inventory/adjustments', [StockAdjustmentController::class, 'index'])->name('inventory.adjustments.index');
        Route::get('inventory/adjustments/create', [StockAdjustmentController::class, 'create'])->name('inventory.adjustments.create');
        Route::post('inventory/adjustments', [StockAdjustmentController::class, 'store'])->name('inventory.adjustments.store');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

        // Operations - Tables
        Route::get('operations/tables', [RestaurantTableController::class, 'indexTables'])->name('operations.tables.index');
        Route::get('operations/tables/create', [RestaurantTableController::class, 'createTable'])->name('operations.tables.create');
        Route::post('operations/tables', [RestaurantTableController::class, 'storeTable'])->name('operations.tables.store');
        Route::get('operations/tables/{table}/edit', [RestaurantTableController::class, 'editTable'])->name('operations.tables.edit');
        Route::put('operations/tables/{table}', [RestaurantTableController::class, 'updateTable'])->name('operations.tables.update');
        Route::delete('operations/tables/{table}', [RestaurantTableController::class, 'destroyTable'])->name('operations.tables.destroy');

        // Operations - Zones
        Route::get('operations/zones', [RestaurantTableController::class, 'indexZones'])->name('operations.zones.index');
        Route::post('operations/zones', [RestaurantTableController::class, 'storeZone'])->name('operations.zones.store');
        Route::delete('operations/zones/{zone}', [RestaurantTableController::class, 'destroyZone'])->name('operations.zones.destroy');

        // Operations - POS Drawers
        Route::get('operations/drawers', [PosDrawerController::class, 'index'])->name('operations.drawers.index');
        Route::post('operations/drawers', [PosDrawerController::class, 'store'])->name('operations.drawers.store');
        Route::post('operations/drawers/{posDrawer}/close', [PosDrawerController::class, 'close'])->name('operations.drawers.close');

        // Operations - KDS Stations
        Route::get('operations/kds', [KdsStationController::class, 'index'])->name('operations.kds.index');
        Route::post('operations/kds', [KdsStationController::class, 'store'])->name('operations.kds.store');
        Route::delete('operations/kds/{kdsStation}', [KdsStationController::class, 'destroy'])->name('operations.kds.destroy');
    });
});
