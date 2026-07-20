<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'subject' => 'required|string|max:150',
            'message' => 'required|string',
        ]);

        $validated['ip_address'] = $request->ip();

        ContactInquiry::create($validated);

        return redirect()->back()->with('success', 'Thank you! We will get back to you shortly.');
    }
}
