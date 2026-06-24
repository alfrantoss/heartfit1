@extends('layouts.app')
@section('title', 'Dashboard Pengiriman')

@push('styles')
<style>
.kpi-card { border-radius:12px !important; transition: transform .2s, box-shadow .2s; overflow:hidden; }
.kpi-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.1) !important; }
.kpi-icon { width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:22px; }
.delivery-card { border-radius:12px !important; border:1px solid #e0e0e0 !important; transition: box-shadow .2s; }
.delivery-card:hover { box-shadow:0 4px 18px rgba(0,0,0,.07) !important; }
.progress-track { height:6px; border-radius:3px; background:#e9ecef; overflow:hidden; }
.progress-fill { height:100%; border-radius:3px; transition:width .5s ease; }
.order-row { border-bottom:1px solid #f0f0f0; padding:6px 0; }
.order-row:last-child { border-bottom:none; }
.date-bar { background:#fff; border-radius:12px; padding:14px 18px; box-shadow:0 2px 10px rgba(0,0,0,.05); margin-bottom:20px; }
.notif-bell { position:relative; cursor:pointer; }
.notif-count { position:absolute; top:-4px; right:-4px; background:#dc3545; color:#fff; border-radius:50%; width:16px; height:16px; font-size:9px; display:flex; align-items:center; justify-content:center; font-weight:700; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ── Date filter ── --}}
    <div class="date-bar d-flex flex-wrap align-items-center gap-3 mb-4">
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <div class="text-muted small fw-semibold"><i class="bx bx-calendar text-success me-1"></i>Tanggal:</div>
            <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" style="max-width:160px;">
            <button class="btn btn-sm btn-primary">Tampilkan</button>
            <a href="{{ route('dashboard.admin') }}" class="btn btn-sm btn-outline-secondary">Hari Ini</a>
        </form>

        @if(in_array(auth()->user()->role, config('settings.delivery.generate', [])))
        <form method="POST" action="{{ route('admin.deliveries.generate') }}" class="ms-auto"
              onsubmit="return confirm('Generate delivery untuk {{ $date }}?')">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <button type="submit" class="btn btn-sm btn-success">
                <i class="bx bx-refresh me-1"></i>Generate Delivery
            </button>
        </form>
        @endif
    </div>

    {{-- Flash --}}
    @foreach(['success','error'] as $type)
    @if(session($type))
    <div class="alert alert-{{ $type === 'success' ? 'success' : 'danger' }} alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
        <i class="bx bx-{{ $type === 'success' ? 'check-circle' : 'x-circle' }} fs-5"></i>
        {{ session($type) }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @endforeach

    {{-- Quick links --}}
    @if(in_array(auth()->user()->role, ['admin','superadmin','bendahara','ahli_gizi']))
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bx bx-receipt me-1"></i>List Orders
        </a>
        <a href="{{ route('admin.orders.export') }}" class="btn btn-sm btn-outline-success">
            <i class="bx bx-download me-1"></i>Export Excel
        </a>
        <a href="{{ route('admin.orders.report') }}" target="_blank" class="btn btn-sm btn-outline-warning">
            <i class="bx bx-printer me-1"></i>Laporan PDF
        </a>
    </div>
    @endif

    {{-- KPI --}}
    <div class="row g-3 mb-4">
        @php
        $kpiItems = [
            ['label'=>'Total Order',    'val'=>$kpi['total_orders']??0,    'color'=>'#28a745','bg'=>'#d4edda','icon'=>'bx-shopping-bag',  'text'=>'text-success'],
            ['label'=>'Sudah Bayar',    'val'=>$kpi['paid_orders']??0,     'color'=>'#17a2b8','bg'=>'#d1ecf1','icon'=>'bx-check-shield',  'text'=>'text-info'],
            ['label'=>'Belum Bayar',    'val'=>$kpi['unpaid_orders']??0,   'color'=>'#ffc107','bg'=>'#fff3cd','icon'=>'bx-time-five',     'text'=>'text-warning'],
            ['label'=>'Aktif Hari Ini', 'val'=>$kpi['active_today']??0,    'color'=>'#6f42c1','bg'=>'#e9d8fd','icon'=>'bx-user-check',   'text'=>''],
        ];
        @endphp
        @foreach($kpiItems as $k)
        <div class="col-6 col-xl-3">
            <div class="card kpi-card shadow-sm border-0" style="border-left:4px solid {{ $k['color'] }} !important;">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="kpi-icon" style="background:{{ $k['bg'] }};">
                        <i class="bx {{ $k['icon'] }} {{ $k['text'] }}" style="{{ !$k['text'] ? 'color:'.$k['color'] : '' }}"></i>
                    </div>
                    <div>
                        <div class="text-muted" style="font-size:11px;font-weight:700;letter-spacing:.5px;">{{ strtoupper($k['label']) }}</div>
                        <div class="fw-bold" style="font-size:26px;color:{{ $k['color'] }};line-height:1.2;">{{ $k['val'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Summary bar --}}
    @if($items->count() > 0)
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-body py-3 d-flex flex-wrap align-items-center gap-3">
            <span class="fw-semibold small">
                <i class="bx bx-calendar-check text-success me-1"></i>
                Pengambilan {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y') }}
            </span>
            @foreach(['diambil'=>['#28a745','Diambil'],'siap'=>['#6f42c1','Siap'],'diproses'=>['#fd7e14','Diproses'],'diterima'=>['#17a2b8','Diterima'],'pending'=>['#6c757d','Pending']] as $key=>[$bg,$lbl])
                @php $cnt = ($agg['siang'][$key]??0) + ($agg['malam'][$key]??0); @endphp
                @if($cnt > 0)
                <span class="badge" style="background:{{ $bg }};padding:5px 11px;border-radius:20px;font-size:11px;">
                    {{ $lbl }}: <strong>{{ $cnt }}</strong>
                </span>
                @endif
            @endforeach
            <span class="ms-auto text-muted small">{{ $items->count() }} menu aktif</span>
        </div>
    </div>
    @endif

    {{-- Delivery cards --}}
    @php
    $statusCfg = [
        'pending'  => ['color'=>'#6c757d','bg'=>'#f8f9fa','label'=>'PENDING',   'pct'=>0],
        'diterima' => ['color'=>'#17a2b8','bg'=>'#e3f9ff','label'=>'DITERIMA',  'pct'=>25],
        'diproses' => ['color'=>'#fd7e14','bg'=>'#fff3e0','label'=>'DIPROSES',  'pct'=>50],
        'siap'     => ['color'=>'#6f42c1','bg'=>'#f0e6ff','label'=>'SIAP',      'pct'=>75],
        'diambil'  => ['color'=>'#28a745','bg'=>'#d4edda','label'=>'DIAMBIL',   'pct'=>100],
    ];
    @endphp

    <div class="row g-3">
    @forelse($items as $row)
    @php
        $spec      = $row->menuMakanan->spec_menu ?? [];
        $menuSiang = $spec['Makan Siang'] ?? [];
        $menuMalam = $spec['Makan Malam'] ?? [];
        $cfgS      = $statusCfg[$row->status_siang] ?? $statusCfg['pending'];
        $cfgM      = $statusCfg[$row->status_malam] ?? $statusCfg['pending'];

        // Orders aktif untuk paket ini
        $ordersAktif = $activeOrdersByPackage[$row->meal_package_id] ?? collect();
    @endphp

    <div class="col-12">
    <div class="delivery-card card shadow-sm">

        {{-- Header card --}}
        <div class="card-header py-3 d-flex flex-wrap align-items-center gap-3"
             style="background:linear-gradient(90deg,#f0fff4,#fff);border-radius:12px 12px 0 0;">
            <div style="width:38px;height:38px;border-radius:9px;background:linear-gradient(135deg,#28a745,#20c997);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bx bx-bowl-hot text-white"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold" style="font-size:15px;">
                    {{ $row->menuMakanan->nama_menu ?? ($row->mealPackage->nama_meal_package ?? 'Menu #'.$row->menu_makanan_id) }}
                </div>
                <div class="text-muted d-flex flex-wrap align-items-center gap-2" style="font-size:12px;">
                    <span><i class="bx bx-box me-1"></i>{{ $row->mealPackage->nama_meal_package ?? 'Paket #'.$row->meal_package_id }}</span>
                    <span class="badge bg-secondary" style="font-size:10px;">Batch {{ $row->batch }}</span>
                    <span><i class="bx bx-calendar me-1"></i>{{ \Carbon\Carbon::parse($row->delivery_date)->locale('id')->isoFormat('D MMM Y') }}</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                {{-- Badge jumlah penerima --}}
                @if($ordersAktif->count() > 0)
                <span class="badge" style="background:#e8f5e9;color:#28a745;border:1px solid #a5d6a7;font-size:11px;padding:5px 10px;border-radius:8px;">
                    <i class="bx bx-user-check me-1"></i>{{ $ordersAktif->count() }} penerima
                </span>
                @endif

                @if($row->confirmed_by)
                <div class="text-success d-flex align-items-center gap-1" style="font-size:12px;">
                    <i class="bx bx-check-shield"></i>
                    <span>{{ $row->confirmer?->name ?? '-' }} · {{ \Carbon\Carbon::parse($row->confirmed_at)->format('H:i') }}</span>
                </div>
                @else
                <div class="text-warning d-flex align-items-center gap-1" style="font-size:12px;">
                    <i class="bx bx-time"></i> Belum dikonfirmasi
                </div>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3">

                {{-- ── Kolom 1: Daftar Order Penerima ── --}}
                <div class="col-lg-5">
                    <div class="fw-semibold small mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="bx bx-list-ul me-1 text-success"></i>DAFTAR PENERIMA AKTIF</span>
                        @if($ordersAktif->count() > 0)
                        <span class="badge bg-success" style="font-size:10px;">{{ $ordersAktif->count() }} order</span>
                        @endif
                    </div>
                    @if($ordersAktif->count() > 0)
                    <div class="rounded-3 border" style="max-height:220px;overflow-y:auto;">
                        @foreach($ordersAktif as $ord)
                        <div class="order-row px-3 d-flex align-items-center gap-2">
                            <div style="width:28px;height:28px;border-radius:7px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bx bx-user text-success" style="font-size:13px;"></i>
                            </div>
                            <div class="flex-grow-1" style="min-width:0;">
                                <div class="fw-semibold text-truncate" style="font-size:13px;">
                                    {{ $ord->user?->name ?? '—' }}
                                </div>
                                <div class="text-muted d-flex flex-wrap gap-2" style="font-size:11px;">
                                    <span class="font-monospace">{{ $ord->order_number }}</span>
                                    @if($ord->whatsapp ?? $ord->user?->detail?->hp)
                                    <span>
                                        <i class="bx bxl-whatsapp" style="color:#25D366;"></i>
                                        {{ $ord->whatsapp ?? $ord->user?->detail?->hp }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.orders.show', $ord) }}"
                               class="btn btn-sm" style="padding:2px 8px;font-size:11px;background:#f0f0f0;border-radius:6px;color:#495057;text-decoration:none;"
                               title="Detail order">
                                <i class="bx bx-link-external"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4 text-muted bg-light rounded-3" style="font-size:13px;">
                        <i class="bx bx-info-circle me-1"></i>
                        Tidak ada order aktif untuk paket ini hari ini
                    </div>
                    @endif

                    {{-- Menu preview --}}
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <div class="bg-light rounded-2 p-2">
                                <div class="fw-semibold text-success" style="font-size:11px;margin-bottom:4px;"><i class="bx bx-sun me-1"></i>MENU SIANG</div>
                                @forelse(array_slice($menuSiang,0,4) as $m)
                                <div style="font-size:11px;border-bottom:1px dashed #e0e0e0;padding:2px 0;">{{ $m }}</div>
                                @empty <div class="text-muted" style="font-size:11px;">-</div>
                                @endforelse
                                @if(count($menuSiang) > 4)<div class="text-muted" style="font-size:10px;">+{{ count($menuSiang)-4 }} lainnya</div>@endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded-2 p-2">
                                <div class="fw-semibold text-primary" style="font-size:11px;margin-bottom:4px;"><i class="bx bx-moon me-1"></i>MENU MALAM</div>
                                @forelse(array_slice($menuMalam,0,4) as $m)
                                <div style="font-size:11px;border-bottom:1px dashed #e0e0e0;padding:2px 0;">{{ $m }}</div>
                                @empty <div class="text-muted" style="font-size:11px;">-</div>
                                @endforelse
                                @if(count($menuMalam) > 4)<div class="text-muted" style="font-size:10px;">+{{ count($menuMalam)-4 }} lainnya</div>@endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Kolom 2: Status Pesanan (Siang) ── --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="fw-semibold small mb-2">
                        <i class="bx bx-sun me-1" style="color:#ffc107;"></i>STATUS SIANG
                    </div>
                    <span class="badge mb-2 rounded-pill" style="background:{{ $cfgS['bg'] }};color:{{ $cfgS['color'] }};border:1px solid {{ $cfgS['color'] }};font-size:12px;padding:5px 12px;">
                        {{ $cfgS['label'] }}
                    </span>
                    <div class="progress-track mb-3">
                        <div class="progress-fill" style="width:{{ $cfgS['pct'] }}%;background:{{ $cfgS['color'] }};"></div>
                    </div>
                    @if(in_array(auth()->user()->role, config('settings.delivery.update_status', [])))
                    <form method="POST" action="{{ route('admin.deliveries.updateStatus', $row->id) }}"
                          class="d-flex gap-2 align-items-center">
                        @csrf @method('PATCH')
                        <input type="hidden" name="field" value="status_siang">
                        <select name="value" class="form-select form-select-sm" style="border-radius:7px;">
                            @foreach(['pending'=>'Pending','diterima'=>'Pesanan Diterima HeartFit','diproses'=>'Pesanan Diproses','siap'=>'Pesanan Siap Diambil','diambil'=>'Pesanan Diambil Pelanggan'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $row->status_siang===$val?'selected':'' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary" style="border-radius:7px;" title="Simpan & kirim notif WA">
                            <i class="bx bx-check"></i>
                        </button>
                    </form>
                    @endif
                </div>

                {{-- ── Kolom 3: Status Pesanan (Malam/Sesi 2) ── --}}
                <div class="col-lg-4 col-sm-6">
                    <div class="fw-semibold small mb-2">
                        <i class="bx bx-moon me-1 text-primary"></i>STATUS MALAM
                    </div>
                    <span class="badge mb-2 rounded-pill" style="background:{{ $cfgM['bg'] }};color:{{ $cfgM['color'] }};border:1px solid {{ $cfgM['color'] }};font-size:12px;padding:5px 12px;">
                        {{ $cfgM['label'] }}
                    </span>
                    <div class="progress-track mb-3">
                        <div class="progress-fill" style="width:{{ $cfgM['pct'] }}%;background:{{ $cfgM['color'] }};"></div>
                    </div>
                    @if(in_array(auth()->user()->role, config('settings.delivery.update_status', [])))
                    <form method="POST" action="{{ route('admin.deliveries.updateStatus', $row->id) }}"
                          class="d-flex gap-2 align-items-center">
                        @csrf @method('PATCH')
                        <input type="hidden" name="field" value="status_malam">
                        <select name="value" class="form-select form-select-sm" style="border-radius:7px;">
                            @foreach(['pending'=>'Pending','diterima'=>'Pesanan Diterima HeartFit','diproses'=>'Pesanan Diproses','siap'=>'Pesanan Siap Diambil','diambil'=>'Pesanan Diambil Pelanggan'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $row->status_malam===$val?'selected':'' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary" style="border-radius:7px;" title="Simpan & kirim notif WA">
                            <i class="bx bx-check"></i>
                        </button>
                    </form>
                    @endif
                </div>

            </div>
        </div>

    </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm text-center py-5" style="border-radius:12px;">
            <div style="font-size:52px;">📦</div>
            <h5 class="fw-bold mt-3 mb-1">Belum Ada Data Pengiriman</h5>
            <p class="text-muted small mb-3">
                {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y') }} belum ada data pengiriman.
            </p>
            @if(in_array(auth()->user()->role, config('settings.delivery.generate', [])))
            <form method="POST" action="{{ route('admin.deliveries.generate') }}" class="d-inline">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <button type="submit" class="btn btn-success">
                    <i class="bx bx-refresh me-1"></i>Generate Delivery Sekarang
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforelse
    </div>

    {{-- ── Preview 3 Hari ke Depan ── --}}
    @if(!empty($upcomingDays))
    <div class="mt-5">
        <h5 class="fw-bold mb-3">
            <i class="bx bx-calendar-week text-primary me-2"></i>
            Preview Order — 3 Hari ke Depan
        </h5>
        <div class="row g-3">
            @foreach($upcomingDays as $day)
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:12px;border-top:4px solid #0d6efd !important;">
                    <div class="card-header d-flex align-items-center justify-content-between py-3"
                         style="background:linear-gradient(90deg,#f0f7ff,#fff);border-radius:12px 12px 0 0;">
                        <div>
                            <div class="fw-bold text-primary" style="font-size:15px;">
                                <i class="bx bx-calendar me-1"></i>{{ $day['label'] }}
                            </div>
                            <div class="text-muted small">{{ $day['date'] }}</div>
                        </div>
                        <span class="badge bg-primary rounded-pill" style="font-size:12px;">
                            {{ $day['total'] }} order aktif
                        </span>
                    </div>
                    <div class="card-body py-3">
                        @if($day['total'] === 0)
                            <div class="text-center text-muted py-3" style="font-size:13px;">
                                <i class="bx bx-info-circle me-1"></i>Tidak ada order aktif
                            </div>
                        @else
                            @foreach($day['byBatch'] as $batch => $batchOrders)
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-secondary" style="font-size:11px;">BATCH {{ $batch }}</span>
                                    <span class="text-muted small">{{ $batchOrders->count() }} orang</span>
                                </div>
                                <div class="rounded-3 border" style="max-height:200px;overflow-y:auto;">
                                    @foreach($batchOrders as $ord)
                                    <div class="d-flex align-items-center gap-2 px-3 py-2"
                                         style="border-bottom:1px solid #f0f0f0;">
                                        <div style="width:26px;height:26px;border-radius:6px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <i class="bx bx-user text-success" style="font-size:12px;"></i>
                                        </div>
                                        <div class="flex-grow-1" style="min-width:0;">
                                            <div class="fw-semibold text-truncate" style="font-size:12px;">
                                                {{ $ord->user?->name ?? '—' }}
                                            </div>
                                            <div class="text-muted d-flex flex-wrap gap-2" style="font-size:10px;">
                                                <span class="font-monospace">{{ $ord->order_number }}</span>
                                                <span class="text-truncate">{{ $ord->package_label }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.orders.show', $ord) }}"
                                           class="btn btn-sm" style="padding:2px 6px;font-size:10px;background:#f0f0f0;border-radius:5px;color:#495057;"
                                           title="Detail order">
                                            <i class="bx bx-link-external"></i>
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
