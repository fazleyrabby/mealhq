<?php

namespace Database\Seeders;

use App\Models\BusinessHour;
use App\Models\Category;
use App\Models\CmsFaq;
use App\Models\CmsGalleryAlbum;
use App\Models\CmsGalleryItem;
use App\Models\CmsPage;
use App\Models\CmsPromotion;
use App\Models\CmsSection;
use App\Models\Ingredient;
use App\Models\KdsStation;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\ModifierGroup;
use App\Models\ModifierItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PosDrawer;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RestaurantTable;
use App\Models\Setting;
use App\Models\StockAdjustment;
use App\Models\Supplier;
use App\Models\TableZone;
use App\Models\TaxRate;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Settings ─────────────────────────────────────────────
        $settings = [
            ['key' => 'company_name', 'value' => 'MealHQ', 'group' => 'general'],
            ['key' => 'company_email', 'value' => 'info@mealhq.test', 'group' => 'general'],
            ['key' => 'company_phone', 'value' => '+1 (555) 123-4567', 'group' => 'general'],
            ['key' => 'company_address', 'value' => '123 Main Street, New York, NY 10001', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'USD', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'America/New_York', 'group' => 'general'],
            ['key' => 'tax_rate', 'value' => '10', 'group' => 'billing'],
            ['key' => 'service_charge_rate', 'value' => '5', 'group' => 'billing'],
            ['key' => 'default_order_status', 'value' => 'pending', 'group' => 'orders'],
            ['key' => 'auto_confirm_orders', 'value' => '1', 'group' => 'orders'],
            ['key' => 'max_dine_in_capacity', 'value' => '100', 'group' => 'reservations'],
            ['key' => 'enable_online_ordering', 'value' => '1', 'group' => 'orders'],
            ['key' => 'enable_delivery', 'value' => '1', 'group' => 'orders'],
            ['key' => 'low_stock_threshold', 'value' => '10', 'group' => 'inventory'],
            ['key' => 'loyalty_points_per_dollar', 'value' => '10', 'group' => 'loyalty'],
        ];
        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }

        // ─── Business Hours ───────────────────────────────────────
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $i => $day) {
            BusinessHour::firstOrCreate(
                ['day_of_week' => $day],
                [
                    'day_of_week' => $day,
                    'opening_time' => in_array($i, [5, 6]) ? '08:00' : '09:00',
                    'closing_time' => in_array($i, [5, 6]) ? '23:00' : '22:00',
                    'is_closed' => false,
                ]
            );
        }

        // ─── Tax Rates ────────────────────────────────────────────
        TaxRate::firstOrCreate(
            ['name' => 'Standard VAT'],
            ['rate' => 10.00, 'type' => 'percentage', 'is_default' => true, 'is_active' => true]
        );
        TaxRate::firstOrCreate(
            ['name' => 'Reduced VAT'],
            ['rate' => 5.00, 'type' => 'percentage', 'is_default' => false, 'is_active' => true]
        );

        // ─── Units ────────────────────────────────────────────────
        $units = [
            ['name' => 'Kilogram', 'short_code' => 'kg'],
            ['name' => 'Gram', 'short_code' => 'g'],
            ['name' => 'Liter', 'short_code' => 'L'],
            ['name' => 'Milliliter', 'short_code' => 'ml'],
            ['name' => 'Piece', 'short_code' => 'pc'],
            ['name' => 'Cup', 'short_code' => 'cup'],
            ['name' => 'Tablespoon', 'short_code' => 'tbsp'],
            ['name' => 'Teaspoon', 'short_code' => 'tsp'],
            ['name' => 'Ounce', 'short_code' => 'oz'],
            ['name' => 'Pound', 'short_code' => 'lb'],
        ];
        foreach ($units as $u) {
            Unit::firstOrCreate(['short_code' => $u['short_code']], $u);
        }
        $unitKg = Unit::where('short_code', 'kg')->first();
        $unitG = Unit::where('short_code', 'g')->first();
        $unitL = Unit::where('short_code', 'L')->first();
        $unitPc = Unit::where('short_code', 'pc')->first();

        // ─── CMS Pages ────────────────────────────────────────────
        $home = CmsPage::firstOrCreate(
            ['slug' => 'home'],
            ['title' => 'Home', 'content' => 'Welcome to MealHQ', 'is_active' => true, 'is_system' => true]
        );
        CmsSection::firstOrCreate(
            ['page_id' => $home->id, 'section_key' => 'hero'],
            ['title' => 'Delicious Food, Delivered Fresh', 'subtitle' => 'Experience the finest cuisine in town', 'body_content' => 'From our kitchen to your table.', 'sort_order' => 0, 'is_visible' => true]
        );
        CmsSection::firstOrCreate(
            ['page_id' => $home->id, 'section_key' => 'features'],
            ['title' => 'Why Choose Us', 'body_content' => 'Fresh ingredients, expert chefs, and exceptional service.', 'sort_order' => 1, 'is_visible' => true]
        );

        $about = CmsPage::firstOrCreate(
            ['slug' => 'about'],
            ['title' => 'About Us', 'is_active' => true, 'is_system' => true]
        );
        $about->content = <<<HTML
