<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\CmsPromotion;
use App\Models\Setting;

class PublicWebsiteController extends Controller
{
    public function home()
    {
        $hero = CmsPage::where('slug', 'home')->first();
        $promotions = CmsPromotion::active()->get();
        $companyName = Setting::get('company_name', 'My Restaurant');

        return view('public.home', compact('hero', 'promotions', 'companyName'));
    }

    public function menu()
    {
        return view('public.menu');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function about()
    {
        $page = CmsPage::where('slug', 'about')->first();

        return view('public.about', compact('page'));
    }
}
