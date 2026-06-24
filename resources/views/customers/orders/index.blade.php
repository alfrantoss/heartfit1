@extends('layouts.app')
@section('title', 'Riwayat Pemesanan')

@push('styles')
<style>
.order-card {
    border-radius: 14px !important;
    border: 1.5px solid #e9ecef !important;
    transition: box-shadow .2s, transform .2s, border-color .2s;
    overflow: hidden;
}
.order-card:hover {
    box-shadow: 0 8px 28px rgba(40,167,69,.13) !important;
    border-color: #28a745 !important;
    transform: translateY(-2px);
}
.order-card .card-body { padding: 20px 22px; }
.status-dot {
    width: 9px; height: 9px; border-radius: 50%;
    display: inline-block; margin-right: 6px;
    animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.6; transform:scale(1.3); }
}
.status-paid    { background:#28a745; }
.status-unpaid  { background:#ffc107; }
.status-expired { background:#6c757d; animation:none; }
.status-canceled{ background:#dc3545; animation:none; }

.filter-tab {
    padding: 6px 18px; border-radius: 20px; font-size:13px; font-weight:600;
    border: 1.5px solid #dee2e6; background: #fff; color: #6c757d;
    text-decoration:none; transition: all .2s;
}
.filter-tab:hover { border-color:#28a745; color:#28a745; }
.filter-tab.active { background:#28a745; border-color:#28a745; color:#fff; }

.empty-state { text-align:center; padding: 60px 20px; }
.empty-state .icon-wrap {
    width:80px; height:80px; border-radius:50%;
    background:linear-gradient(135deg,#d4edda,#f0fff4);
    display:inline-flex; align-items:center; justify-content:center;
    font-size:32px; color:#28a745; margin-bottom:16px;
}
.badge-status {
    font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px;
}
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-history text-success me-2"></i>Riwayat Pemesanan
            </h4>
            <p class="text-muted mb-0 small">Semua pesanan yang pernah kamu buat</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-success">
            <i class="bx bx-plus-circle me-2"></i>Pesan Baru
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="d-flex gap-2 mb-4 flex-wrap align-items-center">
        <a href="{{ route('customer.orders.index', array_merge(request()->except('status'), [])) }}"
           class="filter-tab {{ !request('status') ? 'active' : '' }}">
            Semua
        </a>
        <a href="{{ route('customer.orders.index', ['status'=>'UNPAID']) }}"
           class="filter-tab {{ request('status')==='UNPAID' ? 'active' : '' }}">
            <span class="status-dot status-unpaid"></span>Belum Dibayar
        </a>
        <a href="{{ route('customer.orders.index', ['status'=>'PAID']) }}"
           class="filter-tab {{ request('status')==='PAID' ? 'active' : '' }}">
            <span class="status-dot status-paid"></span>Sudah Dibayar
        </a>
        <a href="{{ route('customer.orders.index', ['status'=>'EXPIRED']) }}"
           class="filter-tab {{ request('status')==='EXPIRED' ? 'active' : '' }}">
            <span class="status-dot status-expired"></span>Kadaluarsa
        </a>

        {{-- Search --}}
        <form method="GET" action="{{ route('customer.orders.index') }}"
              class="d-flex gap-2 ms-auto align-items-center">
            @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <div class="input-group" style="min-width:240px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bx bx-search text-muted"></i>
                </span>
                <input type="search" name="q" value="{{ request('q') }}"
                       class="form-control border-start-0 ps-0"
                       placeholder="Cari nomor order / paket…" style="border-radius:0 10px 10px 0;">
            </div>
            <button type="submit" class="btn btn-primary" style="border-radius:10px;">Cari</button>
            @if(request('q'))
            <a href="{{ route('customer.orders.index', array_filter(['status'=>request('status')])) }}"
               class="btn btn-outline-secondary" style="border-radius:10px;">Reset</a>
            @endif
        </form>
    </div>

    {{-- Order Cards --}}
    @forelse($orders as $o)
    @php
        $total    = $o->amount_total ?? $o->package_price;
        $isUnpaid = strtoupper($o->status) === 'UNPAID';
        $isExpired= strtoupper($o->status) === 'EXPIRED';
        $isPaid   = in_array(strtoupper($o->status), ['PAID','SETTLEMENT']);
        $statusMap= [
            'PAID'       => ['label'=>'Dibayar',     'class'=>'bg-success',              'dot'=>'status-paid'],
            'SETTLEMENT' => ['label'=>'Dibayar',     'class'=>'bg-success',              'dot'=>'status-paid'],
            'UNPAID'     => ['label'=>'Belum Bayar', 'class'=>'bg-warning text-dark',    'dot'=>'status-unpaid'],
            'EXPIRED'    => ['label'=>'Kadaluarsa',  'class'=>'bg-secondary',            'dot'=>'status-expired'],
            'CANCELED'   => ['label'=>'Dibatalkan',  'class'=>'bg-danger',               'dot'=>'status-canceled'],
        ];
        $st = $statusMap[strtoupper($o->status)] ?? ['label'=>$o->status,'class'=>'bg-light text-dark','dot'=>'status-expired'];
    @endphp

    <div class="order-card card mb-3">
        <div class="card-body">
            <div class="row align-items-center g-3">

                {{-- Left: Order info --}}
                <div class="col-md-5">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:48px;height:48px;background:linear-gradient(135deg,#d4edda,#f0fff4);">
                            <i class="bx bx-receipt text-success" style="font-size:22px;"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold" style="font-size:14px;">{{ $o->order_number }}</span>
                                <span class="badge-status {{ $st['class'] }}">
                                    <span class="status-dot {{ $st['dot'] }}" style="margin-right:4px;width:6px;height:6px;"></span>
                                    {{ $st['label'] }}
                                </span>
                            </div>
                            <div class="fw-semibold text-dark" style="font-size:15px;">{{ $o->package_label }}</div>
                            <div class="text-muted small mt-1">
                                <i class="bx bx-category me-1"></i>{{ ucfirst($o->package_category ?? '-') }}
                                @if($o->package_batch)
                                    &nbsp;·&nbsp;<i class="bx bx-layer me-1"></i>Batch {{ $o->package_batch }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mid: Periode & total --}}
                <div class="col-md-4">
                    <div class="d-flex gap-4">
                        <div>
                            <div class="text-muted small mb-1"><i class="bx bx-calendar me-1"></i>Periode</div>
                            <div class="fw-semibold" style="font-size:13px;">
                                {{ optional($o->start_date)->format('d M Y') }}
                                <span class="text-muted">—</span>
                                {{ optional($o->end_date)->format('d M Y') }}
                            </div>
                            <div class="text-muted small">{{ $o->days }} hari</div>
                        </div>
                        <div>
                            <div class="text-muted small mb-1"><i class="bx bx-wallet me-1"></i>Total</div>
                            <div class="fw-bold text-success" style="font-size:15px;">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </div>
                            <div class="text-muted small text-uppercase">{{ str_replace('_',' ',$o->payment_method ?? '-') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Right: Actions --}}
                <div class="col-md-3 d-flex gap-2 flex-wrap justify-content-md-end">
                    <a href="{{ route('orders.finish', $o) }}"
                       class="btn btn-sm btn-outline-secondary" style="border-radius:8px;">
                        <i class="bx bx-show me-1"></i>Detail
                    </a>

                    @if($isPaid)
                        <a href="{{ route('orders.generate-pdf', $o) }}" target="_blank"
                           class="btn btn-sm btn-outline-info" style="border-radius:8px;">
                            <i class="bx bx-receipt me-1"></i>Struk
                        </a>
                    @endif

                    @if($isUnpaid)
                        @if($o->payment_method === 'transfer')
                            <a href="{{ route('orders.pay', $o) }}"
                               class="btn btn-sm btn-warning" style="border-radius:8px;">
                                <i class="bx bx-credit-card me-1"></i>Bayar
                            </a>
                        @else
                            <a href="{{ route('orders.finish', $o) }}"
                               class="btn btn-sm btn-info" style="border-radius:8px;">
                                <i class="bx bx-info-circle me-1"></i>Info COD
                            </a>
                        @endif
                    @endif

                    @if($isExpired)
                        @if($o->payment_method === 'transfer')
                            <a href="{{ route('orders.pay', $o) }}"
                               class="btn btn-sm btn-success" style="border-radius:8px;"
                               title="Bayar ulang, berlaku 5 menit">
                                <i class="bx bx-refresh me-1"></i>Bayar Ulang
                            </a>
                        @endif
                        <a href="{{ route('orders.create', ['package_key'=>$o->package_key]) }}"
                           class="btn btn-sm btn-outline-warning" style="border-radius:8px;">
                            <i class="bx bx-cart me-1"></i>Pesan Ulang
                        </a>
                    @endif
                </div>
            </div>

            {{-- Bottom: Created at + Progress Pengambilan --}}
            <div class="mt-2 pt-2 border-top">
                <div class="d-flex flex-wrap align-items-center gap-3 mb-2">
                    <small class="text-muted">
                        <i class="bx bx-time me-1"></i>
                        Dibuat {{ $o->created_at ? $o->created_at->locale('id')->diffForHumans() : '-' }}
                        &nbsp;·&nbsp;
                        {{ $o->created_at ? $o->created_at->format('d M Y H:i') : '-' }}
                    </small>
                    @if($o->paid_at)
                    <small class="text-success">
                        <i class="bx bx-check-circle me-1"></i>
                        Dibayar {{ $o->paid_at->format('d M Y H:i') }}
                    </small>
                    @endif
                </div>

                @if($isPaid)
                @php
                    $today        = \Carbon\Carbon::today();
                    $isActiveToday = $o->start_date && $o->end_date && $today->between($o->start_date, $o->end_date);
                    $isUpcoming    = $o->start_date && $today->lt($o->start_date);
                    $isDone        = $o->end_date   && $today->gt($o->end_date);

                    // Progress pengambilan dari DB
                    $pp = $o->getPickupProgress();

                    // Status hari ini
                    $deliveryToday = null;
                    if ($isActiveToday) {
                        $deliveryToday = \App\Models\OrderDeliveryStatus::where('meal_package_id', $o->package_key)
                            ->whereDate('delivery_date', $today)->first();
                    }
                    $siangCfg = [
                        'pending'  => ['bg'=>'#6c757d','label'=>'Menunggu'],
                        'diterima' => ['bg'=>'#17a2b8','label'=>'Diterima HeartFit'],
                        'diproses' => ['bg'=>'#fd7e14','label'=>'Sedang Diproses'],
                        'siap'     => ['bg'=>'#6f42c1','label'=>'Siap Diambil 🔔'],
                        'diambil'  => ['bg'=>'#28a745','label'=>'Sudah Diambil ✓'],
                    ];
                @endphp

                {{-- Progress bar pengambilan --}}
                @if($pp['total_sesi'] > 0)
                <div class="rounded-3 p-3" style="background:#f8f9fa;border:1px solid #e9ecef;">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div class="fw-semibold small d-flex align-items-center gap-2">
                            <i class="bx bx-store-alt text-success"></i>
                            Progres Pengambilan
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-success" style="font-size:11px;">
                                {{ $pp['total_diambil'] }}/{{ $pp['total_sesi'] }} sesi diambil
                            </span>
                            <span class="fw-bold" style="font-size:13px;color:{{ $pp['persen'] == 100 ? '#28a745' : '#495057' }};">
                                {{ $pp['persen'] }}%
                            </span>
                        </div>
                    </div>
                    {{-- Progress bar --}}
                    <div style="height:8px;background:#dee2e6;border-radius:4px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pp['persen'] }}%;background:{{ $pp['persen'] == 100 ? '#28a745' : ($pp['persen'] > 50 ? '#17a2b8' : '#ffc107') }};border-radius:4px;transition:width .5s;"></div>
                    </div>
                    {{-- Siang vs Malam breakdown --}}
                    <div class="d-flex gap-3 mt-2">
                        <small class="text-muted">
                            <i class="bx bx-sun me-1" style="color:#ffc107;"></i>Siang:
                            <strong>{{ $pp['siang_diambil'] }}/{{ $pp['siang_total'] }}</strong>
                        </small>
                        <small class="text-muted">
                            <i class="bx bx-moon me-1 text-primary"></i>Malam:
                            <strong>{{ $pp['malam_diambil'] }}/{{ $pp['malam_total'] }}</strong>
                        </small>
                        @if($isDone)
                        <small class="{{ $pp['persen'] == 100 ? 'text-success' : 'text-warning' }} ms-auto fw-semibold">
                            <i class="bx {{ $pp['persen'] == 100 ? 'bx-check-circle' : 'bx-info-circle' }} me-1"></i>
                            {{ $pp['persen'] == 100 ? 'Semua sesi selesai' : 'Paket selesai' }}
                        </small>
                        @endif
                    </div>

                    {{-- Status hari ini (kalau aktif) --}}
                    @if($isActiveToday && $deliveryToday)
                    @php
                        $cfgS = $siangCfg[$deliveryToday->status_siang] ?? $siangCfg['pending'];
                        $cfgM = $siangCfg[$deliveryToday->status_malam] ?? $siangCfg['pending'];
                    @endphp
                    <div class="d-flex flex-wrap gap-2 mt-2 pt-2" style="border-top:1px dashed #dee2e6;">
                        <small class="text-muted fw-semibold align-self-center">Hari ini:</small>
                        <span class="badge rounded-pill" style="background:{{ $cfgS['bg'] }};color:#fff;font-size:10px;padding:3px 9px;">
                            <i class="bx bx-sun me-1"></i>Siang: {{ $cfgS['label'] }}
                        </span>
                        <span class="badge rounded-pill" style="background:{{ $cfgM['bg'] }};color:#fff;font-size:10px;padding:3px 9px;">
                            <i class="bx bx-moon me-1"></i>Malam: {{ $cfgM['label'] }}
                        </span>
                    </div>
                    @elseif($isActiveToday && !$deliveryToday)
                    <div class="mt-2 pt-2" style="border-top:1px dashed #dee2e6;">
                        <small class="text-muted"><i class="bx bx-info-circle me-1"></i>Jadwal hari ini belum tersedia.</small>
                    </div>
                    @elseif($isUpcoming)
                    <div class="mt-2 pt-2" style="border-top:1px dashed #dee2e6;">
                        <small class="text-info"><i class="bx bx-calendar me-1"></i>Mulai {{ $o->start_date->format('d M Y') }}</small>
                    </div>
                    @endif
                </div>
                @elseif($isUpcoming)
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill bg-info text-white" style="font-size:10px;padding:4px 9px;">
                        <i class="bx bx-calendar me-1"></i>Mulai {{ $o->start_date->format('d M Y') }}
                    </span>
                    <small class="text-muted">Progres pengambilan akan tersedia setelah paket dimulai.</small>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm">
        <div class="card-body empty-state">
            <div class="icon-wrap"><i class="bx bx-cart"></i></div>
            <h5 class="fw-bold mb-2">Belum Ada Pesanan</h5>
            <p class="text-muted mb-4">
                @if(request('q'))
                    Tidak ada hasil untuk pencarian "<strong>{{ request('q') }}</strong>".
                @elseif(request('status'))
                    Tidak ada pesanan dengan status <strong>{{ request('status') }}</strong>.
                @else
                    Kamu belum pernah memesan. Yuk mulai perjalanan sehat!
                @endif
            </p>
            <a href="{{ route('orders.create') }}" class="btn btn-success px-4">
                <i class="bx bx-plus-circle me-2"></i>Pesan Sekarang
            </a>
        </div>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">
            Menampilkan {{ $orders->firstItem() }}–{{ $orders->lastItem() }}
            dari {{ $orders->total() }} pesanan
        </small>
        {{ $orders->appends(request()->query())->links('pagination::bootstrap-5-ellipses') }}
    </div>
    @endif

</div>
@endsection
