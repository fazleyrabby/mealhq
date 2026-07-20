@extends('admin.layout')

@section('title', 'POS - MealHQ')

@section('styles')
<style>
    .pos-layout { display: flex; height: calc(100vh - 3.5rem); overflow: hidden; }
    .pos-categories { width: 250px; min-width: 250px; background: #1a1d21; border-right: 1px solid #2a2d31; display: flex; flex-direction: column; }
    .pos-products { flex: 1; overflow: hidden; display: flex; flex-direction: column; min-width: 0; }
    .pos-cart { width: 380px; min-width: 380px; background: #1a1d21; border-left: 1px solid #2a2d31; display: flex; flex-direction: column; }
    .cat-btn { display: flex; align-items: center; gap: .5rem; padding: .625rem 1rem; border: none; background: transparent; color: #9ba0a6; width: 100%; text-align: left; border-radius: 0; transition: all .15s; font-size: .875rem; }
    .cat-btn:hover { background: rgba(255,255,255,.05); color: #fff; }
    .cat-btn.active { background: var(--tblr-primary); color: #fff; }
    .cat-count { margin-left: auto; font-size: .75rem; opacity: .6; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: .75rem; padding: 1rem; overflow-y: auto; align-content: start; }
    .product-card { background: #1a1d21; border: 1px solid #2a2d31; border-radius: .5rem; overflow: hidden; cursor: pointer; transition: all .15s; }
    .product-card:hover { border-color: var(--tblr-primary); transform: translateY(-1px); }
    .product-card.out-of-stock { opacity: .5; pointer-events: none; }
    .product-img { width: 100%; height: 120px; object-fit: cover; background: #2a2d31; }
    .product-badge { position: absolute; top: .5rem; left: .5rem; font-size: .625rem; padding: .125rem .375rem; border-radius: .25rem; font-weight: 600; }
    .qty-badge { position: absolute; top: .5rem; right: .5rem; background: var(--tblr-primary); color: #fff; width: 1.5rem; height: 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: .75rem; font-weight: 700; }
    .cart-item { padding: .75rem 1rem; border-bottom: 1px solid #2a2d31; }
    .cart-item:hover { background: rgba(255,255,255,.02); }
    .modal-mask { position: fixed; inset: 0; background: rgba(0,0,0,.6); z-index: 1050; display: flex; align-items: center; justify-content: center; }
    .modal-panel { background: #1a1d21; border: 1px solid #2a2d31; border-radius: .75rem; max-height: 90vh; overflow-y: auto; }
    .btn-qty { width: 2rem; height: 2rem; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; padding: 0; font-size: .875rem; }
    .table-btn { width: 100%; padding: .75rem .5rem; border: 1px solid #2a2d31; border-radius: .5rem; background: transparent; color: #9ba0a6; text-align: center; transition: all .15s; }
    .table-btn:hover { border-color: var(--tblr-primary); color: #fff; }
    .table-btn.occupied { border-color: #e53935; color: #e53935; }
    .table-btn.selected { border-color: var(--tblr-primary); background: var(--tblr-primary); color: #fff; }
    .table-btn.reserved { border-color: #ff9800; color: #ff9800; }
    .payment-btn { position: sticky; bottom: 1rem; z-index: 10; }
    .cart-body { flex: 1; overflow-y: auto; }
    .tab-btn { padding: .5rem 1rem; border: none; background: transparent; color: #9ba0a6; font-size: .8125rem; border-radius: .375rem; transition: all .15s; }
    .tab-btn:hover { background: rgba(255,255,255,.05); color: #fff; }
    .tab-btn.active { background: var(--tblr-primary); color: #fff; }
    .search-input { background: #2a2d31; border: 1px solid #3a3d41; color: #fff; border-radius: .375rem; padding: .5rem .75rem; font-size: .875rem; width: 100%; }
    .search-input:focus { outline: none; border-color: var(--tblr-primary); }
    .pos-header { padding: .75rem 1rem; border-bottom: 1px solid #2a2d31; display: flex; align-items: center; gap: .75rem; }
    .scrollable-tabs { overflow-x: auto; white-space: nowrap; scrollbar-width: none; -ms-overflow-style: none; }
    .scrollable-tabs::-webkit-scrollbar { display: none; }
    .customer-row { padding: .5rem .75rem; cursor: pointer; border-radius: .375rem; transition: all .1s; }
    .customer-row:hover { background: rgba(255,255,255,.05); }
</style>
@vite(['resources/css/app.css'])
@endsection

@section('content')
<div x-data="posApp()" x-cloak class="pos-layout">

    {{-- ======================== LEFT: CATEGORIES ======================== --}}
    <aside class="pos-categories d-none d-lg-flex flex-column">
        <div class="p-2 border-bottom border-dark">
            <input type="text" class="search-input" placeholder="Search categories..." x-model="categorySearch">
        </div>
        <div class="flex-fill overflow-auto py-2">
            <button class="cat-btn" :class="{ active: !activeCategory }" @click="activeCategory = null; loadProducts()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                <span>All Items</span>
                <span class="cat-count">{{ $categories->sum('menu_items_count') }}</span>
            </button>
            <button class="cat-btn" @click="activeCategory = 'favorites'; loadProducts()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span>Favorites</span>
            </button>
            <button class="cat-btn" @click="activeCategory = 'bestsellers'; loadProducts()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9h.01M10 9h.01M14 9h.01M18 9h.01"/><path d="M4 5h16v4l-3 4-3-4 3-4"/><path d="M4 13h16v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2z"/></svg>
                <span>Best Sellers</span>
            </button>
            <hr class="my-2 border-dark">
            <template x-for="cat in filteredCategories" :key="cat.id">
                <button class="cat-btn" :class="{ active: activeCategory == cat.id }" @click="activeCategory = cat.id; loadProducts()">
                    <span x-text="cat.name"></span>
                    <span class="cat-count" x-text="cat.menu_items_count"></span>
                </button>
            </template>
        </div>
    </aside>

    {{-- ======================== CENTER: PRODUCTS ======================== --}}
    <main class="pos-products">
        {{-- Toolbar --}}
        <div class="pos-header">
            <div class="d-flex gap-2 flex-fill">
                <div class="position-relative flex-fill">
                    <svg class="position-absolute top-50 start-0 translate-middle-y ms-2" width="16" height="16" fill="none" stroke="#9ba0a6" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" class="search-input ps-4" placeholder="Search products... (F2)" x-model="searchQuery" @keydown.window.prevent.f2="$el.focus()" @input.debounce.300ms="loadProducts()" id="pos-search">
                </div>
            </div>
            <button class="btn btn-sm btn-outline-secondary" @click="showOrderType = !showOrderType" title="Order Type">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/><path d="M12 3v6"/></svg>
            </button>
        </div>

        {{-- Product Grid --}}
        <div class="flex-fill overflow-auto" id="products-container" @scroll.throttle.200ms="onProductsScroll">
            <div class="product-grid" id="products-grid">
                <template x-for="item in products" :key="item.id">
                    <div class="product-card position-relative" :class="{ 'out-of-stock': !item.in_stock }" @click="openItemModal(item)">
                        <div class="position-relative">
                            <img :src="item.image_url || '/placeholder.svg'" :alt="item.name" class="product-img" loading="lazy">
                            <div x-show="item.qty > 0" class="qty-badge" x-text="item.qty"></div>
                            <template x-if="item.dietary_badges">
                                <div class="product-badge" x-text="item.dietary_badges"></div>
                            </template>
                        </div>
                        <div class="p-2">
                            <div class="fw-semibold text-truncate" style="color:#e8eaed" x-text="item.name"></div>
                            <div class="text-muted small text-truncate" x-text="item.category_name"></div>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <span style="color:#e8eaed;font-weight:600">$<span x-text="item.base_price.toFixed(2)"></span></span>
                                <button class="btn btn-sm btn-primary rounded-circle" style="width:1.75rem;height:1.75rem;padding:0;font-size:.875rem;line-height:1" @click.stop="quickAdd(item)" title="Add to cart">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="!products.length" class="col-12 text-center text-muted py-5">No products found</div>
            </div>
            <div x-show="loading" class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary"></div>
            </div>
        </div>
    </main>

    {{-- ======================== RIGHT: CART ======================== --}}
    <aside class="pos-cart d-none d-lg-flex flex-column">
        {{-- Order Header --}}
        <div class="p-3 border-bottom border-dark">
            <div class="d-flex gap-2 mb-2 flex-wrap">
                <template x-for="t in ['dine_in','takeaway','delivery']">
                    <button class="tab-btn" :class="{ active: orderType == t }" @click="orderType = t" x-text="t.replace('_',' ').replace(/\b\w/g, l => l.toUpperCase())"></button>
                </template>
            </div>
            <template x-if="orderType == 'dine_in'">
                <div>
                    <button class="btn btn-sm btn-outline-secondary w-100" @click="showTablePicker = !showTablePicker">
                        <span x-text="selectedTable ? 'Table ' + selectedTable.table_number : 'Select Table'"></span>
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="ms-1"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <template x-if="showTablePicker">
                        <div class="mt-2 p-2 border border-dark rounded" style="max-height:200px;overflow-y:auto">
                            <template x-for="(tables, zone) in tableMap" :key="zone">
                                <div class="mb-2">
                                    <div class="small text-muted mb-1" x-text="zone"></div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <template x-for="t in tables">
                                            <button class="table-btn small" :class="{ occupied: t.status == 'occupied', selected: selectedTable?.id == t.id, reserved: t.status == 'reserved' }" @click="selectTable(t)" x-text="t.table_number" style="width:auto;padding:.375rem .625rem;font-size:.75rem"></button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
            <div class="mt-2">
                <div class="position-relative">
                    <input type="text" class="search-input" placeholder="Search customer... (F3)" @keydown.window.prevent.f3="$el.focus()" x-model="customerQuery" @input.debounce.300ms="searchCustomers">
                    <template x-if="customerResults.length">
                        <div class="position-absolute top-100 start-0 end-0 mt-1 border border-dark rounded" style="background:#1a1d21;z-index:20;max-height:200px;overflow-y:auto">
                            <template x-for="c in customerResults" :key="c.id">
                                <div class="customer-row d-flex align-items-center gap-2" @click="selectCustomer(c)">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-8 8-8s8 4 8 8"/></svg>
                                    <div>
                                        <div class="small fw-semibold" x-text="c.name"></div>
                                        <div class="text-muted small" x-text="c.email || c.phone"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                <div x-show="selectedCustomer" class="d-flex align-items-center gap-2 mt-1 small text-muted">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-8 8-8s8 4 8 8"/></svg>
                    <span x-text="selectedCustomer.name"></span>
                    <button class="btn btn-sm p-0 ms-auto" @click="selectedCustomer = null; customerQuery = ''">&times;</button>
                </div>
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="cart-body">
            <template x-for="(item, idx) in cart" :key="idx">
                <div class="cart-item">
                    <div class="d-flex align-items-start gap-2">
                        <img :src="item.image_url || '/placeholder.svg'" class="rounded" style="width:2.5rem;height:2.5rem;object-fit:cover;background:#2a2d31" loading="lazy">
                        <div class="flex-fill min-w-0">
                            <div class="fw-semibold small text-truncate" style="color:#e8eaed" x-text="item.item_name"></div>
                            <template x-if="item.variant_name">
                                <div class="text-muted small" x-text="item.variant_name"></div>
                            </template>
                            <template x-if="item.modifiers?.length">
                                <div class="text-muted small" x-text="item.modifiers.map(m => m.item_name).join(', ')"></div>
                            </template>
                            <template x-if="item.special_instructions">
                                <div class="text-muted small fst-italic" x-text="'Note: ' + item.special_instructions"></div>
                            </template>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <div class="d-flex align-items-center gap-1">
                            <button class="btn btn-sm btn-outline-secondary btn-qty" @click="updateQty(idx, -1)" :disabled="item.qty <= 1">&minus;</button>
                            <span class="fw-semibold small" style="color:#e8eaed;min-width:1.5rem;text-align:center" x-text="item.qty"></span>
                            <button class="btn btn-sm btn-outline-secondary btn-qty" @click="updateQty(idx, 1)">+</button>
                        </div>
                        <span class="fw-semibold small" style="color:#e8eaed">$<span x-text="itemLineTotal(item).toFixed(2)"></span></span>
                        <button class="btn btn-sm p-0 text-danger" @click="cart.splice(idx, 1)">&times;</button>
                    </div>
                </div>
            </template>
            <div x-show="!cart.length" class="text-center text-muted py-5 small">Cart is empty</div>
        </div>

        {{-- Totals --}}
        <div class="p-3 border-top border-dark">
            <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Subtotal</span><span>$<span x-text="subtotal.toFixed(2)"></span></span></div>
            <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Tax (<span x-text="taxRate"></span>%)</span><span>$<span x-text="taxAmount.toFixed(2)"></span></span></div>
            <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Service Charge</span><span>$<span x-text="serviceCharge.toFixed(2)"></span></span></div>
            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Discount</span><span>-$<span x-text="discountAmount.toFixed(2)"></span></span></div>
            <hr class="my-1 border-dark">
            <div class="d-flex justify-content-between fw-bold" style="color:#e8eaed;font-size:1.125rem"><span>Total</span><span>$<span x-text="grandTotal.toFixed(2)"></span></span></div>
        </div>

        {{-- Action Buttons --}}
        <div class="p-3 pt-0 d-flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-outline-secondary" @click="showDiscount = !showDiscount">Discount</button>
            <button class="btn btn-sm btn-outline-secondary" @click="clearCart">Clear</button>
            <template x-if="showDiscount">
                <div class="d-flex gap-1 w-100">
                    <input type="number" step="0.01" class="search-input small" placeholder="Discount $" x-model="discountAmount" min="0" :max="subtotal">
                    <button class="btn btn-sm btn-outline-secondary" @click="showDiscount = false">Apply</button>
                </div>
            </template>
        </div>

        {{-- Payment Button --}}
        <div class="p-3 pt-0 payment-btn">
            <button class="btn btn-lg w-100" :class="cart.length ? 'btn-primary' : 'btn-secondary'" :disabled="!cart.length" @click="openPaymentModal()">
                <span class="fw-bold">Place Order</span>
                <span class="ms-2">$<span x-text="grandTotal.toFixed(2)"></span></span>
            </button>
        </div>
    </aside>

    {{-- ======================== MODIFIER MODAL ======================== --}}
    <template x-if="showModifierModal && selectedItem">
        <div class="modal-mask" @click.self="closeModifierModal">
            <div class="modal-panel" style="width:480px">
                <div class="p-3 border-bottom border-dark d-flex align-items-center gap-3">
                    <img :src="selectedItem.image_url || '/placeholder.svg'" class="rounded" style="width:4rem;height:4rem;object-fit:cover;background:#2a2d31">
                    <div>
                        <h5 class="mb-0" style="color:#e8eaed" x-text="selectedItem.name"></h5>
                        <div class="text-muted small">$<span x-text="selectedItem.base_price.toFixed(2)"></span></div>
                    </div>
                    <button class="btn btn-sm ms-auto" @click="closeModifierModal">&times;</button>
                </div>
                <div class="p-3" style="max-height:50vh;overflow-y:auto">
                    {{-- Variants --}}
                    <template x-if="selectedItem.variants?.length">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Variant</label>
                            <template x-for="v in selectedItem.variants" :key="v.id">
                                <label class="d-flex align-items-center gap-2 p-2 rounded cursor-pointer" style="cursor:pointer" :class="{ 'bg-dark': selectedVariant?.id == v.id }">
                                    <input type="radio" :value="v.id" :checked="selectedVariant?.id == v.id" @change="selectedVariant = v" name="variant" class="form-check-input">
                                    <span x-text="v.name" class="small"></span>
                                    <span x-show="v.price_adjustment" class="ms-auto text-muted small" x-text="(v.price_adjustment > 0 ? '+' : '') + v.price_adjustment.toFixed(2)"></span>
                                </label>
                            </template>
                        </div>
                    </template>

                    {{-- Modifier Groups --}}
                    <template x-for="(group, gi) in modifierGroups" :key="group.id">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                <span x-text="group.name"></span>
                                <span x-show="group.min_selections > 0" class="text-danger"> *</span>
                            </label>
                            <template x-for="mi in group.modifier_items" :key="mi.id">
                                <label class="d-flex align-items-center gap-2 p-2 rounded" style="cursor:pointer" :class="{ 'bg-dark': isModifierSelected(group.id, mi.id) }">
                                    <template x-if="group.type == 'select_one' || group.type == 'required_one'">
                                        <input type="radio" :name="'mod_' + group.id" :value="mi.id" :checked="isModifierSelected(group.id, mi.id)" @change="toggleModifier(group, mi, true)" class="form-check-input">
                                    </template>
                                    <template x-if="group.type == 'select_multiple' || group.type == 'required_multiple'">
                                        <input type="checkbox" :value="mi.id" :checked="isModifierSelected(group.id, mi.id)" @change="toggleModifier(group, mi)" class="form-check-input">
                                    </template>
                                    <span x-text="mi.name" class="small"></span>
                                    <span x-show="mi.price_adjustment" class="ms-auto text-muted small" x-text="(mi.price_adjustment > 0 ? '+' : '') + mi.price_adjustment.toFixed(2)"></span>
                                </label>
                            </template>
                        </div>
                    </template>

                    {{-- Special Instructions --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Special Instructions</label>
                        <textarea class="form-control" rows="2" x-model="specialInstructions" placeholder="Any special requests..."></textarea>
                    </div>

                    {{-- Quantity --}}
                    <div class="d-flex align-items-center gap-3">
                        <label class="form-label small fw-semibold mb-0">Quantity</label>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-secondary btn-qty" @click="itemQty = Math.max(1, itemQty - 1)">&minus;</button>
                            <span class="fw-bold" style="color:#e8eaed" x-text="itemQty"></span>
                            <button class="btn btn-sm btn-outline-secondary btn-qty" @click="itemQty++">+</button>
                        </div>
                        <span class="ms-auto fw-bold" style="color:#e8eaed">$<span x-text="modifierTotalPrice.toFixed(2)"></span></span>
                    </div>
                </div>
                <div class="p-3 border-top border-dark d-flex gap-2">
                    <button class="btn btn-secondary flex-fill" @click="closeModifierModal">Cancel</button>
                    <button class="btn btn-primary flex-fill" @click="addToCart">Add to Order</button>
                </div>
            </div>
        </div>
    </template>

    {{-- ======================== PAYMENT MODAL ======================== --}}
    <template x-if="showPaymentModal">
        <div class="modal-mask" @click.self="showPaymentModal = false">
            <div class="modal-panel" style="width:520px">
                <div class="p-3 border-bottom border-dark d-flex align-items-center">
                    <h5 class="mb-0">Complete Order</h5>
                    <button class="btn btn-sm ms-auto" @click="showPaymentModal = false">&times;</button>
                </div>
                <div class="p-3">
                    <div class="mb-4 text-center">
                        <div class="text-muted small">Grand Total</div>
                        <div class="fw-bold" style="font-size:2rem;color:#e8eaed">$<span x-text="grandTotal.toFixed(2)"></span></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Payment Method</label>
                        <div class="d-flex flex-wrap gap-2">
                            <template x-for="method in ['Cash', 'Card', 'bKash', 'Nagad', 'SSLCommerz']">
                                <button class="btn" :class="paymentMethod == method ? 'btn-primary' : 'btn-outline-secondary'" @click="paymentMethod = method" x-text="method"></button>
                            </template>
                        </div>
                    </div>

                    <template x-if="paymentMethod == 'Cash'">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Amount Received</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" x-model="amountReceived" @input="calculateChange" min="0">
                            </div>
                            <template x-if="amountReceived >= grandTotal">
                                <div class="mt-2 text-success small fw-semibold">Change: $<span x-text="(amountReceived - grandTotal).toFixed(2)"></span></div>
                            </template>
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
    <template x-if="showSuccess">
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
            <div class="alert alert-success d-flex align-items-center gap-2 mb-0">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/></svg>
                <span x-text="successMessage"></span>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
function posApp() {
    return {
        // Data from server
        categories: {{ Js::from($categories->toArray()) }},
        allProducts: {{ Js::from($allItems->toArray()) }},
        modifierGroupsData: {{ Js::from($modifierGroups->toArray()) }},
        tableMap: {{ Js::from($tables->toArray()) }},
        taxRates: {{ Js::from([['rate' => $taxRate?->rate ?? 0]]) }},
        serviceChargeRate: {{ $serviceChargeRate }},

        // State
        activeCategory: null,
        categorySearch: '',
        searchQuery: '',
        products: [],
        loading: false,
        page: 1,
        hasMore: false,

        // Cart
        cart: [],
        orderType: 'dine_in',
        selectedTable: null,
        showTablePicker: false,
        selectedCustomer: null,
        customerQuery: '',
        customerResults: [],
        discountAmount: 0,
        showDiscount: false,

        // Modifier Modal
        showModifierModal: false,
        selectedItem: null,
        selectedVariant: null,
        selectedModifiers: [],
        specialInstructions: '',
        itemQty: 1,

        // Payment Modal
        showPaymentModal: false,
        paymentMethod: 'Cash',
        amountReceived: 0,
        change: 0,
        printReceipt: true,
        sendToKitchen: true,
        submitting: false,

        // Success toast
        showSuccess: false,
        successMessage: '',

        // Computed
        get taxRate() { return this.taxRates[0]?.rate || 0; },
        get filteredCategories() {
            if (!this.categorySearch) return this.categories;
            return this.categories.filter(c => c.name.toLowerCase().includes(this.categorySearch.toLowerCase()));
        },
        get modifierGroups() {
            if (!this.selectedItem?.modifier_groups) return [];
            return this.selectedItem.modifier_groups;
        },
        get subtotal() {
            return this.cart.reduce((sum, item) => {
                const modTotal = (item.modifiers || []).reduce((s, m) => s + (parseFloat(m.price_adjustment) || 0), 0);
                return sum + ((parseFloat(item.unit_price) + modTotal) * item.qty);
            }, 0);
        },
        get taxAmount() { return this.subtotal * (this.taxRate / 100); },
        get serviceCharge() { return this.subtotal * (this.serviceChargeRate / 100); },
        get grandTotal() { return Math.max(0, this.subtotal + this.taxAmount + this.serviceCharge - this.discountAmount); },
        get modifierTotalPrice() {
            const basePrice = this.selectedItem ? parseFloat(this.selectedItem.base_price) : 0;
            const variantAdj = this.selectedVariant ? parseFloat(this.selectedVariant.price_adjustment || 0) : 0;
            const modAdj = Array.from(this.selectedModifiers.values()).reduce((sum, m) => sum + (parseFloat(m.price_adjustment) || 0), 0);
            return (basePrice + variantAdj + modAdj) * this.itemQty;
        },

        init() {
            this.loadProducts();
            // Keyboard shortcut for F8 payment
            document.addEventListener('keydown', (e) => {
                if (e.key === 'F8') { e.preventDefault(); if (this.cart.length) this.openPaymentModal(); }
                if (e.key === 'Escape') { this.closeModifierModal(); this.showPaymentModal = false; }
                if (e.key === 'F4') { e.preventDefault(); this.showTablePicker = !this.showTablePicker; }
                if (e.key === 'F5') { e.preventDefault(); this.showDiscount = !this.showDiscount; }
            });
            // Focus search on load
            setTimeout(() => document.getElementById('pos-search')?.focus(), 100);
        },

        // Products
        loadProducts() {
            this.loading = true;
            this.page = 1;
            const url = new URL('{{ route("admin.pos.search") }}', window.location.origin);
            if (this.searchQuery) url.searchParams.set('q', this.searchQuery);
            if (this.activeCategory && !['favorites','bestsellers'].includes(this.activeCategory)) {
                url.searchParams.set('category_id', this.activeCategory);
            }
            url.searchParams.set('page', this.page);

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data.html, 'text/html');
                    const items = doc.querySelectorAll('.product-card');
                    this.products = [];
                    items.forEach(el => {
                        const match = data.html.match(/x-data="posApp\(\)"/);
                        // We'll rebuild products from allProducts with qty from cart
                    });
                    // Simpler: rebuild from allProducts with current search/category
                    this.rebuildProducts();
                    this.hasMore = data.has_more;
                    this.loading = false;
                })
                .catch(() => {
                    this.rebuildProducts();
                    this.loading = false;
                });
        },

        rebuildProducts() {
            let items = [...this.allProducts];
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                items = items.filter(i => i.name.toLowerCase().includes(q));
            }
            if (this.activeCategory && this.activeCategory !== 'favorites' && this.activeCategory !== 'bestsellers') {
                items = items.filter(i => i.category_id == this.activeCategory);
            }
            if (this.activeCategory === 'favorites') {
                items = items.filter(i => i.is_featured);
            }
            if (this.activeCategory === 'bestsellers') {
                items = items.sort(() => Math.random() - 0.5).slice(0, 20);
            }
            // Map cart qty
            items = items.map(i => {
                const cartItem = this.cart.find(c => c.menu_item_id == i.id);
                return { ...i, qty: cartItem?.qty || 0, image_url: i.image_url || null, category_name: i.category?.name || '', modifier_groups: this.modifierGroupsData.filter(g => i.modifier_group_ids?.includes(g.id)) || [] };
            });
            this.products = items;
        },

        onProductsScroll(e) {
            const el = e.target;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 200 && this.hasMore && !this.loading) {
                this.page++;
                this.loadProducts();
            }
        },

        // Cart
        quickAdd(item) {
            if (item.modifier_groups?.length || item.variants?.length) {
                this.openItemModal(item);
                return;
            }
            const existing = this.cart.find(i => i.menu_item_id == item.id && !i.variant_name);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({
                    menu_item_id: item.id,
                    menu_item_variant_id: null,
                    item_name: item.name,
                    variant_name: null,
                    unit_price: parseFloat(item.base_price),
                    qty: 1,
                    modifiers: [],
                    special_instructions: '',
                    image_url: item.image_url || null,
                });
            }
            this.rebuildProducts();
        },

        updateQty(idx, delta) {
            const item = this.cart[idx];
            if (!item) return;
            item.qty = Math.max(1, item.qty + delta);
            this.rebuildProducts();
        },

        itemLineTotal(item) {
            const modTotal = (item.modifiers || []).reduce((s, m) => s + (parseFloat(m.price_adjustment) || 0), 0);
            return (parseFloat(item.unit_price) + modTotal) * item.qty;
        },

        clearCart() {
            if (this.cart.length && confirm('Clear all items?')) {
                this.cart = [];
                this.selectedTable = null;
                this.selectedCustomer = null;
                this.discountAmount = 0;
                this.rebuildProducts();
            }
        },

        // Customer
        searchCustomers() {
            if (!this.customerQuery) { this.customerResults = []; return; }
            fetch(`{{ route("admin.pos.customers") }}?q=${encodeURIComponent(this.customerQuery)}`)
                .then(r => r.json())
                .then(data => { this.customerResults = data; });
        },

        selectCustomer(c) {
            this.selectedCustomer = c;
            this.customerQuery = '';
            this.customerResults = [];
        },

        // Table
        selectTable(t) {
            this.selectedTable = this.selectedTable?.id == t.id ? null : t;
        },

        // Modifier Modal
        openItemModal(item) {
            this.selectedItem = item;
            this.selectedVariant = null;
            this.selectedModifiers = new Map();
            this.specialInstructions = '';
            this.itemQty = 1;
            this.showModifierModal = true;
        },

        closeModifierModal() {
            this.showModifierModal = false;
            this.selectedItem = null;
        },

        toggleModifier(group, modifierItem, isRadio = false) {
            const key = group.id + '_' + modifierItem.id;
            if (isRadio) {
                // Clear others in group
                for (const [k] of this.selectedModifiers) {
                    if (k.startsWith(group.id + '_')) this.selectedModifiers.delete(k);
                }
                this.selectedModifiers.set(key, { ...modifierItem, group_name: group.name });
            } else {
                if (this.selectedModifiers.has(key)) {
                    this.selectedModifiers.delete(key);
                } else {
                    if (this.selectedModifiers.size >= (group.max_selections || 99)) return;
                    this.selectedModifiers.set(key, { ...modifierItem, group_name: group.name });
                }
            }
            this.selectedModifiers = new Map(this.selectedModifiers);
        },

        isModifierSelected(groupId, itemId) {
            return this.selectedModifiers.has(groupId + '_' + itemId);
        },

        addToCart() {
            if (!this.selectedItem) return;
            const mods = Array.from(this.selectedModifiers.values());
            const variantAdj = this.selectedVariant ? parseFloat(this.selectedVariant.price_adjustment || 0) : 0;
            const basePrice = parseFloat(this.selectedItem.base_price) + variantAdj;

            this.cart.push({
                menu_item_id: this.selectedItem.id,
                menu_item_variant_id: this.selectedVariant?.id || null,
                item_name: this.selectedItem.name,
                variant_name: this.selectedVariant?.name || null,
                unit_price: basePrice,
                qty: this.itemQty,
                modifiers: mods.map(m => ({
                    group_name: m.group_name,
                    item_name: m.name,
                    price_adjustment: parseFloat(m.price_adjustment || 0),
                })),
                special_instructions: this.specialInstructions,
                image_url: this.selectedItem.image_url || null,
            });

            this.closeModifierModal();
            this.rebuildProducts();
        },

        // Payment
        openPaymentModal() {
            this.showPaymentModal = true;
            this.paymentMethod = 'Cash';
            this.amountReceived = this.grandTotal;
        },

        calculateChange() {
            // reactive
        },

        submitOrder() {
            if (this.submitting) return;
            this.submitting = true;

            const items = this.cart.map(i => ({
                menu_item_id: i.menu_item_id,
                menu_item_variant_id: i.menu_item_variant_id,
                item_name: i.item_name,
                variant_name: i.variant_name,
                unit_price: i.unit_price,
                quantity: i.qty,
                special_instructions: i.special_instructions,
                modifiers: i.modifiers || [],
            }));

            fetch('{{ route("admin.pos.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    items,
                    customer_id: this.selectedCustomer?.id || null,
                    table_session_id: this.selectedTable?.id || null,
                    type: this.orderType,
                    source: 'pos',
                    notes: '',
                    discount_amount: this.discountAmount,
                }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.successMessage = data.message;
                    this.showSuccess = true;
                    this.cart = [];
                    this.selectedTable = null;
                    this.selectedCustomer = null;
                    this.discountAmount = 0;
                    this.showPaymentModal = false;
                    this.rebuildProducts();
                    setTimeout(() => { this.showSuccess = false; }, 4000);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error placing order');
            })
            .finally(() => { this.submitting = false; });
        },
    };
}
</script>
@endpush
@endsection
