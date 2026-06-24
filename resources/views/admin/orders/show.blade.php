@extends('layouts.app')

@section('title', 'Detail Order')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Order #{{ $order->order_number }}</h5>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>

        <div class="card-body">

            {{-- ── Informasi Order + Customer ── --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Informasi Order</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>No. Order:</strong></td>
                            <td>{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @switch(strtoupper($order->status))
                                    @case('PAID') @case('SETTLEMENT')
                                        <span class="badge bg-success">PAID</span>
                                    @break
                                    @case('UNPAID')
                                        <span class="badge bg-warning text-dark">UNPAID</span>
                                    @break
                                    @case('EXPIRED')
                                        <span class="badge bg-secondary">EXPIRED</span>
                                    @break
                                    @case('CANCELED')
                                        <span class="badge bg-danger">CANCELED</span>
                                    @break
                                    @default
                                        <span class="badge bg-light text-dark">{{ strtoupper($order->status ?? '—') }}</span>
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Metode Pembayaran:</strong></td>
                            <td class="text-uppercase">{{ str_replace('_', ' ', $order->payment_method ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Dibuat:</strong></td>
                            <td>{{ $order->created_at ? $order->created_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Dibayar:</strong></td>
                            <td>{{ $order->paid_at ? $order->paid_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Informasi Customer</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $order->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $order->user?->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>No. HP:</strong></td>
                            <td>
                                @if($order->user?->detail?->hp)
                                    {{ $order->user->detail->hp }}
                                    @if(in_array(auth()->user()->role, ['ahli_gizi', 'superadmin']))
                                        <a href="https://wa.me/62{{ substr($order->user->detail->hp, 1) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-success ms-2">
                                            <i class="bx bxl-whatsapp"></i> WA
                                        </a>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Alamat:</strong></td>
                            <td>{{ $order->user?->detail?->alamat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- ── Informasi Paket ── --}}
            <div class="row mb-4">
                <div class="col-12">
                    <h6>Informasi Paket</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Nama Paket:</strong></td>
                            <td>{{ $order->package_label }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kategori:</strong></td>
                            <td>{{ $order->package_category ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Batch:</strong></td>
                            <td>{{ $order->package_batch ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Periode:</strong></td>
                            <td>
                                {{ $order->start_date ? \Carbon\Carbon::parse($order->start_date)->format('d M Y') : '-' }}
                                s.d
                                {{ $order->end_date ? \Carbon\Carbon::parse($order->end_date)->format('d M Y') : '-' }}
                                ({{ $order->days ?? 0 }} hari)
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Harga Paket:</strong></td>
                            <td>Rp {{ number_format($order->package_price ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Bayar:</strong></td>
                            <td><strong>Rp {{ number_format($order->amount_total ?? $order->package_price ?? 0, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- ── Tanggal Layanan ── --}}
            @if($order->service_dates && count($order->service_dates) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="mb-3">Tanggal Layanan</h6>
                    @php
                        $serviceDates = $order->service_dates;
                        $totalDates   = count($serviceDates);
                        $showAll      = request('show_all_dates', false);
                    @endphp
                    <div class="row g-2">
                        @foreach(array_slice($serviceDates, 0, $showAll ? $totalDates : 6) as $sd)
                        <div class="col-auto">
                            <div class="card card-body p-2 text-center border-0 bg-light">
                                <div class="small text-muted mb-1">{{ \Carbon\Carbon::parse($sd)->format('D') }}</div>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($sd)->format('d') }}</div>
                                <div class="small">{{ \Carbon\Carbon::parse($sd)->format('M Y') }}</div>
                            </div>
                        </div>
                        @endforeach
                        @if(!$showAll && $totalDates > 6)
                        <div class="col-auto">
                            <div class="card card-body p-2 text-center border-0 bg-secondary">
                                <div class="small text-white">+{{ $totalDates - 6 }}</div>
                                <div class="fw-bold text-white">lagi</div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="mt-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted">Total: {{ $totalDates }} hari layanan</small>
                        @if($totalDates > 6)
                        <a href="{{ request()->fullUrlWithQuery(['show_all_dates' => $showAll ? 0 : 1]) }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-{{ $showAll ? 'chevron-up' : 'chevron-down' }}"></i>
                            {{ $showAll ? 'Sembunyikan' : 'Lihat Semua' }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Menu Unik ── --}}
            @if($order->unique_menus && count($order->unique_menus) > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h6>Menu yang Didapatkan ({{ $order->unique_menu_count ?? count($order->unique_menus) }} menu)</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($order->unique_menus as $menu)
                            <span class="badge bg-info text-white">{{ $menu }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Catatan Khusus ── --}}
            @if(!empty($order->notes))
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="mb-2">
                        <i class="bx bx-comment-dots me-2"></i>Catatan Customer
                        @if(strcasecmp($order->package_category ?? '', 'personal') === 0)
                            <small class="text-muted">(Paket Personal)</small>
                        @endif
                    </h6>
                    <div class="alert alert-info py-2 mb-0">
                        <i class="bx bx-info-circle me-1"></i>{{ $order->notes }}
                    </div>
                </div>
            </div>
            @endif

            {{-- ── Progres Pengambilan ── --}}
            @if(in_array(strtoupper($order->status), ['PAID','SETTLEMENT']))
            @php
                $pickup  = $order->getPickupProgress();
                $history = $order->getPickupHistory();

                $stCfg = [
                    'pending'  => ['color'=>'#6c757d','icon'=>'bx-time',        'label'=>'Menunggu'],
                    'diterima' => ['color'=>'#17a2b8','icon'=>'bx-package',     'label'=>'Diterima'],
                    'diproses' => ['color'=>'#fd7e14','icon'=>'bx-bowl-hot',    'label'=>'Diproses'],
                    'siap'     => ['color'=>'#6f42c1','icon'=>'bx-bell',        'label'=>'Siap Diambil 🔔'],
                    'diambil'  => ['color'=>'#28a745','icon'=>'bx-check-circle','label'=>'Diambil ✓'],
                ];

                $historyMap  = $history->keyBy(fn($r) => \Carbon\Carbon::parse($r->delivery_date)->toDateString());
                $periodDates = [];
                if ($order->start_date && $order->end_date) {
                    $cur = \Carbon\Carbon::parse($order->start_date)->copy();
                    $eod = \Carbon\Carbon::parse($order->end_date);
                    while ($cur->lte($eod)) {
                        $periodDates[] = $cur->toDateString();
                        $cur->addDay();
                    }
                }
            @endphp
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="mb-3 d-flex align-items-center gap-2">
                        <i class="bx bx-store-alt text-success"></i>
                        Progres Pengambilan
                        <span class="badge bg-success rounded-pill ms-1">
                            {{ $pickup['total_diambil'] }} / {{ $pickup['total_sesi'] }} sesi
                        </span>
                    </h6>

                    {{-- KPI cards --}}
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-sm-3">
                            <div class="rounded-3 text-center py-3 px-2" style="background:#f0fff4;border:1px solid #d4edda;">
                                <div class="fw-bold fs-3 text-success">{{ $pickup['total_diambil'] }}</div>
                                <div class="text-muted small">Sesi Diambil</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="rounded-3 text-center py-3 px-2" style="background:#f8f9fa;border:1px solid #dee2e6;">
                                <div class="fw-bold fs-3 text-secondary">{{ $pickup['total_sesi'] }}</div>
                                <div class="text-muted small">Total Sesi</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="rounded-3 text-center py-3 px-2" style="background:#f8f9fa;border:1px solid #dee2e6;">
                                <div class="fw-bold fs-3 text-secondary">{{ $pickup['hari_diambil'] }}/{{ $pickup['hari_total'] }}</div>
                                <div class="text-muted small">Hari Penuh</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="rounded-3 text-center py-3 px-2"
                                 style="background:{{ $pickup['persen']==100 ? '#f0fff4' : '#fff8e1' }};border:1px solid {{ $pickup['persen']==100 ? '#d4edda' : '#ffe082' }};">
                                <div class="fw-bold fs-3" style="color:{{ $pickup['persen']==100 ? '#28a745' : '#ffc107' }};">
                                    {{ $pickup['persen'] }}%
                                </div>
                                <div class="text-muted small">Selesai</div>
                            </div>
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span><i class="bx bx-sun me-1 text-warning"></i>Siang: <strong>{{ $pickup['diambil_siang'] }}/{{ $pickup['siang_total'] }}</strong></span>
                        <span><i class="bx bx-moon me-1 text-primary"></i>Malam: <strong>{{ $pickup['diambil_malam'] }}/{{ $pickup['malam_total'] }}</strong></span>
                    </div>
                    <div class="progress mb-4" style="height:12px;border-radius:6px;">
                        <div class="progress-bar {{ $pickup['persen']==100 ? 'bg-success' : 'bg-warning' }}"
                             role="progressbar"
                             style="width:{{ $pickup['persen'] }}%;border-radius:6px;"
                             aria-valuenow="{{ $pickup['persen'] }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                    {{-- Tabel per-hari --}}
                    @if(count($periodDates) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle" style="font-size:13px;">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width:36px;">#</th>
                                    <th class="text-start">Tanggal</th>
                                    <th><i class="bx bx-sun text-warning"></i> Siang</th>
                                    <th><i class="bx bx-moon text-primary"></i> Malam</th>
                                    <th>Dikonfirmasi oleh</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($periodDates as $idx => $date)
                                @php
                                    $row      = $historyMap[$date] ?? null;
                                    $cS       = $stCfg[$row->status_siang ?? 'pending'] ?? $stCfg['pending'];
                                    $cM       = $stCfg[$row->status_malam ?? 'pending'] ?? $stCfg['pending'];
                                    $isToday  = $date === \Carbon\Carbon::today()->toDateString();
                                    $bothDone = $row && $row->status_siang === 'diambil' && $row->status_malam === 'diambil';
                                @endphp
                                <tr class="{{ $bothDone ? 'table-success' : ($isToday ? 'table-info' : '') }}">
                                    <td class="text-muted text-center">{{ $idx + 1 }}</td>
                                    <td class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('ddd, D MMM Y') }}
                                        @if($isToday)
                                            <span class="badge bg-primary ms-1" style="font-size:9px;">Hari ini</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($row)
                                        <span class="badge rounded-pill"
                                              style="background:{{ $cS['color'] }};color:#fff;font-size:11px;padding:4px 10px;">
                                            <i class="bx {{ $cS['icon'] }} me-1"></i>{{ $cS['label'] }}
                                        </span>
                                        @else
                                        <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($row)
                                        <span class="badge rounded-pill"
                                              style="background:{{ $cM['color'] }};color:#fff;font-size:11px;padding:4px 10px;">
                                            <i class="bx {{ $cM['icon'] }} me-1"></i>{{ $cM['label'] }}
                                        </span>
                                        @else
                                        <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        @if($row && $row->confirmer)
                                            <i class="bx bx-user me-1"></i>{{ $row->confirmer->name }}
                                            @if($row->confirmed_at)
                                                <div style="font-size:10px;">
                                                    {{ \Carbon\Carbon::parse($row->confirmed_at)->format('d/m H:i') }}
                                                </div>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted small mb-0">Data periode tidak tersedia.</p>
                    @endif

                </div>
            </div>
            @endif

        </div>{{-- /card-body --}}
    </div>{{-- /card --}}
</div>
@endsection
