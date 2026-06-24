@extends('layouts.app')
@include('customers.partials.package-detail-modal')
@section('title', 'Dashboard')

@push('styles')
<style>
/* ── delivery stepper ── */
.step-track { position:relative; }
.step-track::before {
    content:''; position:absolute; top:22px; left:calc(100% / 8);
    right:calc(100% / 8); height:3px; background:#e9ecef; z-index:0;
}
.step-item { position:relative; z-index:1; }
.step-circle {
    width:44px; height:44px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:18px; font-weight:700; border:2px solid transparent;
    transition: all .3s;
}
.step-done   { background:#28a745; border-color:#28a745; color:#fff; }
.step-active { background:#fff3cd; border-color:#ffc107; color:#856404; animation:pulse-step 1.5s infinite; }
.step-failed { background:#f8d7da; border-color:#dc3545; color:#842029; }
.step-idle   { background:#f8f9fa; border-color:#dee2e6; color:#adb5bd; }
@keyframes pulse-step {
    0%,100% { box-shadow:0 0 0 0 rgba(255,193,7,.4); }
    50%      { box-shadow:0 0 0 8px rgba(255,193,7,0); }
}

/* ── delivery card gradient header ── */
.delivery-header {
    background: linear-gradient(135deg, #1a5c2a, #28a745);
    color: #fff; border-radius: 14px 14px 0 0; padding: 16px 20px;
}

/* ── stat cards ── */
.stat-card {
    border-radius:14px; border:none;
    transition: transform .2s, box-shadow .2s;
}
.stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.1) !important; }
.stat-icon {
    width:48px; height:48px; border-radius:12px;
    display:flex; align-items:center; justify-content:center; font-size:22px;
}

/* ── unpaid alert ── */
.unpaid-pill {
    background:#fff; border:1.5px solid #ffc107; border-radius:12px;
    padding:10px 14px; display:flex; align-items:center; gap:10px;
    transition: box-shadow .2s;
}
.unpaid-pill:hover { box-shadow:0 4px 14px rgba(255,193,7,.25); }

/* ── history table ── */
.history-badge {
    font-size:11px; padding:3px 9px; border-radius:20px; font-weight:600;
}
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

@php
    use Illuminate\Support\Str;
    $row        = $items->first();
    $indoDate   = \Carbon\Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMMM Y');

    // Stat ringkasan
    $totalOrders  = \App\Models\Order::where('user_id', auth()->id())->count();
    $paidOrders   = \App\Models\Order::where('user_id', auth()->id())->whereIn('status',['PAID','SETTLEMENT'])->count();
    $unpaidCount  = $unpaidOrders->count();
@endphp

{{-- ── Flash --}}
@if(session('status'))
<div class="alert alert-success alert-dismissible fade show mb-4 d-flex align-items-center gap-2" role="alert">
    <i class="bx bx-check-circle fs-5"></i>
    <span>{{ session('status') }}</span>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ── Stat Cards ── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100" style="border-left:4px solid #28a745 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#d4edda;"><i class="bx bx-cart text-success"></i></div>
                <div>
                    <div class="text-muted small">Total Order</div>
                    <div class="fw-bold fs-4">{{ $totalOrders }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100" style="border-left:4px solid #17a2b8 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#d1ecf1;"><i class="bx bx-check-circle text-info"></i></div>
                <div>
                    <div class="text-muted small">Sudah Bayar</div>
                    <div class="fw-bold fs-4 text-info">{{ $paidOrders }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100" style="border-left:4px solid #ffc107 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#fff3cd;"><i class="bx bx-time-five text-warning"></i></div>
                <div>
                    <div class="text-muted small">Belum Bayar</div>
                    <div class="fw-bold fs-4 text-warning">{{ $unpaidCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow-sm h-100" style="border-left:4px solid #6f42c1 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:#e9d8fd;"><i class="bx bx-leaf" style="color:#6f42c1;"></i></div>
                <div>
                    <div class="text-muted small">Status Aktif</div>
                    <div class="fw-bold fs-4" style="color:#6f42c1;">{{ $hasActiveOrder ? 'Ya' : 'Tidak' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Unpaid reminder ── --}}
@if($unpaidOrders->isNotEmpty())
<div class="card border-0 shadow-sm mb-4" style="border-left:4px solid #ffc107 !important;border-radius:14px;">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div style="width:40px;height:40px;border-radius:10px;background:#fff3cd;display:flex;align-items:center;justify-content:center;">
                <i class="bx bx-bell text-warning fs-5"></i>
            </div>
            <div>
                <div class="fw-bold">{{ $unpaidCount }} Pesanan Menunggu Pembayaran</div>
                <div class="text-muted small">Segera selesaikan sebelum kedaluwarsa</div>
            </div>
            <a href="{{ route('customer.orders.index', ['status'=>'UNPAID']) }}"
               class="btn btn-sm btn-warning ms-auto" style="border-radius:8px;">
                Lihat Semua
            </a>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @foreach($unpaidOrders as $unpaid)
            <div class="unpaid-pill">
                <i class="bx bx-receipt text-warning"></i>
                <div>
                    <div class="fw-semibold" style="font-size:13px;">{{ $unpaid->order_number }}</div>
                    <div class="text-muted" style="font-size:12px;">{{ $unpaid->package_label }}</div>
                </div>
                <span class="fw-bold text-success ms-2" style="font-size:13px; white-space:nowrap;">
                    Rp {{ number_format($unpaid->amount_total ?? $unpaid->package_price, 0, ',', '.') }}
                </span>
                @if($unpaid->payment_method === 'transfer')
                <a href="{{ route('orders.pay', $unpaid) }}"
                   class="btn btn-sm btn-warning ms-1" style="border-radius:8px;">Bayar</a>
                @else
                <a href="{{ route('orders.finish', $unpaid) }}"
                   class="btn btn-sm btn-outline-info ms-1" style="border-radius:8px;">Info</a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Delivery Status Hari Ini ── --}}
<div class="row g-3 mb-4">
    @if(!$row)
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body text-center py-5">
                @if($hasActiveOrder)
                    <div style="font-size:48px;">🕐</div>
                    <h6 class="fw-bold mt-3 mb-1">Belum Ada Status Pengantaran Hari Ini</h6>
                    <p class="text-muted small mb-0">Status pengantaran akan muncul setelah admin memrosesnya.</p>
                @else
                    <div style="font-size:48px;">🌿</div>
                    <h6 class="fw-bold mt-3 mb-1">Belum Ada Paket Aktif</h6>
                    <p class="text-muted small mb-3">Mulai perjalanan sehatmu dengan memesan paket HeartFit.</p>
                    <a href="{{ route('orders.create') }}" class="btn btn-success px-4">
                        <i class="bx bx-plus-circle me-2"></i>Pesan Sekarang
                    </a>
                @endif
            </div>
        </div>
    </div>
    @else
    @php
        $spec      = $row->menuMakanan->spec_menu ?? [];
        $menuSiang = $spec['Makan Siang'] ?? [];
        $menuMalam = $spec['Makan Malam'] ?? [];

        $steps    = ['PENDING','DITERIMA','DIPROSES','SIAP','DIAMBIL'];
        $mapStep  = fn($s) => match(Str::lower(trim($s ?? ''))) {
            'pending'  => 1,
            'diterima' => 2,
            'diproses' => 3,
            'siap'     => 4,
            'diambil'  => 5,
            default    => 1,
        };
        $stepSiang    = $mapStep($row->status_siang);
        $stepMalam    = $mapStep($row->status_malam);
        $failedSiang  = Str::lower($row->status_siang) === 'gagal dikirim';
        $failedMalam  = Str::lower($row->status_malam) === 'gagal dikirim';

        // Render stepper HTML
        $renderStepper = function(int $cur, bool $failed) use ($steps) {
            $total = count($steps);
            $pct   = $failed ? max(0, ($cur-2)/($total-1)*100) : ($cur >= $total ? 100 : ($cur-1)/($total-1)*100);
            $html  = '<div class="position-relative py-3">';
            $html .= '<div class="position-absolute" style="top:40%;left:12%;right:12%;height:3px;background:#e9ecef;z-index:0;"></div>';
            $html .= '<div class="position-absolute" style="top:40%;left:12%;height:3px;width:'.min(100,$pct*0.76).'%;background:'.($failed?'#dc3545':'#28a745').';z-index:0;transition:width .5s;"></div>';
            $html .= '<div class="d-flex justify-content-between">';
            for ($i=1;$i<=$total;$i++) {
                $isFail   = $failed && $i === $cur;
                $isTrail  = $failed && $i < $cur;
                $isDone   = !$failed && ($cur >= $total ? true : $i < $cur);
                $isActive = !$failed && !($cur>=$total) && $i === $cur;
                $cls  = $isFail||$isTrail ? 'step-failed' : ($isDone ? 'step-done' : ($isActive ? 'step-active' : 'step-idle'));
                $icon = $isFail||$isTrail ? '✕' : ($isDone ? '✓' : ($isActive ? '⏱' : '·'));
                $lbl  = $isFail ? 'GAGAL' : $steps[$i-1];
                $html .= '<div class="d-flex flex-column align-items-center" style="flex:1;z-index:1;">';
                $html .= '<div class="step-circle '.$cls.'">'.$icon.'</div>';
                $html .= '<small class="mt-1 text-center fw-semibold" style="font-size:10px;color:'.($isFail||$isTrail?'#842029':($isDone?'#1e7e34':($isActive?'#856404':'#adb5bd'))).';">'.$lbl.'</small>';
                $html .= '</div>';
            }
            $html .= '</div></div>';
            return $html;
        };
    @endphp

    {{-- Card Siang --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
            <div class="delivery-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-sun fs-5"></i>
                    <div>
                        <div class="fw-bold">Pengantaran Siang</div>
                        <div style="font-size:12px;opacity:.8;">{{ $indoDate }}</div>
                    </div>
                    <span class="ms-auto badge"
                          style="background:rgba(255,255,255,.25);font-size:11px;">
                        Batch {{ $row->batch }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3 pb-2 border-bottom">
                    <small class="text-muted">Paket</small>
                    <div class="fw-semibold">{{ $row->mealPackage->nama_meal_package ?? 'Paket #'.$row->meal_package_id }}</div>
                </div>

                {!! $renderStepper($stepSiang, $failedSiang) !!}

                @if(!empty($menuSiang))
                <div class="mt-3">
                    <div class="text-muted small fw-semibold mb-2"><i class="bx bx-bowl-hot me-1 text-success"></i>MENU SIANG</div>
                    <ul class="list-unstyled mb-0">
                        @foreach($menuSiang as $m)
                        <li class="py-1 border-bottom d-flex align-items-center gap-2" style="font-size:13px;">
                            <i class="bx bx-check-circle text-success"></i>{{ $m }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Card Malam --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
            <div class="delivery-header" style="background:linear-gradient(135deg,#1a2a5c,#3a5bd9);">
                <div class="d-flex align-items-center gap-2">
                    <i class="bx bx-moon fs-5"></i>
                    <div>
                        <div class="fw-bold">Pengantaran Malam</div>
                        <div style="font-size:12px;opacity:.8;">{{ $indoDate }}</div>
                    </div>
                    <span class="ms-auto badge" style="background:rgba(255,255,255,.25);font-size:11px;">
                        Batch {{ $row->batch }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3 pb-2 border-bottom">
                    <small class="text-muted">Paket</small>
                    <div class="fw-semibold">{{ $row->mealPackage->nama_meal_package ?? 'Paket #'.$row->meal_package_id }}</div>
                </div>

                {!! $renderStepper($stepMalam, $failedMalam) !!}

                @if(!empty($menuMalam))
                <div class="mt-3">
                    <div class="text-muted small fw-semibold mb-2"><i class="bx bx-moon me-1 text-primary"></i>MENU MALAM</div>
                    <ul class="list-unstyled mb-0">
                        @foreach($menuMalam as $m)
                        <li class="py-1 border-bottom d-flex align-items-center gap-2" style="font-size:13px;">
                            <i class="bx bx-check-circle text-primary"></i>{{ $m }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ── CTA jika belum ada order aktif ── --}}
@if(!$hasActiveOrder)
<div class="card border-0 shadow-sm mb-4" style="border-radius:14px;background:linear-gradient(135deg,#f0fff4,#e8f5e9);">
    <div class="card-body d-flex align-items-center gap-4 flex-wrap p-4">
        <div style="font-size:52px;">🥗</div>
        <div class="flex-grow-1">
            <h5 class="fw-bold mb-1">Mulai Perjalanan Sehat Anda!</h5>
            <p class="text-muted mb-0">Pilih paket makan sehat HeartFit yang sesuai dengan kebutuhan dan anggaran kamu.</p>
        </div>
        @if(session('warning'))
        <div class="alert alert-warning py-2 px-3 mb-0">{{ session('warning') }}</div>
        @endif
        <a href="{{ route('orders.create') }}" class="btn btn-success px-4 py-2">
            <i class="bx bx-plus-circle me-2"></i>Pesan Sekarang
        </a>
    </div>
</div>
@endif

{{-- ── Riwayat Penerimaan ── --}}
@if($deliveryHistory->isNotEmpty())
<div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
    <div class="card-header bg-white d-flex align-items-center justify-content-between py-3" style="border-radius:14px 14px 0 0;">
        <div class="d-flex align-items-center gap-2">
            <div style="width:36px;height:36px;border-radius:10px;background:#d4edda;display:flex;align-items:center;justify-content:center;">
                <i class="bx bx-history text-success"></i>
            </div>
            <div>
                <div class="fw-bold">Riwayat Penerimaan</div>
                <div class="text-muted small">10 hari terakhir</div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:14px;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th class="ps-4 py-3">Tanggal</th>
                        <th>Paket</th>
                        <th class="text-center">🌞 Siang</th>
                        <th class="text-center">🌙 Malam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveryHistory as $histDate => $rows)
                    @foreach($rows as $dRow)
                    <tr>
                        <td class="ps-4 py-2">
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($histDate)->locale('id')->isoFormat('D MMM Y') }}</div>
                            <div class="text-muted" style="font-size:12px;">{{ \Carbon\Carbon::parse($histDate)->locale('id')->isoFormat('dddd') }}</div>
                        </td>
                        <td>{{ $dRow->order->package_label ?? 'Order #'.$dRow->order_id }}</td>
                        <td class="text-center">
                            @php $ss = $dRow->status_siang; @endphp
                            @if($ss === 'sampai')
                                <span class="history-badge bg-success text-white">✓ Sampai</span>
                            @elseif($ss === 'gagal dikirim')
                                <span class="history-badge bg-danger text-white">✕ Gagal</span>
                            @elseif($ss === 'sedang dikirim')
                                <span class="history-badge bg-info text-white">⏱ Dikirim</span>
                            @else
                                <span class="history-badge bg-light text-muted">{{ ucfirst($ss) }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php $sm = $dRow->status_malam; @endphp
                            @if($sm === 'sampai')
                                <span class="history-badge bg-success text-white">✓ Sampai</span>
                            @elseif($sm === 'gagal dikirim')
                                <span class="history-badge bg-danger text-white">✕ Gagal</span>
                            @elseif($sm === 'sedang dikirim')
                                <span class="history-badge bg-info text-white">⏱ Dikirim</span>
                            @else
                                <span class="history-badge bg-light text-muted">{{ ucfirst($sm) }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ── Katalog Paket ── --}}
<div class="card border-0 shadow-sm" style="border-radius:14px;">
    <div class="card-header bg-white text-center py-4" style="border-radius:14px 14px 0 0;border-bottom:1px solid #f0f0f0;">
        <h5 class="fw-bold mb-1">HeartFit Diet Packages 🥗</h5>
        <p class="text-muted mb-0 small">Pilihan paket makan sehat untuk kebutuhan harian Anda</p>
    </div>
    <div class="card-body">
        <div class="row g-3 justify-content-center">
            @foreach($packages as $key => $pkg)
            @if(($pkg['meal_packages'] ?? collect())->count() > 0)
            @php
                $prices   = collect($pkg['meal_packages'])->pluck('price')->sort()->values();
                $minPrice = $prices->first();
                $maxPrice = $prices->last();
                $icons    = ['reguler'=>'🥦','premium'=>'🥩','personal'=>'🌟'];
                $icon     = $icons[$key] ?? '🍱';
            @endphp
            <div class="col-sm-6 col-lg-4">
                <div class="card border h-100" style="border-radius:14px;transition:all .2s;cursor:pointer;"
                     onmouseenter="this.style.cssText='border-radius:14px;transform:translateY(-4px);box-shadow:0 10px 30px rgba(40,167,69,.15);border-color:#28a745;'"
                     onmouseleave="this.style.cssText='border-radius:14px;transition:all .2s;'"
                     onclick="openPackageModal('{{ $key }}')">
                    <div class="card-body text-center py-4">
                        <div style="font-size:40px;">{{ $icon }}</div>
                        <h6 class="fw-bold text-uppercase mt-2 mb-1">{{ $pkg['type'] }}</h6>
                        <div class="text-success fw-bold mb-3" style="font-size:18px;">
                            Rp {{ number_format($minPrice, 0, ',', '.') }}
                            @if($minPrice != $maxPrice)
                                <span class="text-muted fw-normal" style="font-size:13px;">
                                    — {{ number_format($maxPrice, 0, ',', '.') }}
                                </span>
                            @endif
                            <span class="d-block text-muted fw-normal" style="font-size:12px;">per paket</span>
                        </div>
                        <ul class="list-unstyled text-start small mb-3">
                            @if($key==='reguler')
                                <li class="py-1"><i class="bx bx-check text-success me-2"></i>Menu seimbang harian</li>
                                <li class="py-1"><i class="bx bx-check text-success me-2"></i>Pilihan karbo sehat</li>
                                <li class="py-1"><i class="bx bx-check text-success me-2"></i>Siang & malam</li>
                            @elseif($key==='premium')
                                <li class="py-1"><i class="bx bx-check text-success me-2"></i>Menu premium pilihan</li>
                                <li class="py-1"><i class="bx bx-check text-success me-2"></i>Protein tinggi</li>
                                <li class="py-1"><i class="bx bx-check text-success me-2"></i>Nutrisi optimal</li>
                            @elseif($key==='personal')
                                <li class="py-1"><i class="bx bx-check text-success me-2"></i>Menu personal custom</li>
                                <li class="py-1"><i class="bx bx-check text-success me-2"></i>Konsultasi ahli gizi</li>
                                <li class="py-1"><i class="bx bx-check text-success me-2"></i>Program khusus</li>
                            @else
                                <li class="py-1"><i class="bx bx-check text-success me-2"></i>Beragam pilihan menu</li>
                            @endif
                        </ul>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-success flex-fill" onclick="event.stopPropagation();openPackageModal('{{ $key }}')">
                                <i class="bx bx-info-circle me-1"></i>Detail
                            </button>
                            <a href="{{ route('orders.create') }}" class="btn btn-success flex-fill" onclick="event.stopPropagation();">
                                <i class="bx bx-cart me-1"></i>Pesan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>

</div>

@push('scripts')
<script>
const modalData = @json($packages);
</script>
@endpush
@endsection
