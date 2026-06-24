<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('welcome') }}" class="app-brand-link">
            <img src="{{ asset('assets/img/favicon/heartfit_logo.png') }}" width="180px" alt="HeartFit">
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $isAuth = auth()->check();
        $role   = $isAuth ? auth()->user()->role ?? null : null;
    @endphp

    <ul class="menu-inner py-1">

        {{-- ── Dashboard ── --}}
        <li class="menu-item {{ request()->routeIs('dashboard.admin','dashboard.customer','ahli_gizi.orders','ahli_gizi.orders.show') ? 'active' : '' }}">
            @if ($isAuth && $role === 'ahli_gizi')
                <a href="{{ route('ahli_gizi.orders') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Dashboard</div>
                </a>
            @elseif ($isAuth && in_array($role, ['admin','superadmin','bendahara','medical_record','kurir']))
                <a href="{{ route('dashboard.admin') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Dashboard</div>
                </a>
            @else
                <a href="{{ route('dashboard.customer') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Dashboard</div>
                </a>
            @endif
        </li>

        {{-- ══════════════════════════════════════
             SUPERADMIN / ADMIN
        ══════════════════════════════════════ --}}
        @if ($isAuth && in_array($role, ['admin','superadmin']))

            {{-- Data Users (superadmin only) --}}
            @if ($role === 'superadmin')
                <li class="menu-header small text-uppercase"><span class="menu-header-text">Data Users</span></li>
                <li class="menu-item {{ request()->routeIs('admin.data.petugas*','admin.data.customers*','admin.data.customer*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-group"></i>
                        <div>Data User</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('admin.data.petugas*') ? 'active' : '' }}">
                            <a href="{{ route('admin.data.petugas') }}" class="menu-link">
                                <div>Petugas / Admin</div>
                            </a>
                        </li>
                        <li class="menu-item {{ request()->routeIs('admin.data.customers*','admin.data.customer*') ? 'active' : '' }}">
                            <a href="{{ route('admin.data.customers') }}" class="menu-link">
                                <div>Customers</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Package & Menu --}}
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Produk</span></li>
            <li class="menu-item {{ request()->routeIs('admin.packageType*','admin.mealPackage*','admin.menuMakanan*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-bowl-hot"></i>
                    <div>Paket & Menu</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.packageType*') ? 'active' : '' }}">
                        <a href="{{ route('admin.packageType') }}" class="menu-link"><div>Tipe Paket</div></a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.mealPackage*') ? 'active' : '' }}">
                        <a href="{{ route('admin.mealPackage') }}" class="menu-link"><div>Meal Package</div></a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('admin.menuMakanan*') ? 'active' : '' }}">
                        <a href="{{ route('admin.menuMakanan') }}" class="menu-link"><div>Menu Makanan</div></a>
                    </li>
                </ul>
            </li>

            {{-- Orders --}}
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Transaksi</span></li>
            <li class="menu-item {{ request()->routeIs('admin.orders*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-receipt"></i>
                    <div>Orders</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.index') }}" class="menu-link"><div>List Orders</div></a>
                    </li>
                    {{-- <li class="menu-item {{ request()->routeIs('admin.orders.report') ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.report') }}" target="_blank" class="menu-link"><div>Laporan PDF</div></a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.orders.export') }}" class="menu-link"><div>Export Excel</div></a>
                    </li> --}}
                </ul>
            </li>

            {{-- Pengaturan (superadmin only) --}}
            @if ($role === 'superadmin')
                <li class="menu-header small text-uppercase"><span class="menu-header-text">Sistem</span></li>
                <li class="menu-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <a href="{{ route('admin.settings') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-cog"></i>
                        <div>Pengaturan</div>
                    </a>
                </li>
            @endif
        @endif

        {{-- ══════════════════════════════════════
             MEDICAL RECORD
        ══════════════════════════════════════ --}}
        @if ($isAuth && $role === 'medical_record')
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Data</span></li>
            <li class="menu-item {{ request()->routeIs('admin.data.customers*','admin.data.customer*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-group"></i>
                    <div>Customers</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.data.customers*','admin.data.customer*') ? 'active' : '' }}">
                        <a href="{{ route('admin.data.customers') }}" class="menu-link"><div>List & Detail</div></a>
                    </li>
                </ul>
            </li>
        @endif

        {{-- ══════════════════════════════════════
             BENDAHARA
        ══════════════════════════════════════ --}}
        @if ($isAuth && $role === 'bendahara')
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Transaksi</span></li>
            <li class="menu-item {{ request()->routeIs('admin.orders*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-receipt"></i>
                    <div>Orders</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.orders.index') }}" class="menu-link"><div>List Orders</div></a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admin.orders.export') }}" class="menu-link"><div>Export Excel</div></a>
                    </li>
                </ul>
            </li>
        @endif

        {{-- ══════════════════════════════════════
             AHLI GIZI
        ══════════════════════════════════════ --}}
        @if ($isAuth && $role === 'ahli_gizi')
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Data</span></li>
            <li class="menu-item {{ request()->routeIs('admin.data.customers*','admin.data.customer*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-group"></i>
                    <div>Customers</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('admin.data.customers*','admin.data.customer*') ? 'active' : '' }}">
                        <a href="{{ route('admin.data.customers') }}" class="menu-link"><div>List Customers</div></a>
                    </li>
                </ul>
            </li>
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Transaksi</span></li>
            <li class="menu-item {{ request()->routeIs('ahli_gizi.orders*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-receipt"></i>
                    <div>Orders</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('ahli_gizi.orders') ? 'active' : '' }}">
                        <a href="{{ route('ahli_gizi.orders') }}" class="menu-link"><div>List Orders Personal</div></a>
                    </li>
                </ul>
            </li>
        @endif

        {{-- ══════════════════════════════════════
             CUSTOMER
        ══════════════════════════════════════ --}}
        @if ($isAuth && $role === 'customer')

            <li class="menu-header small text-uppercase"><span class="menu-header-text">Pemesanan</span></li>

            {{-- Buat Order --}}
            <li class="menu-item {{ request()->routeIs('orders.create') ? 'active' : '' }}">
                <a href="{{ route('orders.create') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-plus-circle"></i>
                    <div>Pesan Sekarang</div>
                </a>
            </li>

            {{-- Riwayat Pemesanan --}}
            <li class="menu-item {{ request()->routeIs('customer.orders.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-history"></i>
                    <div>Riwayat Pemesanan</div>
                    @php
                        $unpaidCount = \App\Models\Order::where('user_id', auth()->id())
                            ->where('status','UNPAID')->count();
                    @endphp
                    @if($unpaidCount > 0)
                        <span class="badge bg-danger rounded-pill ms-auto">{{ $unpaidCount }}</span>
                    @endif
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('customer.orders.index') && !request('status') ? 'active' : '' }}">
                        <a href="{{ route('customer.orders.index') }}" class="menu-link">
                            <div>Semua Order</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('customer.orders.index') && request('status') === 'UNPAID' ? 'active' : '' }}">
                        <a href="{{ route('customer.orders.index', ['status' => 'UNPAID']) }}" class="menu-link">
                            <div>Belum Dibayar</div>
                            @if($unpaidCount > 0)
                                <span class="badge bg-warning text-dark rounded-pill ms-auto">{{ $unpaidCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('customer.orders.index') && request('status') === 'PAID' ? 'active' : '' }}">
                        <a href="{{ route('customer.orders.index', ['status' => 'PAID']) }}" class="menu-link">
                            <div>Sudah Dibayar</div>
                        </a>
                    </li>
                </ul>
            </li>

        @endif

    </ul>
</aside>
