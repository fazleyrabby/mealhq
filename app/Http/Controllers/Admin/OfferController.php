<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::query()
            ->with('category')
            ->onSpecial()
            ->when($request->filled('home_only'), fn ($q) => $q->showOnHomeOffers())
            ->orderBy('show_on_home_offers', 'desc')
            ->orderBy('name');

        $offers = $query->paginate(20)->withQueryString();

        return view('admin.menu.offers.index', compact('offers'));
    }
}
