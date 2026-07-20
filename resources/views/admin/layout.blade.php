<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - MealHQ')</title>
    <link href="{{ asset('tabler/css/tabler-vendors.min.css') }}" rel="stylesheet">
    <link href="{{ asset('tabler/css/tabler.min.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="layout-fluid">
    <script src="{{ asset('tabler/js/tabler-theme.min.js') }}"></script>
    <div class="page">

        <!-- Sidebar -->
        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">
                <!-- Mobile toggler -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
                    aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Logo -->
                <div class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none fs-3 fw-bold">MealHQ</a>
                </div>

                <!-- Mobile action icons -->
                <div class="navbar-nav flex-row d-lg-none">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 p-0 px-2" data-bs-toggle="dropdown" aria-label="Open user menu">
                            <span class="avatar avatar-sm">{{ auth()->check() ? substr(auth()->user()->name, 0, 2) : '?' }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <a href="{{ route('admin.settings') }}" class="dropdown-item">Settings</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Navigation menu -->
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <!-- Dashboard -->
                        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        <!-- CMS -->
                        <li class="nav-item dropdown {{ request()->routeIs('admin.cms.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#navbar-cms" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ request()->routeIs('admin.cms.*') ? 'true' : 'false' }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                                </span>
                                <span class="nav-link-title">CMS</span>
                            </a>
                            <ul class="dropdown-menu" data-bs-popper="none">
                                <li><a class="dropdown-item" href="{{ route('admin.cms.pages.index') }}">Pages</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.cms.promotions.index') }}">Promotions</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.cms.faqs.index') }}">FAQs</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.cms.gallery.index') }}">Gallery</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.cms.inquiries.index') }}">Inquiries</a></li>
                            </ul>
                        </li>

                        <!-- Menu -->
                        <li class="nav-item dropdown {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#navbar-menu" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ request()->routeIs('admin.menu.*') ? 'true' : 'false' }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
                                </span>
                                <span class="nav-link-title">Menu</span>
                            </a>
                            <ul class="dropdown-menu" data-bs-popper="none">
                                <li><a class="dropdown-item" href="{{ route('admin.menu.categories.index') }}">Categories</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.menu.items.index') }}">Menu Items</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.menu.modifiers.index') }}">Modifiers</a></li>
                            </ul>
                        </li>

                        <!-- Inventory -->
                        <li class="nav-item dropdown {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#navbar-inventory" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ request()->routeIs('admin.inventory.*') ? 'true' : 'false' }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                                </span>
                                <span class="nav-link-title">Inventory</span>
                            </a>
                            <ul class="dropdown-menu" data-bs-popper="none">
                                <li><a class="dropdown-item" href="{{ route('admin.inventory.ingredients.index') }}">Ingredients</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.inventory.recipes.index') }}">Recipes</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.inventory.suppliers.index') }}">Suppliers</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.inventory.purchase-orders.index') }}">Purchase Orders</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.inventory.adjustments.index') }}">Stock Adjustments</a></li>
                            </ul>
                        </li>

                        <!-- Orders -->
                        <li class="nav-item {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.pos.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>
                                </span>
                                <span class="nav-link-title">POS</span>
                            </a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.orders.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
                                </span>
                                <span class="nav-link-title">Orders</span>
                            </a>
                        </li>

                        <li class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.reports.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                                </span>
                                <span class="nav-link-title">Reports</span>
                            </a>
                        </li>

                        <!-- Operations -->
                        <li class="nav-item dropdown {{ request()->routeIs('admin.operations.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#navbar-operations" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ request()->routeIs('admin.operations.*') ? 'true' : 'false' }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                </span>
                                <span class="nav-link-title">Operations</span>
                            </a>
                            <ul class="dropdown-menu" data-bs-popper="none">
                                <li><a class="dropdown-item" href="{{ route('admin.operations.tables.index') }}">Tables</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.operations.zones.index') }}">Zones</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.operations.drawers.index') }}">POS Drawers</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.operations.kds.index') }}">KDS Stations</a></li>
                            </ul>
                        </li>

                        <!-- Settings -->
                        <li class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.settings') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                </span>
                                <span class="nav-link-title">Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="page-wrapper">

            <!-- Top header bar with user menu -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">@yield('title', 'Dashboard')</h2>
                        </div>
                        <div class="col-auto ms-auto d-print-none">
                            @auth
                            <div class="btn-list">
                                <div class="dropdown">
                                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                                        <span class="avatar avatar-sm">{{ substr(auth()->user()->name, 0, 2) }}</span>
                                        <div class="d-none d-xl-block ps-2">
                                            <div>{{ auth()->user()->name }}</div>
                                            <div class="mt-1 small text-secondary">{{ auth()->user()->email }}</div>
                                        </div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <a href="{{ route('admin.settings') }}" class="dropdown-item">Settings</a>
                                        <div class="dropdown-divider"></div>
                                        <form method="POST" action="{{ route('admin.logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Logout</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                </div>
                                <div>{{ session('success') }}</div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert"></a>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="icon"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                </div>
                                <div>{{ session('error') }}</div>
                            </div>
                            <a class="btn-close" data-bs-dismiss="alert"></a>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <p class="text-secondary mb-0">&copy; {{ date('Y') }} MealHQ. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('tabler/js/tabler.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
