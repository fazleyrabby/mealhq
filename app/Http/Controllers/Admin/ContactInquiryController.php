<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use App\Services\AdminListingService;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            ContactInquiry::latest(),
            ['name', 'email', 'subject', 'message'],
            ['is_read' => ['1', '0'], 'is_replied' => ['1', '0']],
            'created_at',
            'desc'
        );

        return view('admin.cms.inquiries.index', $result + ['inquiries' => $result['items']]);
    }

    public function show(ContactInquiry $inquiry)
    {
        if (! $inquiry->is_read) {
            $inquiry->update(['is_read' => true]);
        }

        return view('admin.cms.inquiries.show', compact('inquiry'));
    }

    public function markRead(ContactInquiry $inquiry)
    {
        $inquiry->update(['is_read' => true]);

        return back()->with('success', 'Marked as read.');
    }

    public function markReplied(ContactInquiry $inquiry)
    {
        $inquiry->update(['is_replied' => true]);

        return back()->with('success', 'Marked as replied.');
    }

    public function destroy(ContactInquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()->route('admin.cms.inquiries.index')->with('success', 'Inquiry deleted.');
    }
}
