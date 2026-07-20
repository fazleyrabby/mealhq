<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CmsBanner;
use App\Models\CmsPage;
use App\Models\MenuItem;
use App\Models\Setting;
use App\Services\HtmlPurifierService;

class PublicWebsiteController extends Controller
{
    public function home()
    {
        $hero = CmsPage::where('slug', 'home')->first();
        $banners = CmsBanner::active()->ordered()->get();
        $companyName = Setting::get('company_name', 'MealHQ');

        $specials = MenuItem::query()
            ->active()
            ->visibleOnChannel('web_only')
            ->onSpecial()
            ->showOnHomeOffers()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($banners->count()) {
            $slides = $banners->map(fn ($b) => [
                'image' => $b->image_url,
                'title' => $b->title,
                'subtitle' => $b->subtitle,
                'cta_text' => $b->cta_text,
                'cta_url' => $b->cta_url,
            ])->all();
        } else {
            $defaults = [
                ['hero_banner.jpg', 'Exceptional cuisine, crafted with passion.', 'Fresh ingredients, bold flavors, and unforgettable moments await you.'],
                ['kitchen_banner.jpg', 'Made by hand, served with heart.', 'Every dish is prepared by our chefs using locally sourced produce.'],
                ['qr_banner.jpg', 'Scan, order, and relax.', 'Browse the menu and place your order in just a few taps.'],
            ];
            $slides = collect($defaults)->map(fn ($d) => [
                'image' => asset('images/banners/'.$d[0]),
                'title' => $d[1],
                'subtitle' => $d[2],
                'cta_text' => 'View Our Menu',
                'cta_url' => route('public.menu'),
            ])->all();
        }

        return view('public.home', compact('hero', 'banners', 'slides', 'specials', 'companyName'));
    }

    public function menu()
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with(['menuItems' => function ($q) {
                $q->active()
                    ->visibleOnChannel('web_only')
                    ->orderBy('sort_order')
                    ->orderBy('name');
            }])
            ->get()
            ->filter(fn ($category) => $category->menuItems->isNotEmpty());

        return view('public.menu', compact('categories'));
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function about()
    {
        $page = CmsPage::where('slug', 'about')->first();

        $content = '';
        if ($page && $page->content) {
            $content = HtmlPurifierService::clean($page->content);
        }

        return view('public.about', compact('page', 'content'));
    }
}
