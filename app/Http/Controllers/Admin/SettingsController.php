<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessHour;
use App\Models\Setting;
use App\Models\TaxRate;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        $company = $settings->get('company', collect());
        $orders = $settings->get('orders', collect());
        $billing = $settings->get('billing', collect());
        $reservations = $settings->get('reservations', collect());
        $hours = BusinessHour::orderBy('day_of_week')->get();
        $taxRates = TaxRate::all();

        return view('admin.settings.index', compact('company', 'orders', 'billing', 'reservations', 'hours', 'taxRates'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }

    public function updateHours(Request $request)
    {
        $validated = $request->validate([
            'hours' => 'required|array',
            'hours.*.opening_time' => 'nullable|string',
            'hours.*.closing_time' => 'nullable|string',
            'hours.*.is_closed' => 'boolean',
        ]);

        foreach ($validated['hours'] as $id => $data) {
            $hour = BusinessHour::findOrFail($id);
            $hour->update([
                'opening_time' => $data['is_closed'] ? null : ($data['opening_time'] ?? null),
                'closing_time' => $data['is_closed'] ? null : ($data['closing_time'] ?? null),
                'is_closed' => $data['is_closed'] ?? false,
            ]);
        }

        return redirect()->route('admin.settings')->with('success', 'Business hours updated.');
    }

    public function storeTaxRate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:percentage,fixed',
        ]);

        TaxRate::create($validated);

        return redirect()->route('admin.settings')->with('success', 'Tax rate added.');
    }
}
