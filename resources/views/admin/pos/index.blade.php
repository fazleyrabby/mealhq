@extends('admin.layout')

@section('title', 'POS - MealHQ')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
    .pos-wrapper { display: flex; height: calc(100vh - 3.5rem); }
    .pos-categories { width: 250px; min-width: 250px; background: #f8f9fa; border-right: 1px solid #e9ecef; display: flex; flex-direction: column; height: 100vh; }
    .pos-products { flex: 1; min-width: 0; display: flex; flex-direction: column; height: 100vh; background: #fff; }
    .pos-cart { width: 380px; min-width: 380px; background: #f8f9fa; border-left: 1px solid #e9ecef; display: flex; flex-direction: column; height: 100vh; }
    .cat-btn { display: flex; align-items: center; gap: .5rem; padding: .625rem 1rem; border: none; background: transparent; color: #495057; width: 100%; text-align: left; border-radius: 0; font-size: .875rem; cursor: pointer; }
    .cat-btn:hover { background: rgba(0,0,0,.03); color: #1a1d21; }
    .cat-btn.active, .cat-btn.active:hover { background: var(--tblr-primary, #206bc4); color: #fff; }
    .cat-count { margin-left: auto; font-size: .75rem; opacity: .6; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: .75rem; padding: 1rem; align-content: start; }
    .product-card { background: #fff; border: 1px solid #e9ecef; border-radius: .5rem; overflow: hidden; cursor: pointer; transition: border-color .15s, box-shadow .15s; }
    .product-card:hover { border-color: var(--tblr-primary, #206bc4); box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    .product-card.out-of-stock { opacity: .5; pointer-events: none; }
    .product-img { width: 100%; height: 110px; object-fit: cover; background: #f0f0f0; display: block; }
    .qty-badge { position: absolute; top: .375rem; right: .375rem; background: var(--tblr-primary, #206bc4); color: #fff; width: 1.375rem; height: 1.375rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .6875rem; font-weight: 700; }
    .cart-item { padding: .625rem 1rem; border-bottom: 1px solid #e9ecef; }
    .cart-body { flex: 1; overflow-y: auto; }
    .btn-qty { width: 1.75rem; height: 1.75rem; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; padding: 0; font-size: .8125rem; line-height: 1; }
    .tab-btn { padding: .375rem .75rem; border: 1px solid transparent; background: transparent; color: #6c757d; font-size: .8125rem; border-radius: .375rem; cursor: pointer; }
    .tab-btn:hover { border-color: #dee2e6; color: #1a1d21; }
    .tab-btn.active { background: var(--tblr-primary, #206bc4); color: #fff; border-color: var(--tblr-primary, #206bc4); }
    .search-input { background: #fff; border: 1px solid #ced4da; color: #1a1d21; border-radius: .375rem; padding: .4375rem .75rem; font-size: .875rem; width: 100%; outline: none; }
    .search-input:focus { border-color: var(--tblr-primary, #206bc4); box-shadow: 0 0 0 2px rgba(32,107,196,.15); }
    .search-input::placeholder { color: #adb5bd; }
    .modal-mask { position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1055; display: flex; align-items: center; justify-content: center; }
    .modal-panel { background: #fff; border: 1px solid #dee2e6; border-radius: .75rem; max-height: 90vh; overflow-y: auto; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.15); }
    .table-btn { padding: .375rem .5rem; border: 1px solid #dee2e6; border-radius: .375rem; background: #fff; color: #495057; text-align: center; font-size: .75rem; cursor: pointer; min-width: 2.5rem; }
    .table-btn:hover { border-color: var(--tblr-primary, #206bc4); color: var(--tblr-primary, #206bc4); }
    .table-btn.occupied { border-color: #e53935; color: #e53935; }
    .table-btn.selected { background: var(--tblr-primary, #206bc4); color: #fff; border-color: var(--tblr-primary, #206bc4); }
    .table-btn.reserved { border-color: #ff9800; color: #ff9800; }
    .customer-row { padding: .5rem .75rem; cursor: pointer; border-radius: .375rem; color: #495057; }
    .customer-row:hover { background: rgba(0,0,0,.03); }
    .pos-scroll { overflow-y: auto; }
    .pos-scroll::-webkit-scrollbar { width: 6px; }
    .pos-scroll::-webkit-scrollbar-track { background: transparent; }
    .pos-scroll::-webkit-scrollbar-thumb { background: #ced4da; border-radius: 3px; }
    .text-muted-light { color: #6c757d; }
</style>
@endpush

@section('content')
<div x-data="posApp()" x-cloak class="pos-wrapper">

    {{-- ======================== LEFT: CATEGORIES ======================== --}}
    <aside class="pos-categories d-none d-lg-flex flex-column">
        <div class="p-2 border-bottom border-light">
            <input type="text" class="search-input" placeholder="Search categories..." x-model="categorySearch">
        </div>
        <div class="flex-fill pos-scroll py-2">
            <button class="cat-btn" :class="{ active: !activeCategory }" @click="activeCategory = null; filterProducts()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                <span>All Items</span>
                <span class="cat-count">{{ $categories->sum('menu_items_count') }}</span>
            </button>
            <button class="cat-btn" :class="{ active: activeCategory == 'favorites' }" @click="activeCategory = 'favorites'; filterProducts()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Favorites</span>
            </button>
            <button class="cat-btn" :class="{ active: activeCategory == 'bestsellers' }" @click="activeCategory = 'bestsellers'; filterProducts()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9h.01M10 9h.01M14 9h.01M18 9h.01"/><path d="M4 5h16v4l-3 4-3-4 3-4"/><path d="M4 13h16v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2z"/></svg>
                <span>Best Sellers</span>
            </button>
            <hr class="my-2">
            <template x-for="cat in filteredCategories" :key="cat.id">
                <button class="cat-btn" :class="{ active: activeCategory == cat.id }" @click="activeCategory = cat.id; filterProducts()">
                    <span x-text="cat.name"></span>
                    <span class="cat-count" x-text="cat.menu_items_count"></span>
                </button>
            </template>
        </div>
    </aside>

    {{-- ======================== CENTER: PRODUCTS ======================== --}}
    <main class="pos-products">
        <div class="px-3 py-2 border-bottom border-light bg-white">
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative flex-fill">
                    <svg class="position-absolute top-50 translate-middle-y ms-2" width="16" height="16" fill="none" stroke="#adb5bd" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" class="search-input ps-4" placeholder="Search products... (F2)" x-model="searchQuery" @input.debounce.300ms="filterProducts()" id="pos-search">
                </div>
                <button class="btn btn-sm btn-outline-secondary" @click="newOrder" title="New Order">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                </button>
            </div>
        </div>
        <div class="flex-fill pos-scroll" id="products-container">
            <div class="product-grid">
                <template x-for="item in products" :key="item.id">
                    <div class="product-card" @click="openItemModal(item)">
                        <div class="position-relative">
                            <img :src="item.image_url || 'https://placehold.co/300x200/f0f0f0/9ba0a6?text=' + encodeURIComponent(item.name[0])" :alt="item.name" class="product-img" loading="lazy">
                            <div x-show="item.qty > 0" class="qty-badge" x-text="item.qty"></div>
                        </div>
                        <div class="p-2">
                            <div class="fw-semibold text-truncate" style="font-size:.875rem;color:#1a1d21" x-text="item.name"></div>
                            <div class="text-muted small text-truncate" style="font-size:.75rem" x-text="item.category_name"></div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span style="font-weight:600;font-size:.875rem;color:#1a1d21">$<span x-text="Number(item.base_price).toFixed(2)"></span></span>
                                <button class="btn btn-sm btn-primary rounded-circle" style="width:1.625rem;height:1.625rem;padding:0;font-size:.8125rem;line-height:1" @click.stop="quickAdd(item)" title="Add">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="!products.length" class="col-12 text-center text-muted py-5 small">No products found</div>
            </div>
        </div>
    </main>

    {{-- ======================== RIGHT: CART ======================== --}}
    <aside class="pos-cart d-none d-lg-flex flex-column">
        {{-- Order Header --}}
        <div class="px-3 py-2 border-bottom border-light bg-white">
            <div class="d-flex gap-1 mb-2 flex-wrap">
                <template x-for="t in ['dine_in','takeaway','delivery']" :key="t">
                    <button class="tab-btn" :class="{ active: orderType == t }" @click="orderType = t" x-text="t.replace('_',' ').replace(/\b\w/g, l => l.toUpperCase())"></button>
                </template>
            </div>
            <template x-if="orderType == 'dine_in'">
                <div class="mb-2">
                    <button class="btn btn-sm btn-outline-secondary w-100" @click="showTablePicker = !showTablePicker" style="font-size:.8125rem">
                        <span x-text="selectedTable ? 'Table ' + selectedTable.table_number : 'Select Table (F4)'"></span>
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="ms-1"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <template x-if="showTablePicker">
                        <div class="mt-1 p-2 border rounded pos-scroll" style="max-height:10rem;background:#fff">
                            <template x-for="(tables, zone) in tableMap" :key="zone">
                                <div class="mb-1">
                                    <div class="small text-muted-light mb-1" x-text="zone"></div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <template x-for="t in tables" :key="t.id">
                                            <button class="table-btn" :class="{ occupied: t.status == 'occupied', selected: selectedTable?.id == t.id, reserved: t.status == 'reserved' }" @click="selectTable(t)" x-text="t.table_number"></button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
            <div>
                <div class="position-relative">
                    <input type="text" class="search-input" placeholder="Search customer... (F3)" x-model="customerQuery" @input.debounce.300ms="searchCustomers" @keydown.escape="customerResults = []">
                    <template x-if="customerResults.length">
                        <div class="position-absolute top-100 start-0 end-0 mt-1 border rounded" style="background:#fff;z-index:20;max-height:10rem;overflow-y:auto">
                            <template x-for="c in customerResults" :key="c.id">
                                <div class="customer-row d-flex align-items-center gap-2" @click="selectCustomer(c)">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-8 8-8s8 4 8 8"/></svg>
                                    <div>
                                        <div class="small fw-semibold" x-text="c.name"></div>
                                        <div class="text-muted small" style="font-size:.6875rem" x-text="c.email || c.phone"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                <div x-show="selectedCustomer" class="d-flex align-items-center gap-1 mt-1 small text-muted-light">
                    <span x-text="selectedCustomer.name"></span>
                    <button class="btn btn-sm p-0 ms-auto text-muted border-0 bg-transparent" @click="selectedCustomer = null; customerQuery = ''">&times;</button>
                </div>
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="cart-body pos-scroll bg-white">
            <template x-for="(item, idx) in cart" :key="idx">
                <div class="cart-item">
                    <div class="d-flex align-items-start gap-2">
                        <img :src="item.image_url || 'https://placehold.co/40x40/f0f0f0/9ba0a6?text=' + encodeURIComponent(item.item_name[0])" class="rounded" style="width:2rem;height:2rem;object-fit:cover;background:#f0f0f0;flex-shrink:0" loading="lazy">
                        <div class="flex-fill min-w-0">
                            <div class="fw-semibold text-truncate" style="font-size:.8125rem;color:#1a1d21" x-text="item.item_name"></div>
                            <template x-if="item.variant_name">
                                <div class="text-muted small" style="font-size:.6875rem" x-text="item.variant_name"></div>
                            </template>
                            <template x-if="item.modifiers?.length">
                                <div class="text-muted small text-truncate" style="font-size:.6875rem" x-text="item.modifiers.map(m => m.item_name).join(', ')"></div>
                            </template>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <div class="d-flex align-items-center gap-1">
                            <button class="btn btn-sm btn-outline-secondary btn-qty" @click="updateQty(idx, -1)" :disabled="item.qty <= 1">&minus;</button>
                            <span class="fw-semibold" style="font-size:.8125rem;min-width:1.25rem;text-align:center;color:#1a1d21" x-text="item.qty"></span>
                            <button class="btn btn-sm btn-outline-secondary btn-qty" @click="updateQty(idx, 1)">+</button>
                        </div>
                        <span class="fw-semibold" style="font-size:.8125rem;color:#1a1d21">$<span x-text="itemLineTotal(item).toFixed(2)"></span></span>
                        <button class="btn btn-sm p-0 text-danger border-0 bg-transparent" @click="removeItem(idx)" style="font-size:1rem">&times;</button>
                    </div>
                </div>
            </template>
            <div x-show="!cart.length" class="text-center text-muted py-5 small">Cart is empty</div>
        </div>

        {{-- Totals --}}
        <div class="px-3 py-2 border-top border-light bg-white">
            <div class="d-flex justify-content-between small mb-1"><span class="text-muted-light">Subtotal</span><span style="color:#1a1d21">$<span x-text="subtotal.toFixed(2)"></span></span></div>
            <div class="d-flex justify-content-between small mb-1"><span class="text-muted-light">Tax (<span x-text="taxRate"></span>%)</span><span style="color:#1a1d21">$<span x-text="taxAmount.toFixed(2)"></span></span></div>
            <div class="d-flex justify-content-between small mb-1"><span class="text-muted-light">Service</span><span style="color:#1a1d21">$<span x-text="serviceCharge.toFixed(2)"></span></span></div>
            <div class="d-flex justify-content-between small mb-1"><span class="text-muted-light">Discount</span><span style="color:#e53935">-$<span x-text="discountAmount.toFixed(2)"></span></span></div>
            <hr class="my-1">
            <div class="d-flex justify-content-between fw-bold" style="font-size:1.125rem;color:#1a1d21"><span>Total</span><span>$<span x-text="grandTotal.toFixed(2)"></span></span></div>
        </div>

        {{-- Discount Input --}}
        <div class="px-3 py-1 bg-white">
            <template x-if="showDiscount">
                <div class="input-group input-group-sm mb-1">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control form-control-sm" placeholder="Discount" x-model="discountAmount" min="0" :max="subtotal">
                    <button class="btn btn-sm btn-outline-secondary" @click="showDiscount = false">Apply</button>
                </div>
            </template>
        </div>

        {{-- Action Buttons --}}
        <div class="px-3 py-1 d-flex gap-2 bg-white">
            <button class="btn btn-sm btn-outline-secondary flex-fill" @click="showDiscount = !showDiscount">Discount (F5)</button>
            <button class="btn btn-sm btn-outline-secondary flex-fill" @click="clearCart">Clear</button>
        </div>

        {{-- Payment Button --}}
        <div class="px-3 py-2 mt-auto bg-white">
            <button class="btn btn-lg w-100" :class="cart.length ? 'btn-primary' : 'btn-secondary'" :disabled="!cart.length" @click="openPaymentModal()">
                <span class="fw-bold">Place Order</span>
                <span class="ms-2">$<span x-text="grandTotal.toFixed(2)"></span></span>
            </button>
        </div>
    </aside>

    {{-- ======================== MODIFIER MODAL ======================== --}}
    <template x-if="showModifierModal && selectedItem">
        <div class="modal-mask" @click.self="closeModifierModal">
            <div class="modal-panel" style="max-width:24rem">
                <div class="px-3 py-2 border-bottom d-flex align-items-center gap-2">
                    <img :src="selectedItem.image_url || 'https://placehold.co/64x64/f0f0f0/9ba0a6?text=' + encodeURIComponent(selectedItem.name[0])" class="rounded" style="width:3.5rem;height:3.5rem;object-fit:cover;background:#f0f0f0;flex-shrink:0">
                    <div class="flex-fill">
                        <div class="fw-semibold" style="color:#1a1d21" x-text="selectedItem.name"></div>
                        <div class="text-muted small">$<span x-text="Number(selectedItem.base_price).toFixed(2)"></span></div>
                    </div>
                    <button class="btn btn-sm btn-ghost-dark" @click="closeModifierModal" style="font-size:1.25rem">&times;</button>
                </div>
                <div class="p-3" style="max-height:50vh;overflow-y:auto">
                    <template x-if="selectedItem.variants?.length">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Variant</label>
                            <template x-for="v in selectedItem.variants" :key="v.id">
                                <label class="d-flex align-items-center gap-2 px-2 py-1 rounded" style="cursor:pointer" :class="{ 'bg-light': selectedVariant?.id == v.id }">
                                    <input type="radio" :value="v.id" :checked="selectedVariant?.id == v.id" @change="selectedVariant = v" name="variant" class="form-check-input">
                                    <span class="small" x-text="v.name"></span>
                                    <span x-show="v.price_adjustment" class="ms-auto text-muted small" x-text="(v.price_adjustment > 0 ? '+' : '') + Number(v.price_adjustment).toFixed(2)"></span>
                                </label>
                            </template>
                        </div>
                    </template>
                    <template x-for="group in modifierGroups" :key="group.id">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                <span x-text="group.name"></span>
                                <span x-show="group.min_selections > 0" class="text-danger"> *</span>
                            </label>
                            <template x-for="mi in group.items" :key="mi.id">
                                <label class="d-flex align-items-center gap-2 px-2 py-1 rounded" style="cursor:pointer" :class="{ 'bg-light': isModifierSelected(group.id, mi.id) }">
                                    <template x-if="group.type == 'select_one' || group.type == 'required_one'">
                                        <input type="radio" :name="'mod_' + group.id" :value="mi.id" :checked="isModifierSelected(group.id, mi.id)" @change="toggleModifier(group, mi, true)" class="form-check-input">
                                    </template>
                                    <template x-if="group.type == 'select_multiple' || group.type == 'required_multiple'">
                                        <input type="checkbox" :value="mi.id" :checked="isModifierSelected(group.id, mi.id)" @change="toggleModifier(group, mi)" class="form-check-input">
                                    </template>
                                    <span class="small" x-text="mi.name"></span>
                                    <span x-show="mi.price_adjustment" class="ms-auto text-muted small" x-text="(mi.price_adjustment > 0 ? '+' : '') + Number(mi.price_adjustment).toFixed(2)"></span>
                                </label>
                            </template>
                        </div>
                    </template>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Special Instructions</label>
                        <textarea class="form-control form-control-sm" rows="2" x-model="specialInstructions" placeholder="Any special requests..."></textarea>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="form-label small fw-semibold mb-0">Qty</label>
                        <button class="btn btn-sm btn-outline-secondary btn-qty" @click="itemQty = Math.max(1, itemQty - 1)">&minus;</button>
                        <span class="fw-bold" style="min-width:1.5rem;text-align:center;color:#1a1d21" x-text="itemQty"></span>
                        <button class="btn btn-sm btn-outline-secondary btn-qty" @click="itemQty++">+</button>
                        <span class="ms-auto fw-bold" style="color:#1a1d21">$<span x-text="modifierTotalPrice.toFixed(2)"></span></span>
                    </div>
                </div>
                <div class="px-3 py-2 border-top d-flex gap-2">
                    <button class="btn btn-secondary flex-fill" @click="closeModifierModal">Cancel</button>
                    <button class="btn btn-primary flex-fill" @click="addToCart">Add to Order</button>
                </div>
            </div>
        </div>
    </template>

    {{-- ======================== PAYMENT MODAL ======================== --}}
    <template x-if="showPaymentModal">
        <div class="modal-mask" @click.self="showPaymentModal = false">
            <div class="modal-panel" style="max-width:28rem">
                <div class="px-3 py-2 border-bottom d-flex align-items-center">
                    <h5 class="mb-0" style="font-size:1rem">Complete Order</h5>
                    <button class="btn btn-sm ms-auto" @click="showPaymentModal = false" style="font-size:1.25rem">&times;</button>
                </div>
                <div class="p-3">
                    <div class="mb-3 text-center">
                        <div class="text-muted small">Grand Total</div>
                        <div class="fw-bold" style="font-size:1.75rem;color:#1a1d21">$<span x-text="grandTotal.toFixed(2)"></span></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Payment Method</label>
                        <div class="d-flex flex-wrap gap-1">
                            <template x-for="method in ['Cash', 'Card', 'bKash', 'Nagad', 'SSLCommerz']" :key="method">
                                <button class="btn btn-sm" :class="paymentMethod == method ? 'btn-primary' : 'btn-outline-secondary'" @click="paymentMethod = method" x-text="method"></button>
                            </template>
                        </div>
                    </div>
                    <template x-if="paymentMethod == 'Cash'">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Amount Received</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" x-model="amountReceived" min="0">
                            </div>
                            <div x-show="amountReceived >= grandTotal" class="mt-1 text-success small fw-semibold">
                                Change: $<span x-text="(amountReceived - grandTotal).toFixed(2)"></span>
                            </div>
                        </div>
                    </template>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="print-receipt" x-model="printReceipt">
                            <label class="form-check-label small" for="print-receipt">Print Receipt</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="send-kitchen" x-model="sendToKitchen">
                            <label class="form-check-label small" for="send-kitchen">Send to Kitchen</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-secondary flex-fill" @click="showPaymentModal = false">Cancel</button>
                        <button class="btn btn-primary flex-fill" @click="submitOrder" :disabled="submitting">
                            <span x-show="!submitting">Complete Order</span>
                            <span x-show="submitting" class="spinner-border spinner-border-sm"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ======================== SUCCESS TOAST ======================== --}}
    <div x-show="showSuccess" x-transition.duration.300ms class="position-fixed bottom-0 end-0 p-3" style="z-index:1060">
        <div class="alert alert-success d-flex align-items-center gap-2 mb-0 shadow-lg">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
            <span x-text="successMessage"></span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function posApp() {
    return {
        categories: {{ Js::from($categories->toArray()) }},
        allProducts: {{ Js::from($allItems->toArray()) }},
        modifierGroupsData: {{ Js::from($modifierGroups->toArray()) }},
        tableMap: {{ Js::from($tables->toArray()) }},
        taxRates: {{ Js::from([['rate' => $taxRate?->rate ?? 0]]) }},
        serviceChargeRate: {{ $serviceChargeRate }},

        activeCategory: null,
        categorySearch: '',
        searchQuery: '',
        products: [],

        // Cart state (persisted to server session)
        cart: [],
        orderType: 'dine_in',
        selectedTable: null,
        showTablePicker: false,
        selectedCustomer: null,
        customerQuery: '',
        customerResults: [],
        discountAmount: 0,
        showDiscount: false,
        loadingCart: false,

        showModifierModal: false,
        selectedItem: null,
        selectedVariant: null,
        selectedModifiers: new Map(),
        specialInstructions: '',
        itemQty: 1,

        showPaymentModal: false,
        paymentMethod: 'Cash',
        amountReceived: 0,
        printReceipt: true,
        sendToKitchen: true,
        submitting: false,

        showSuccess: false,
        successMessage: '',

        get taxRate() { return this.taxRates[0]?.rate || 0; },
        get filteredCategories() {
            if (!this.categorySearch) return this.categories;
            const q = this.categorySearch.toLowerCase();
            return this.categories.filter(c => c.name.toLowerCase().includes(q));
        },
        get modifierGroups() {
            if (!this.selectedItem?.modifier_groups) return [];
            return this.selectedItem.modifier_groups;
        },
        get subtotal() {
            return this.cart.reduce((sum, item) => {
                const modTotal = (item.modifiers || []).reduce((s, m) => s + (Number(m.price_adjustment) || 0), 0);
                return sum + ((Number(item.unit_price) + modTotal) * item.qty);
            }, 0);
        },
        get taxAmount() { return this.subtotal * (this.taxRate / 100); },
        get serviceCharge() { return this.subtotal * (this.serviceChargeRate / 100); },
        get grandTotal() { return Math.max(0, this.subtotal + this.taxAmount + this.serviceCharge - Number(this.discountAmount || 0)); },
        get modifierTotalPrice() {
            if (!this.selectedItem) return 0;
            const base = Number(this.selectedItem.base_price) || 0;
            const vAdj = this.selectedVariant ? Number(this.selectedVariant.price_adjustment || 0) : 0;
            let mAdj = 0;
            this.selectedModifiers.forEach(m => { mAdj += Number(m.price_adjustment || 0); });
            return (base + vAdj + mAdj) * this.itemQty;
        },

        persist() {
            fetch('/admin/pos/cart/sync', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                body: JSON.stringify({
                    items: this.cart,
                    order_type: this.orderType,
                    customer_id: this.selectedCustomer?.id || null,
                    customer_name: this.selectedCustomer?.name || null,
                    table_id: this.selectedTable?.id || null,
                    table_number: this.selectedTable?.table_number || null,
                    discount_amount: this.discountAmount,
                    editing_order_id: this.editingOrderId,
                    editing_order_number: this.editingOrderNumber,
                }),
            }).catch(() => {});
        },

        init() {
            document.querySelector('.page-body')?.classList.add('p-0');
            document.querySelector('.container-xl')?.classList.add('p-0', 'mw-100');
            const header = document.querySelector('.page-header');
            if (header) header.style.display = 'none';

            this.loadingCart = true;
            fetch('/admin/pos/cart')
                .then(r => r.json())
                .then(cart => {
                    this.cart = cart.items || [];
                    this.orderType = cart.order_type || 'dine_in';
                    this.discountAmount = Number(cart.discount_amount || 0);
                    if (cart.customer_id) this.selectedCustomer = { id: cart.customer_id, name: cart.customer_name || 'Customer' };
                    if (cart.table_id) this.selectedTable = { id: cart.table_id, table_number: cart.table_number || 'Table' };
                    this.filterProducts();
                })
                .catch(() => { this.filterProducts(); })
                .finally(() => { this.loadingCart = false; });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'F2') { e.preventDefault(); document.getElementById('pos-search')?.focus(); }
                if (e.key === 'F4') { e.preventDefault(); this.showTablePicker = !this.showTablePicker; }
                if (e.key === 'F5') { e.preventDefault(); this.showDiscount = !this.showDiscount; }
                if (e.key === 'F8') { e.preventDefault(); if (this.cart.length) this.openPaymentModal(); }
                if (e.key === 'Escape') { this.closeModifierModal(); this.showPaymentModal = false; this.showTablePicker = false; }
            });
        },

        filterProducts() {
            let items = [...this.allProducts];
            const q = this.searchQuery.toLowerCase().trim();
            if (q) items = items.filter(i => i.name.toLowerCase().includes(q));
            if (this.activeCategory && !['favorites','bestsellers'].includes(this.activeCategory)) {
                items = items.filter(i => String(i.category_id) === String(this.activeCategory));
            }
            if (this.activeCategory === 'favorites') items = items.filter(i => i.is_featured);
            if (this.activeCategory === 'bestsellers') items = [...items].sort(() => Math.random() - 0.5).slice(0, 20);
            this.products = items.map(i => {
                const ci = this.cart.find(c => c.menu_item_id === i.id);
                return { ...i, qty: ci?.qty || 0, category_name: i.category?.name || '' };
            });
        },

        quickAdd(item) {
            if ((item.modifier_groups && item.modifier_groups.length) || (item.variants && item.variants.length)) {
                this.openItemModal(item);
                return;
            }
            const existing = this.cart.find(i => i.menu_item_id === item.id && !i.variant_name && !i.modifiers.length);
            if (existing) { existing.qty++; }
            else {
                this.cart.push({
                    menu_item_id: item.id, menu_item_variant_id: null,
                    item_name: item.name, variant_name: null,
                    unit_price: Number(item.base_price), qty: 1,
                    modifiers: [], special_instructions: '', image_url: item.image_url || null,
                });
            }
            this.filterProducts();
            this.persist();
        },

        updateQty(idx, delta) {
            if (!this.cart[idx]) return;
            this.cart[idx].qty = Math.max(1, this.cart[idx].qty + delta);
            this.filterProducts();
            this.persist();
        },

        removeItem(idx) {
            this.cart.splice(idx, 1);
            this.filterProducts();
            this.persist();
        },

        itemLineTotal(item) {
            const modTotal = (item.modifiers || []).reduce((s, m) => s + (Number(m.price_adjustment) || 0), 0);
            return (Number(item.unit_price) + modTotal) * item.qty;
        },

        clearCart() {
            if (!this.cart.length) return;
            if (!confirm('Clear all items?')) return;
            this.cart = []; this.selectedTable = null; this.selectedCustomer = null;
            this.discountAmount = 0;
            this.filterProducts();
            this.persist();
        },

        searchCustomers() {
            if (!this.customerQuery.trim()) { this.customerResults = []; return; }
            fetch('/admin/pos/customers?q=' + encodeURIComponent(this.customerQuery))
                .then(r => r.json()).then(data => { this.customerResults = data; })
                .catch(() => { this.customerResults = []; });
        },

        selectCustomer(c) {
            this.selectedCustomer = c; this.customerQuery = ''; this.customerResults = [];
            this.persist();
        },
        selectTable(t) {
            this.selectedTable = this.selectedTable?.id === t.id ? null : t;
            this.persist();
        },

        openItemModal(item) {
            this.selectedItem = item; this.selectedVariant = null;
            this.selectedModifiers = new Map(); this.specialInstructions = '';
            this.itemQty = 1; this.showModifierModal = true;
        },

        closeModifierModal() { this.showModifierModal = false; this.selectedItem = null; },

        toggleModifier(group, mi, isRadio) {
            const key = group.id + '_' + mi.id;
            if (isRadio) {
                for (const k of this.selectedModifiers.keys()) { if (k.startsWith(group.id + '_')) this.selectedModifiers.delete(k); }
                this.selectedModifiers.set(key, { ...mi, group_name: group.name });
            } else {
                if (this.selectedModifiers.has(key)) this.selectedModifiers.delete(key);
                else if (this.selectedModifiers.size < (group.max_selections || 99)) this.selectedModifiers.set(key, { ...mi, group_name: group.name });
            }
            this.selectedModifiers = new Map(this.selectedModifiers);
        },

        isModifierSelected(groupId, itemId) { return this.selectedModifiers.has(groupId + '_' + itemId); },

        addToCart() {
            if (!this.selectedItem) return;
            const mods = [];
            this.selectedModifiers.forEach(m => mods.push({ group_name: m.group_name, item_name: m.name, price_adjustment: Number(m.price_adjustment || 0) }));
            const vAdj = this.selectedVariant ? Number(this.selectedVariant.price_adjustment || 0) : 0;
            this.cart.push({
                menu_item_id: this.selectedItem.id, menu_item_variant_id: this.selectedVariant?.id || null,
                item_name: this.selectedItem.name, variant_name: this.selectedVariant?.name || null,
                unit_price: Number(this.selectedItem.base_price) + vAdj, qty: this.itemQty,
                modifiers: mods, special_instructions: this.specialInstructions,
                image_url: this.selectedItem.image_url || null,
            });
            this.closeModifierModal();
            this.filterProducts();
            this.persist();
        },

        openPaymentModal() { this.paymentMethod = 'Cash'; this.amountReceived = this.grandTotal; this.showPaymentModal = true; },

        newOrder() {
            this.cart = [];
            this.selectedTable = null;
            this.selectedCustomer = null;
            this.discountAmount = 0;
            this.filterProducts();
            this.persist();
        },

        submitOrder() {
            if (this.submitting || !this.cart.length) return;
            this.submitting = true;

            const url = '/admin/pos/order';
            const method = 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                body: JSON.stringify({
                    items: this.cart.map(i => ({
                        menu_item_id: i.menu_item_id, menu_item_variant_id: i.menu_item_variant_id,
                        item_name: i.item_name, variant_name: i.variant_name,
                        unit_price: i.unit_price, quantity: i.qty,
                        special_instructions: i.special_instructions, modifiers: i.modifiers || [],
                    })),
                    customer_id: this.selectedCustomer?.id || null,
                    table_session_id: this.selectedTable?.id || null,
                    type: this.orderType, source: 'pos', notes: '',
                    discount_amount: this.discountAmount,
                }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.successMessage = data.message; this.showSuccess = true;
                    const printed = this.printReceipt && data.order?.id;
                    if (printed) {
                        const fmt = 'thermal';
                        window.open('/admin/pos/order/' + data.order.id + '/receipt?format=' + fmt, '_blank');
                    }
                    // Reset cart + clear session
                    this.cart = []; this.selectedTable = null; this.selectedCustomer = null;
                    this.discountAmount = 0; this.showPaymentModal = false;
                    this.filterProducts();
                    this.persist();
                    setTimeout(() => { this.showSuccess = false; }, 4000);
                } else { alert(data.message || 'Error'); }
            })
            .catch(err => { console.error(err); alert('Error placing order'); })
            .finally(() => { this.submitting = false; });
        },
    };
}
</script>
@endpush