<h2>Our Story</h2>
<p>Founded in 2024, MealHQ began with a simple idea: bring restaurant-quality food to your door without compromising on taste, freshness, or care. What started as a small neighborhood kitchen has grown into a beloved destination for those who appreciate honest cooking and warm hospitality.</p>
<p>Every dish that leaves our kitchen is a reflection of our love for food and the community we serve. We work closely with local farmers and producers to source the freshest seasonal ingredients, and our chefs craft each plate by hand with attention to detail.</p>

<h2>Our Mission</h2>
<p>We believe great food has the power to bring people together. Our mission is to make exceptional dining accessible — whether you are joining us in the restaurant, ordering for delivery, or celebrating a special occasion. We are committed to quality, sustainability, and a genuinely welcoming experience for every guest.</p>

<h2>What Sets Us Apart</h2>
<ul>
    <li><strong>Fresh, local ingredients</strong> sourced daily from trusted regional suppliers.</li>
    <li><strong>Hand-crafted menus</strong> designed by our culinary team and refreshed with the seasons.</li>
    <li><strong>A warm, welcoming space</strong> where every guest feels at home.</li>
    <li><strong>Thoughtful service</strong> from a team that truly cares about your experience.</li>
</ul>

<blockquote>“From our kitchen to your table — crafted with care, served with love.”</blockquote>
HTML;
        $about->save();
        CmsSection::firstOrCreate(
            ['page_id' => $about->id, 'section_key' => 'story'],
            ['title' => 'Our Story', 'body_content' => 'Founded in 2024, MealHQ has been serving delicious meals to the community with passion and dedication.', 'sort_order' => 0, 'is_visible' => true]
        );

        CmsPage::firstOrCreate(
            ['slug' => 'contact'],
            ['title' => 'Contact Us', 'content' => 'Get in touch', 'is_active' => true, 'is_system' => true]
        );

        // ─── CMS Promotions ───────────────────────────────────────
        CmsPromotion::create([
            'title' => 'Weekend Special',
            'subtitle' => '20% off on all dine-in orders',
            'promo_code' => 'WEEKEND20',
            'discount_type' => 'percentage',
            'discount_value' => 20,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'is_active' => true,
            'cta_url' => '/menu',
            'cta_text' => 'Order Now',
        ]);
        CmsPromotion::create([
            'title' => 'Free Delivery',
            'subtitle' => 'Free delivery on orders over $30',
            'promo_code' => 'FREEDEL',
            'discount_type' => 'free_delivery',
            'discount_value' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(60),
            'is_active' => true,
        ]);

        // ─── CMS FAQs ─────────────────────────────────────────────
        $faqs = [
            ['category' => 'general', 'question' => 'What are your opening hours?', 'answer' => 'We are open Monday to Friday 9AM-10PM, and weekends 8AM-11PM.', 'sort_order' => 0, 'is_active' => true],
            ['category' => 'menu', 'question' => 'Do you offer vegetarian options?', 'answer' => 'Yes! We have a wide selection of vegetarian and vegan dishes on our menu.', 'sort_order' => 1, 'is_active' => true],
            ['category' => 'reservations', 'question' => 'Can I make a reservation?', 'answer' => 'Absolutely! You can call us or use our online reservation system.', 'sort_order' => 2, 'is_active' => true],
            ['category' => 'catering', 'question' => 'Do you offer catering services?', 'answer' => 'Yes, we provide catering for events of all sizes. Contact us for more details.', 'sort_order' => 3, 'is_active' => true],
            ['category' => 'billing', 'question' => 'What payment methods do you accept?', 'answer' => 'We accept all major credit cards, debit cards, and mobile payments.', 'sort_order' => 4, 'is_active' => true],
        ];
        foreach ($faqs as $faq) {
            CmsFaq::firstOrCreate(['question' => $faq['question']], $faq);
        }

        // ─── CMS Gallery ──────────────────────────────────────────
        $album = CmsGalleryAlbum::firstOrCreate(
            ['slug' => 'interior'],
            ['name' => 'Restaurant Interior', 'description' => 'Photos of our restaurant', 'sort_order' => 0, 'is_active' => true]
        );
        CmsGalleryItem::create(['album_id' => $album->id, 'title' => 'Main Dining Area', 'sort_order' => 0]);
        CmsGalleryItem::create(['album_id' => $album->id, 'title' => 'Bar Area', 'sort_order' => 1]);
        CmsGalleryItem::create(['album_id' => $album->id, 'title' => 'Outdoor Patio', 'sort_order' => 2]);

        // ─── Menu Categories ──────────────────────────────────────
        $mainCourse = Category::firstOrCreate(
            ['slug' => 'main-course'],
            ['name' => 'Main Course', 'description' => 'Hearty main dishes', 'sort_order' => 0, 'is_active' => true]
        );
        $appetizers = Category::firstOrCreate(
            ['slug' => 'appetizers'],
            ['name' => 'Appetizers', 'description' => 'Start your meal right', 'sort_order' => 1, 'is_active' => true]
        );
        $desserts = Category::firstOrCreate(
            ['slug' => 'desserts'],
            ['name' => 'Desserts', 'description' => 'Sweet endings', 'sort_order' => 2, 'is_active' => true]
        );
        $beverages = Category::firstOrCreate(
            ['slug' => 'beverages'],
            ['name' => 'Beverages', 'description' => 'Refreshing drinks', 'sort_order' => 3, 'is_active' => true]
        );
        $salads = Category::firstOrCreate(
            ['slug' => 'salads'],
            ['name' => 'Salads', 'description' => 'Fresh and healthy', 'sort_order' => 4, 'is_active' => true]
        );

        // ─── Menu Items ───────────────────────────────────────────
        $items = [
            // Main Courses
            ['name' => 'Grilled Ribeye Steak', 'slug' => 'grilled-ribeye-steak', 'category_id' => $mainCourse->id, 'base_price' => 34.99, 'description' => '12oz ribeye with herb butter and seasonal vegetables', 'is_active' => true, 'is_featured' => true, 'image_url' => '/storage/menu-items/grilled-ribeye-steak.jpg'],
            ['name' => 'Pan-Seared Salmon', 'slug' => 'pan-seared-salmon', 'category_id' => $mainCourse->id, 'base_price' => 26.99, 'description' => 'Atlantic salmon with lemon dill sauce and rice pilaf', 'is_active' => true, 'image_url' => '/storage/menu-items/pan-seared-salmon.jpg'],
            ['name' => 'Chicken Parmesan', 'slug' => 'chicken-parmesan', 'category_id' => $mainCourse->id, 'base_price' => 22.99, 'description' => 'Breaded chicken breast with marinara and mozzarella', 'is_active' => true, 'image_url' => '/storage/menu-items/chicken-parmesan.jpg'],
            ['name' => 'Vegetable Stir Fry', 'slug' => 'vegetable-stir-fry', 'category_id' => $mainCourse->id, 'base_price' => 18.99, 'description' => 'Fresh seasonal vegetables in a savory sauce', 'is_active' => true, 'image_url' => '/storage/menu-items/vegetable-stir-fry.jpg'],
            ['name' => 'Lamb Chops', 'slug' => 'lamb-chops', 'category_id' => $mainCourse->id, 'base_price' => 38.99, 'description' => 'Grilled lamb chops with mint sauce and roasted potatoes', 'is_active' => true, 'is_featured' => true, 'image_url' => '/storage/menu-items/lamb-chops.jpg'],
            // Appetizers
            ['name' => 'Bruschetta', 'slug' => 'bruschetta', 'category_id' => $appetizers->id, 'base_price' => 9.99, 'description' => 'Toasted bread with tomatoes, basil, and balsamic glaze', 'is_active' => true, 'image_url' => '/storage/menu-items/bruschetta.jpg'],
            ['name' => 'Calamari', 'slug' => 'calamari', 'category_id' => $appetizers->id, 'base_price' => 12.99, 'description' => 'Crispy fried calamari with marinara sauce', 'is_active' => true, 'image_url' => '/storage/menu-items/calamari.jpg'],
            ['name' => 'Spinach Artichoke Dip', 'slug' => 'spinach-artichoke-dip', 'category_id' => $appetizers->id, 'base_price' => 11.99, 'description' => 'Creamy dip served with tortilla chips', 'is_active' => true, 'image_url' => '/storage/menu-items/spinach-artichoke-dip.jpg'],
            ['name' => 'Chicken Wings', 'slug' => 'chicken-wings', 'category_id' => $appetizers->id, 'base_price' => 14.99, 'description' => 'Buffalo wings with ranch dressing', 'is_active' => true, 'has_variants' => true, 'image_url' => '/storage/menu-items/chicken-wings.jpg'],
            // Desserts
            ['name' => 'Tiramisu', 'slug' => 'tiramisu', 'category_id' => $desserts->id, 'base_price' => 8.99, 'description' => 'Classic Italian coffee-flavored dessert', 'is_active' => true, 'image_url' => '/storage/menu-items/tiramisu.jpg'],
            ['name' => 'Chocolate Lava Cake', 'slug' => 'chocolate-lava-cake', 'category_id' => $desserts->id, 'base_price' => 9.99, 'description' => 'Warm chocolate cake with molten center', 'is_active' => true, 'is_featured' => true, 'image_url' => '/storage/menu-items/chocolate-lava-cake.jpg'],
            ['name' => 'Cheesecake', 'slug' => 'cheesecake', 'category_id' => $desserts->id, 'base_price' => 7.99, 'description' => 'New York style cheesecake with berry compote', 'is_active' => true, 'image_url' => '/storage/menu-items/cheesecake.jpg'],
            // Beverages
            ['name' => 'Fresh Lemonade', 'slug' => 'fresh-lemonade', 'category_id' => $beverages->id, 'base_price' => 4.99, 'description' => 'House-made lemonade', 'is_active' => true, 'image_url' => '/storage/menu-items/fresh-lemonade.jpg'],
            ['name' => 'Iced Tea', 'slug' => 'iced-tea', 'category_id' => $beverages->id, 'base_price' => 3.99, 'description' => 'Fresh brewed iced tea', 'is_active' => true, 'image_url' => '/storage/menu-items/iced-tea.jpg'],
            ['name' => 'Espresso', 'slug' => 'espresso', 'category_id' => $beverages->id, 'base_price' => 3.49, 'description' => 'Double shot espresso', 'is_active' => true, 'image_url' => '/storage/menu-items/espresso.jpg'],
            // Salads
            ['name' => 'Caesar Salad', 'slug' => 'caesar-salad', 'category_id' => $salads->id, 'base_price' => 13.99, 'description' => 'Romaine lettuce, croutons, parmesan with Caesar dressing', 'is_active' => true, 'image_url' => '/storage/menu-items/caesar-salad.jpg'],
            ['name' => 'Greek Salad', 'slug' => 'greek-salad', 'category_id' => $salads->id, 'base_price' => 14.99, 'description' => 'Tomatoes, cucumbers, olives, feta cheese', 'is_active' => true, 'image_url' => '/storage/menu-items/greek-salad.jpg'],
            ['name' => 'Garden Salad', 'slug' => 'garden-salad', 'category_id' => $salads->id, 'base_price' => 9.99, 'description' => 'Mixed greens with seasonal vegetables', 'is_active' => true, 'image_url' => '/storage/menu-items/garden-salad.jpg'],
        ];
        foreach ($items as $data) {
            $imagePath = 'menu-items/' . $data['slug'] . '.jpg';
            if (isset($data['image_url']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath)) {
                // Image exists, keep it
            } else {
                $data['image_url'] = null; // Use fallback placeholder in UI
            }
            MenuItem::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // ─── Special Offers (20% off) ──────────────────────────────
        $homeOffers = [
            'grilled-ribeye-steak' => 27.99,
            'pan-seared-salmon'    => 21.59,
            'chocolate-lava-cake'  => 7.99,
        ];
        foreach ($homeOffers as $slug => $special) {
            $item = MenuItem::where('slug', $slug)->first();
            if ($item) {
                $item->update([
                    'special_price' => $special,
                    'show_on_home_offers' => true,
                ]);
            }
        }

        // ─── Menu Item Variants ───────────────────────────────────
        $wings = MenuItem::where('slug', 'chicken-wings')->first();
        if ($wings) {
            MenuItemVariant::firstOrCreate(
                ['menu_item_id' => $wings->id, 'name' => '6 Pieces'],
                ['price_adjustment' => 0, 'is_active' => true]
            );
            MenuItemVariant::firstOrCreate(
                ['menu_item_id' => $wings->id, 'name' => '10 Pieces'],
                ['price_adjustment' => 5.00, 'is_active' => true]
            );
            MenuItemVariant::firstOrCreate(
                ['menu_item_id' => $wings->id, 'name' => '20 Pieces'],
                ['price_adjustment' => 12.00, 'is_active' => true]
            );
        }

        // ─── Modifier Groups & Items ──────────────────────────────
        $dressings = ModifierGroup::firstOrCreate(
            ['name' => 'Dressing'],
            ['type' => 'select_one', 'max_selections' => 1, 'min_selections' => 0, 'sort_order' => 0, 'is_active' => true]
        );
        $sides = ModifierGroup::firstOrCreate(
            ['name' => 'Side Dish'],
            ['type' => 'select_one', 'max_selections' => 1, 'min_selections' => 0, 'sort_order' => 1, 'is_active' => true]
        );
        $extras = ModifierGroup::firstOrCreate(
            ['name' => 'Extra Toppings'],
            ['type' => 'select_multiple', 'max_selections' => 3, 'min_selections' => 0, 'sort_order' => 2, 'is_active' => true]
        );

        $dressingItems = ['Caesar', 'Ranch', 'Italian', 'Balsamic Vinaigrette', 'Blue Cheese'];
        foreach ($dressingItems as $i => $name) {
            ModifierItem::firstOrCreate(
                ['modifier_group_id' => $dressings->id, 'name' => $name],
                ['price_adjustment' => 0, 'sort_order' => $i, 'is_active' => true]
            );
        }

        $sideItems = ['French Fries', 'Sweet Potato Fries', 'Side Salad', 'Coleslaw', 'Rice Pilaf'];
        foreach ($sideItems as $i => $name) {
            ModifierItem::firstOrCreate(
                ['modifier_group_id' => $sides->id, 'name' => $name],
                ['price_adjustment' => $i === 0 ? 0 : 1.99, 'sort_order' => $i, 'is_active' => true]
            );
        }

        $extraItems = ['Extra Cheese' => 1.99, 'Bacon' => 2.49, 'Avocado' => 1.99, 'Fried Egg' => 1.49, 'Mushrooms' => 1.29];
        $i = 0;
        foreach ($extraItems as $name => $price) {
            ModifierItem::firstOrCreate(
                ['modifier_group_id' => $extras->id, 'name' => $name],
                ['price_adjustment' => $price, 'sort_order' => $i++, 'is_active' => true]
            );
        }

        // Link modifier groups to menu items (salads)
        $caesarSalad = MenuItem::where('slug', 'caesar-salad')->first();
        $greekSalad = MenuItem::where('slug', 'greek-salad')->first();
        if ($caesarSalad) {
            $caesarSalad->modifierGroups()->syncWithoutDetaching([$dressings->id, $extras->id]);
        }
        if ($greekSalad) {
            $greekSalad->modifierGroups()->syncWithoutDetaching([$dressings->id]);
        }

        // ─── Ingredients ──────────────────────────────────────────
        $ingredients = [
            ['name' => 'Chicken Breast', 'unit_id' => $unitKg->id, 'stock_quantity' => 25, 'cost_per_unit' => 8.99, 'min_stock_level' => 5],
            ['name' => 'Ribeye Steak', 'unit_id' => $unitKg->id, 'stock_quantity' => 15, 'cost_per_unit' => 18.50, 'min_stock_level' => 3],
            ['name' => 'Salmon Fillet', 'unit_id' => $unitKg->id, 'stock_quantity' => 12, 'cost_per_unit' => 15.99, 'min_stock_level' => 3],
            ['name' => 'Lamb Chops', 'unit_id' => $unitKg->id, 'stock_quantity' => 10, 'cost_per_unit' => 22.00, 'min_stock_level' => 2],
            ['name' => 'Mixed Vegetables', 'unit_id' => $unitKg->id, 'stock_quantity' => 30, 'cost_per_unit' => 3.50, 'min_stock_level' => 10],
            ['name' => 'Romaine Lettuce', 'unit_id' => $unitPc->id, 'stock_quantity' => 40, 'cost_per_unit' => 1.99, 'min_stock_level' => 10],
            ['name' => 'Tomatoes', 'unit_id' => $unitKg->id, 'stock_quantity' => 20, 'cost_per_unit' => 4.99, 'min_stock_level' => 5],
            ['name' => 'Mozzarella Cheese', 'unit_id' => $unitKg->id, 'stock_quantity' => 10, 'cost_per_unit' => 9.99, 'min_stock_level' => 3],
            ['name' => 'Olive Oil', 'unit_id' => $unitL->id, 'stock_quantity' => 8, 'cost_per_unit' => 12.99, 'min_stock_level' => 2],
            ['name' => 'Garlic', 'unit_id' => $unitKg->id, 'stock_quantity' => 5, 'cost_per_unit' => 6.99, 'min_stock_level' => 1],
            ['name' => 'Flour', 'unit_id' => $unitKg->id, 'stock_quantity' => 25, 'cost_per_unit' => 2.49, 'min_stock_level' => 5],
            ['name' => 'Butter', 'unit_id' => $unitKg->id, 'stock_quantity' => 15, 'cost_per_unit' => 5.99, 'min_stock_level' => 3],
            ['name' => 'Eggs', 'unit_id' => $unitPc->id, 'stock_quantity' => 60, 'cost_per_unit' => 0.35, 'min_stock_level' => 12],
            ['name' => 'Milk', 'unit_id' => $unitL->id, 'stock_quantity' => 10, 'cost_per_unit' => 3.99, 'min_stock_level' => 3],
            ['name' => 'Potatoes', 'unit_id' => $unitKg->id, 'stock_quantity' => 35, 'cost_per_unit' => 1.99, 'min_stock_level' => 10],
        ];
        foreach ($ingredients as $data) {
            Ingredient::firstOrCreate(['name' => $data['name']], $data);
        }

        // ─── Recipes ──────────────────────────────────────────────
        $steak = MenuItem::where('slug', 'grilled-ribeye-steak')->first();
        $salmon = MenuItem::where('slug', 'pan-seared-salmon')->first();
        $chickenParm = MenuItem::where('slug', 'chicken-parmesan')->first();

        if ($steak) {
            $recipe = Recipe::firstOrCreate(
                ['menu_item_id' => $steak->id, 'name' => $steak->name . ' Recipe'],
                ['instructions' => 'Season the ribeye with salt and pepper. Grill to desired doneness. Serve with herb butter and seasonal vegetables.', 'yield_quantity' => 1]
            );
            $ribeyeIng = Ingredient::where('name', 'Ribeye Steak')->first();
            $butterIng = Ingredient::where('name', 'Butter')->first();
            if ($ribeyeIng) {
                RecipeIngredient::firstOrCreate(
                    ['recipe_id' => $recipe->id, 'ingredient_id' => $ribeyeIng->id],
                    ['quantity' => 0.340, 'waste_percentage' => 5]
                );
            }
            if ($butterIng) {
                RecipeIngredient::firstOrCreate(
                    ['recipe_id' => $recipe->id, 'ingredient_id' => $butterIng->id],
                    ['quantity' => 0.030, 'waste_percentage' => 2]
                );
            }
        }

        if ($salmon) {
            $recipe = Recipe::firstOrCreate(
                ['menu_item_id' => $salmon->id, 'name' => $salmon->name . ' Recipe'],
                ['instructions' => 'Season salmon fillet. Pan-sear skin-side down until crispy. Flip and cook until done. Serve with lemon dill sauce.', 'yield_quantity' => 1]
            );
            $salmonIng = Ingredient::where('name', 'Salmon Fillet')->first();
            $lemonIng = Ingredient::where('name', 'Olive Oil')->first();
            if ($salmonIng) {
                RecipeIngredient::firstOrCreate(
                    ['recipe_id' => $recipe->id, 'ingredient_id' => $salmonIng->id],
                    ['quantity' => 0.200, 'waste_percentage' => 5]
                );
            }
            if ($lemonIng) {
                RecipeIngredient::firstOrCreate(
                    ['recipe_id' => $recipe->id, 'ingredient_id' => $lemonIng->id],
                    ['quantity' => 0.015, 'waste_percentage' => 2]
                );
            }
        }

        if ($chickenParm) {
            $recipe = Recipe::firstOrCreate(
                ['menu_item_id' => $chickenParm->id, 'name' => $chickenParm->name . ' Recipe'],
                ['instructions' => 'Bread the chicken breast. Fry until golden. Top with marinara and mozzarella. Bake until cheese melts.', 'yield_quantity' => 1]
            );
            $chickenIng = Ingredient::where('name', 'Chicken Breast')->first();
            $cheeseIng = Ingredient::where('name', 'Mozzarella Cheese')->first();
            if ($chickenIng) {
                RecipeIngredient::firstOrCreate(
                    ['recipe_id' => $recipe->id, 'ingredient_id' => $chickenIng->id],
                    ['quantity' => 0.250, 'waste_percentage' => 5]
                );
            }
            if ($cheeseIng) {
                RecipeIngredient::firstOrCreate(
                    ['recipe_id' => $recipe->id, 'ingredient_id' => $cheeseIng->id],
                    ['quantity' => 0.100, 'waste_percentage' => 3]
                );
            }
        }

        // ─── Suppliers ────────────────────────────────────────────
        $suppliers = [
            ['name' => 'Fresh Foods Co.', 'contact_person' => 'John Smith', 'email' => 'john@freshfoods.test', 'phone' => '+1 (555) 100-2000', 'address' => '100 Market Street, New York, NY', 'is_active' => true],
            ['name' => 'Quality Meats Ltd.', 'contact_person' => 'Sarah Johnson', 'email' => 'sarah@qualitymeats.test', 'phone' => '+1 (555) 200-3000', 'address' => '200 Industrial Blvd, New York, NY', 'is_active' => true],
            ['name' => 'Ocean Fresh Seafood', 'contact_person' => 'Mike Chen', 'email' => 'mike@oceanfresh.test', 'phone' => '+1 (555) 300-4000', 'address' => '300 Harbor Drive, New York, NY', 'is_active' => true],
            ['name' => 'Green Valley Produce', 'contact_person' => 'Lisa Garcia', 'email' => 'lisa@greenvalley.test', 'phone' => '+1 (555) 400-5000', 'address' => '400 Farm Road, New York, NY', 'is_active' => true],
            ['name' => 'Beverage Distributors Inc.', 'contact_person' => 'David Wilson', 'email' => 'david@beveragedist.test', 'phone' => '+1 (555) 500-6000', 'address' => '500 Commerce Ave, New York, NY', 'is_active' => true],
        ];
        foreach ($suppliers as $data) {
            Supplier::firstOrCreate(['email' => $data['email']], $data);
        }

        // ─── Purchase Orders ──────────────────────────────────────
        $adminUser = User::first();
        $supplier = Supplier::first();
        if ($supplier && $adminUser) {
            $po = PurchaseOrder::firstOrCreate(
                ['order_number' => 'PO-2024-0001'],
                [
                    'supplier_id' => $supplier->id,
                    'status' => 'received',
                    'total_amount' => 0,
                    'notes' => 'Initial stock order',
                    'created_by' => $adminUser->id,
                    'order_date' => now()->subDays(5),
                    'expected_delivery' => now()->subDays(2),
                ]
            );
            $ingredients = Ingredient::take(5)->get();
            if ($po->wasRecentlyCreated) {
                foreach ($ingredients as $ing) {
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'ingredient_id' => $ing->id,
                        'quantity' => 10,
                        'unit_cost' => $ing->cost_per_unit,
                        'total_cost' => 10 * $ing->cost_per_unit,
                        'received_quantity' => 10,
                    ]);
                }
            }
        }

        // ─── Stock Adjustments ────────────────────────────────────
        $lowStockIngredient = Ingredient::first();
        if ($lowStockIngredient && $adminUser) {
            StockAdjustment::create([
                'ingredient_id' => $lowStockIngredient->id,
                'type' => 'addition',
                'quantity' => 5,
                'unit_cost' => $lowStockIngredient->cost_per_unit,
                'reason' => 'Initial stock addition',
                'adjusted_by' => $adminUser->id,
            ]);
        }

        // ─── Table Zones & Restaurant Tables ──────────────────────
        $zoneIndoor = TableZone::firstOrCreate(
            ['name' => 'Main Hall'],
            ['color' => '#4CAF50', 'sort_order' => 0, 'is_active' => true]
        );
        $zoneOutdoor = TableZone::firstOrCreate(
            ['name' => 'Patio'],
            ['color' => '#2196F3', 'sort_order' => 1, 'is_active' => true]
        );
        $zoneBar = TableZone::firstOrCreate(
            ['name' => 'Bar Area'],
            ['color' => '#FF9800', 'sort_order' => 2, 'is_active' => true]
        );

        $tables = [
            ['zone_id' => $zoneIndoor->id, 'table_number' => 'T1', 'capacity' => 2, 'status' => 'available', 'sort_order' => 0],
            ['zone_id' => $zoneIndoor->id, 'table_number' => 'T2', 'capacity' => 2, 'status' => 'available', 'sort_order' => 1],
            ['zone_id' => $zoneIndoor->id, 'table_number' => 'T3', 'capacity' => 4, 'status' => 'occupied', 'sort_order' => 2],
            ['zone_id' => $zoneIndoor->id, 'table_number' => 'T4', 'capacity' => 4, 'status' => 'available', 'sort_order' => 3],
            ['zone_id' => $zoneIndoor->id, 'table_number' => 'T5', 'capacity' => 6, 'status' => 'available', 'sort_order' => 4],
            ['zone_id' => $zoneOutdoor->id, 'table_number' => 'P1', 'capacity' => 2, 'status' => 'reserved', 'sort_order' => 5],
            ['zone_id' => $zoneOutdoor->id, 'table_number' => 'P2', 'capacity' => 4, 'status' => 'available', 'sort_order' => 6],
            ['zone_id' => $zoneOutdoor->id, 'table_number' => 'P3', 'capacity' => 6, 'status' => 'available', 'sort_order' => 7],
            ['zone_id' => $zoneBar->id, 'table_number' => 'B1', 'capacity' => 2, 'status' => 'available', 'sort_order' => 8],
            ['zone_id' => $zoneBar->id, 'table_number' => 'B2', 'capacity' => 2, 'status' => 'cleaning', 'sort_order' => 9],
        ];
        foreach ($tables as $data) {
            RestaurantTable::firstOrCreate(
                ['table_number' => $data['table_number']],
                $data
            );
        }

        // ─── KDS Stations ─────────────────────────────────────────
        $kdsStations = [
            ['name' => 'Main Kitchen', 'display_name' => 'Kitchen', 'type' => 'kitchen', 'sort_order' => 0, 'is_active' => true],
            ['name' => 'Grill Station', 'display_name' => 'Grill', 'type' => 'grill', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Salad Prep', 'display_name' => 'Salads', 'type' => 'prep', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Bar', 'display_name' => 'Bar', 'type' => 'bar', 'sort_order' => 3, 'is_active' => true],
        ];
        foreach ($kdsStations as $data) {
            KdsStation::firstOrCreate(['name' => $data['name']], $data);
        }

        // ─── POS Drawers ──────────────────────────────────────────
        if ($adminUser) {
            PosDrawer::create([
                'name' => 'Main Register',
                'opening_balance' => 200.00,
                'opened_by' => $adminUser->id,
                'opened_at' => now()->subHours(8),
                'status' => 'open',
                'notes' => 'Morning shift',
            ]);
        }

        // ─── Orders (seed data for Reports & Analytics) ─────────
        if (Order::count() === 0) {
        $menuItems = MenuItem::all();
        $staff = User::first();
        $seq = 1;
        $statusPool = ['completed', 'completed', 'completed', 'served', 'served', 'preparing', 'pending', 'cancelled'];
        $sourcePool = ['pos', 'pos', 'web', 'walk_in', 'phone'];
        $typePool = ['dine_in', 'takeaway', 'delivery'];

        for ($i = 0; $i < 90; $i++) {
            $date = now()->subDays(rand(0, 89))->subHours(rand(8, 22))->subMinutes(rand(0, 59));
            $status = $statusPool[array_rand($statusPool)];
            $source = $sourcePool[array_rand($sourcePool)];
            $numItems = rand(1, 4);
            $subtotal = 0;
            $lineItems = [];

            for ($j = 0; $j < $numItems; $j++) {
                $mi = $menuItems->random();
                $qty = rand(1, 3);
                $unit = (float) $mi->base_price;
                $lineSub = $unit * $qty;
                $subtotal += $lineSub;
                $lineItems[] = [
                    'menu_item_id' => $mi->id,
                    'item_name' => $mi->name,
                    'unit_price' => $unit,
                    'quantity' => $qty,
                    'subtotal' => $lineSub,
                ];
            }

            $tax = round($subtotal * 0.10, 2);
            $service = round($subtotal * 0.05, 2);
            $total = round($subtotal + $tax + $service, 2);

            $order = Order::create([
                'order_number' => 'ORD-' . str_pad($seq, 5, '0', STR_PAD_LEFT),
                'source' => $source,
                'status' => $status,
                'type' => $typePool[array_rand($typePool)],
                'customer_id' => null,
                'user_id' => $staff?->id,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'service_charge' => $service,
                'discount_amount' => 0,
                'total_amount' => $total,
                'ordered_at' => $date,
                'completed_at' => in_array($status, ['completed', 'served'])
                    ? $date->copy()->addMinutes(rand(20, 90))
                    : null,
            ]);
            // Backdate created_at so Reports (which filter by created_at) reflect history
            $order->created_at = $date;
            $order->save();

            foreach ($lineItems as $li) {
                $order->items()->create($li);
            }

            $seq++;
        }
        }
    }
}
