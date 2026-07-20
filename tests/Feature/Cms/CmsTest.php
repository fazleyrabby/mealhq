<?php

use App\Models\CmsFaq;
use App\Models\CmsGalleryAlbum;
use App\Models\CmsGalleryItem;
use App\Models\CmsPage;
use App\Models\CmsPromotion;
use App\Models\CmsSection;
use App\Models\ContactInquiry;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

// Pages & Sections

test('cms page can be created', function () {
    $page = CmsPage::factory()->create([
        'title' => 'About Us',
        'is_active' => true,
    ]);

    expect($page->title)->toBe('About Us');
    expect($page->slug)->not->toBeNull();
});

test('cms page generates slug from title', function () {
    $page = CmsPage::create(['title' => 'About Us']);

    expect($page->slug)->toBe('about-us');
});

test('cms page can have multiple sections', function () {
    $page = CmsPage::factory()->create();
    $section1 = CmsSection::create([
        'page_id' => $page->id,
        'section_key' => 'hero',
        'title' => 'Hero Section',
    ]);
    $section2 = CmsSection::create([
        'page_id' => $page->id,
        'section_key' => 'features',
        'title' => 'Features',
    ]);

    expect($page->sections)->toHaveCount(2);
});

test('section key must be unique per page', function () {
    $page = CmsPage::factory()->create();

    CmsSection::create([
        'page_id' => $page->id,
        'section_key' => 'hero',
        'title' => 'Hero',
    ]);

    expect(fn () => CmsSection::create([
        'page_id' => $page->id,
        'section_key' => 'hero',
        'title' => 'Duplicate',
    ]))->toThrow(QueryException::class);
});

// Promotions

test('promotion can be created with scoped active query', function () {
    $promo = CmsPromotion::create([
        'title' => 'Summer Sale',
        'promo_code' => 'SUMMER20',
        'discount_type' => 'percentage',
        'discount_value' => 20,
        'start_date' => now()->subDay(),
        'end_date' => now()->addMonth(),
    ]);

    expect(CmsPromotion::active()->count())->toBe(1);
});

test('expired promotion is not active', function () {
    CmsPromotion::create([
        'title' => 'Old Sale',
        'promo_code' => 'OLD',
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'start_date' => now()->subMonths(2),
        'end_date' => now()->subMonth(),
    ]);

    expect(CmsPromotion::active()->count())->toBe(0);
});

test('promotion can be soft deleted', function () {
    $promo = CmsPromotion::factory()->create();
    $promo->delete();

    expect(CmsPromotion::count())->toBe(0);
    expect(CmsPromotion::withTrashed()->count())->toBe(1);
});

// Gallery

test('gallery album can be created with slug', function () {
    $album = CmsGalleryAlbum::create(['name' => 'Food Gallery']);

    expect($album->slug)->toBe('food-gallery');
});

test('gallery album can have items', function () {
    $album = CmsGalleryAlbum::factory()->create();
    CmsGalleryItem::create([
        'album_id' => $album->id,
        'title' => 'Photo 1',
    ]);

    expect($album->items)->toHaveCount(1);
});

test('gallery items are cascade deleted with album', function () {
    $album = CmsGalleryAlbum::factory()->create();
    CmsGalleryItem::create([
        'album_id' => $album->id,
        'title' => 'Photo 1',
    ]);

    $album->delete();

    expect(CmsGalleryItem::count())->toBe(0);
});

// FAQs

test('faq can be created with category', function () {
    $faq = CmsFaq::create([
        'category' => 'general',
        'question' => 'What are your hours?',
        'answer' => 'We are open Mon-Sat 9am-10pm.',
    ]);

    expect($faq->category)->toBe('general');
});

test('faqs can be sorted by sort_order', function () {
    CmsFaq::create(['category' => 'general', 'question' => 'Q1', 'answer' => 'A1', 'sort_order' => 2]);
    CmsFaq::create(['category' => 'general', 'question' => 'Q2', 'answer' => 'A2', 'sort_order' => 0]);

    $faqs = CmsFaq::orderBy('sort_order')->get();
    expect($faqs->first()->question)->toBe('Q2');
});

// Contact Inquiries

test('contact inquiry defaults to unread', function () {
    $inquiry = ContactInquiry::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Question',
        'message' => 'Hello',
    ]);

    expect($inquiry->status)->toBe('unread');
});

test('unread scope returns only unread inquiries', function () {
    ContactInquiry::create(['name' => 'A', 'email' => 'a@a.com', 'subject' => 'S1', 'message' => 'M1', 'status' => 'unread']);
    ContactInquiry::create(['name' => 'B', 'email' => 'b@a.com', 'subject' => 'S2', 'message' => 'M2', 'status' => 'read']);

    expect(ContactInquiry::unread()->count())->toBe(1);
});
