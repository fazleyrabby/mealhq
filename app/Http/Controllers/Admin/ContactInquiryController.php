<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    public function index()
    {
        $inquiries = ContactInquiry::latest()->paginate(20);

        return view('admin.cms.inquiries.index', compact('inquiries'));
    }

    public function show(ContactInquiry $inquiry)
    {
        return view('admin.cms.inquiries.show', compact('inquiry'));
    }

    public function markRead(ContactInquiry $inquiry)
    {
        $inquiry->update(['status' => 'read']);

        return redirect()->back()->with('success', 'Marked as read.');
    }

    public function markReplied(Request $request, ContactInquiry $inquiry)
    {
        $inquiry->update([
            'status' => 'replied',
            'notes' => $request->input('notes', $inquiry->notes),
        ]);

        return redirect()->back()->with('success', 'Marked as replied.');
    }

    public function destroy(ContactInquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()->route('admin.cms.inquiries.index')->with('success', 'Inquiry deleted.');
    }
}
