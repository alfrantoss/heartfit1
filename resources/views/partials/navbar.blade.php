<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        {{-- Greeting --}}
        <div class="navbar-nav align-items-center flex-grow-1">
            @auth
            @php
                $hour     = now('Asia/Jakarta')->hour;
                $greeting = $hour < 12 ? '🌅 Selamat Pagi' : ($hour < 17 ? '☀️ Selamat Siang' : '🌙 Selamat Sore');
                $activeOrder = null;
                if (auth()->user()->role === 'customer') {
                    $activeOrder = \App\Models\Order::where('user_id', auth()->id())
                        ->whereIn('status', ['PAID','SETTLEMENT'])
                        ->whereDate('end_date', '>=', now())
                        ->first();
                }
            @endphp
            <div class="d-none d-xl-flex flex-column">
                <span class="fw-semibold" style="font-size:14px;">
                    {{ $greeting }}, <span class="text-primary">{{ auth()->user()->name }}</span>
                </span>
                @if($activeOrder)
                    @php $endFmt = \Carbon\Carbon::parse($activeOrder->end_date)->locale('id')->isoFormat('D MMM Y'); @endphp
                    <span class="text-muted" style="font-size:11px;">
                        <i class="bx bx-calendar-check text-primary me-1"></i>
                        Aktif s/d {{ $endFmt }}
                    </span>
                @endif
            </div>
            @endauth
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto gap-1">

            {{-- Notifikasi & unpaid — khusus customer --}}
            @auth
            @if(auth()->user()->role === 'customer')
                @php
                    $unpaidCt     = \App\Models\Order::where('user_id', auth()->id())->where('status','UNPAID')->count();
                    $unreadNotifs = auth()->user()->unreadNotifications()->latest()->take(10)->get();
                    $unreadCount  = auth()->user()->unreadNotifications()->count();
                @endphp

                {{-- Bell notif dropdown --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow position-relative"
                       href="javascript:void(0)" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-bell bx-sm"></i>
                        @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              style="font-size:9px;padding:3px 5px;">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-0"
                        style="min-width:320px;max-height:420px;overflow-y:auto;border-radius:10px;">
                        {{-- Header --}}
                        <li class="dropdown-header d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <span class="fw-bold" style="font-size:14px;">
                                <i class="bx bx-bell me-1"></i>Notifikasi
                            </span>
                            @if($unreadCount > 0)
                            <button onclick="markAllRead()" class="btn btn-xs btn-link p-0 text-muted" style="font-size:12px;">
                                Tandai semua dibaca
                            </button>
                            @endif
                        </li>
                        @forelse($unreadNotifs as $notif)
                        @php $d = $notif->data; @endphp
                        <li>
                            <a class="dropdown-item py-2 px-3 {{ !$notif->read_at ? 'bg-light' : '' }}"
                               href="{{ $d['url'] ?? '#' }}"
                               onclick="markRead('{{ $notif->id }}')"
                               style="white-space:normal;border-bottom:1px solid #f0f0f0;">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="avatar flex-shrink-0"
                                         style="width:34px;height:34px;border-radius:8px;background:#e7e7ff;display:flex;align-items:center;justify-content:center;">
                                        <i class="bx {{ $d['icon'] ?? 'bx-bell' }} text-primary"></i>
                                    </div>
                                    <div style="min-width:0;flex:1;">
                                        <div class="fw-semibold" style="font-size:13px;line-height:1.3;">{{ $d['title'] ?? 'Notifikasi' }}</div>
                                        <div class="text-muted" style="font-size:12px;white-space:normal;line-height:1.4;">{{ $d['message'] ?? '' }}</div>
                                        <div class="text-muted" style="font-size:11px;margin-top:3px;">
                                            <i class="bx bx-time-five me-1"></i>{{ $notif->created_at->locale('id')->diffForHumans() }}
                                        </div>
                                    </div>
                                    @if(!$notif->read_at)
                                    <span style="width:8px;height:8px;border-radius:50%;background:#696cff;flex-shrink:0;margin-top:5px;"></span>
                                    @endif
                                </div>
                            </a>
                        </li>
                        @empty
                        <li class="text-center py-4 px-3 text-muted" style="font-size:13px;">
                            <i class="bx bx-bell-off fs-3 d-block mb-2 opacity-50"></i>
                            Belum ada notifikasi
                        </li>
                        @endforelse
                        <li class="dropdown-footer border-top">
                            <a href="{{ route('customer.orders.index') }}" class="dropdown-item text-center text-primary py-2" style="font-size:13px;">
                                Lihat semua order
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Unpaid badge --}}
                @if($unpaidCt > 0)
                <li class="nav-item">
                    <a href="{{ route('customer.orders.index', ['status'=>'UNPAID']) }}"
                       class="nav-link position-relative" title="{{ $unpaidCt }} pesanan belum dibayar">
                        <i class="bx bx-credit-card bx-sm"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"
                              style="font-size:9px;">{{ $unpaidCt }}</span>
                    </a>
                </li>
                @endif
            @endif
            @endauth

            {{-- User dropdown --}}
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center gap-2"
                   href="javascript:void(0);" data-bs-toggle="dropdown">
                    {{-- Avatar bulat standard Sneat --}}
                    <div class="avatar avatar-online">
                        <span class="avatar-initial rounded-circle bg-label-primary" style="font-size:14px;font-weight:600;">
                            @auth{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}@endauth
                        </span>
                    </div>
                    <div class="d-none d-xl-block">
                        @auth
                        <div style="font-size:13px;font-weight:600;line-height:1.2;">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size:11px;text-transform:capitalize;">{{ auth()->user()->role }}</div>
                        @endauth
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-label-primary" style="font-size:14px;font-weight:600;">
                                            @auth{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}@endauth
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    @auth
                                    <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                                    <small class="text-muted text-capitalize">{{ auth()->user()->role }}</small>
                                    @endauth
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>

                    {{-- Customer: profil --}}
                    @auth
                    @if(auth()->user()->role === 'customer')
                        @php $ud = auth()->user()->detail; @endphp
                        @if($ud)
                        <li>
                            <a class="dropdown-item" href="{{ route('customer.data.customer.detail', $ud->id) }}">
                                <i class="bx bx-user me-2"></i> Profil Saya
                            </a>
                        </li>
                        @endif
                    @endif

                    {{-- Superadmin: pengaturan --}}
                    @if(auth()->user()->role === 'superadmin')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.settings') }}">
                                <i class="bx bx-cog me-2"></i> Pengaturan
                            </a>
                        </li>
                    @endif
                    @endauth

                    <li><div class="dropdown-divider"></div></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bx bx-power-off me-2"></i> Log Out
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
