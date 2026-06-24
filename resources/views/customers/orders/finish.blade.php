@extends('layouts.app')
@section('title','Selesai')

@push('styles')
<style>
  .page-wrap { max-width: 760px; }
  .kv-card .kv-row + .kv-row{border-top:1px dashed #eef2f7}
  .kv-label{
    flex:0 0 160px; max-width:160px;
    font-size:.8rem; text-transform:uppercase; letter-spacing:.04em;
    color:var(--bs-secondary-color);
  }
  .kv-value{flex:1; color:var(--bs-emphasis-color); font-weight:600}
  .kv-mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace}
  .badge-soft{
    background:#f6f9ff; border:1px solid #e5edff; color:#3b82f6; font-weight:600;
  }
  .section-title{letter-spacing:.02em}
</style>
@endpush

@section('content')
@php
  use Carbon\Carbon;
  $start = $order->start_date instanceof \Carbon\Carbon ? $order->start_date : Carbon::parse($order->start_date);
  $end   = $order->end_date   instanceof \Carbon\Carbon ? $order->end_date   : Carbon::parse($order->end_date);

  $grandTotal   = (int)($order->amount_total ?? $order->package_price ?? 0);
  $methodLabel  = strtoupper(str_replace('_',' ', $order->payment_method ?? '-'));
  $statusBadge  = match($order->status){
    'PAID'    => 'bg-success',
    'PENDING' => 'bg-info',
    'FAILED'  => 'bg-danger',
    'EXPIRED' => 'bg-secondary',
    'UNPAID'  => 'bg-warning',
    default   => 'bg-secondary',
  };
@endphp

<div class="container py-5 d-flex justify-content-center">
  <div class="page-wrap w-100">

    {{-- Header --}}
    <div class="text-center mb-3">
      <h3 class="fw-bold text-primary mb-2">Terima kasih!</h3>
      <p class="text-muted mb-1">Nomor Order:</p>
      <div class="kv-mono fs-6">{{ $order->order_number }}</div>
    </div>

    {{-- Alert Status --}}
    @if($order->status === 'PAID')
      <div class="alert alert-success mx-auto" style="max-width:640px;">
        Pembayaran <strong>berhasil</strong>. Kami akan segera memproses pesanan Anda.
        @if(!empty($order->paid_at))
          <div class="mt-1 small mb-0 text-success-700">Dibayar pada: <strong>{{ \Carbon\Carbon::parse($order->paid_at)->toDateTimeString() }}</strong></div>
        @endif
      </div>
    @elseif($order->status === 'UNPAID' && $order->payment_method === 'transfer')
      <div class="alert alert-info mx-auto" style="max-width:640px;">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <strong>Menunggu pembayaran</strong>. Halaman ini akan otomatis diperbarui setelah pembayaran berhasil.
            <div class="small text-muted mt-1">
              <i class="bx bx-time me-1"></i>Auto-check setiap 5 detik (maksimal 5 menit)
            </div>
          </div>
          <button onclick="manualCheck()" class="btn btn-sm btn-outline-info">
            <i class="bx bx-refresh me-1"></i>Cek Sekarang
          </button>
        </div>
      </div>
    @elseif($order->status === 'EXPIRED')
      <div class="alert alert-warning mx-auto" style="max-width:640px;">
        <div class="d-flex align-items-center">
          <i class="bx bx-error-circle me-2 fs-5"></i>
          <div>
            <strong>Pembayaran expired</strong>. Anda memiliki dua pilihan:
            <ul class="mb-0 mt-2 small">
              <li><strong>Bayar Ulang</strong> - Lanjutkan pembayaran order yang sama (waktu: 5 menit)</li>
              <li><strong>Pesan Ulang</strong> - Buat order baru dengan paket yang sama</li>
            </ul>
          </div>
        </div>
      </div>
    @elseif(($order->payment_method ?? '') === 'cod')
      <div class="alert alert-info mx-auto" style="max-width:640px;">
        Metode <strong>COD</strong> dipilih. Silakan siapkan pembayaran saat pesanan diantar.
      </div>
    @else
      <div class="alert alert-warning mx-auto" style="max-width:640px;">
        Status saat ini: <strong>{{ $order->status }}</strong>. 
        @if($order->payment_method === 'transfer')
          Halaman ini akan otomatis diperbarui setelah pembayaran berhasil.
          <button onclick="manualCheck()" class="btn btn-sm btn-outline-warning ms-2">
            <i class="bx bx-refresh me-1"></i>Cek Status
          </button>
        @else
          Jika Anda sudah membayar, halaman ini akan diperbarui setelah notifikasi diterima.
        @endif
      </div>
    @endif

    <div class="text-center">
      @if($order->status === 'EXPIRED')
        {{-- Bayar Ulang untuk transfer --}}
        @if($order->payment_method === 'transfer')
          <a href="{{ route('orders.pay', $order) }}" class="btn btn-success mt-2">
            <i class="bx bx-time me-1"></i>Bayar Ulang (5 Menit)
          </a>
        @endif
        <a href="{{ route('orders.create') }}?package_key={{ $order->package_key }}" class="btn btn-warning mt-2">
          <i class="bx bx-refresh me-1"></i>Pesan Ulang Paket
        </a>
      @endif
      <a href="{{ route('orders.create') }}" class="btn btn-primary mt-2">Pesan Paket Lain</a>
      <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary mt-2">Lihat Orderan Kamu</a>
    </div>

    {{-- Ringkasan --}}
    <div class="card border-0 shadow-sm mt-4 mx-auto kv-card">
      <div class="card-body">
        <h5 class="fw-semibold mb-3 text-center section-title">Ringkasan</h5>

        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Paket</div>
          <div class="kv-value fw-semibold">
            {{ $order->package_label }}
            <span class="text-muted fw-normal">({{ $order->package_category }})</span>
            @if(!empty($order->package_batch))
              <span class="badge badge-soft rounded-pill ms-1">BATCH: {{ $order->package_batch }}</span>
            @endif
          </div>
        </div>

        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Periode</div>
          <div class="kv-value">
            {{ $start->toDateString() }} s/d {{ $end->toDateString() }}
            <span class="text-muted fw-normal">({{ (int)$order->days }} hari)</span>
          </div>
        </div>

        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Total</div>
          <div class="kv-value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
        </div>

        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Metode</div>
          <div class="kv-value">{{ $methodLabel }}</div>
        </div>

        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Status</div>
          <div class="kv-value">
            <span class="badge {{ $statusBadge }}">{{ $order->status }}</span>
          </div>
        </div>

        @if(!empty($order->notes))
          <div class="kv-row d-flex align-items-start py-2">
            <div class="kv-label">Catatan</div>
            <div class="kv-value">
              <div class="alert alert-info small mb-0 py-2">
                <i class="bx bx-info-circle me-1"></i>{{ $order->notes }}
              </div>
            </div>
          </div>
        @endif

        {{-- Status Pengambilan Hari Ini --}}
        @if(in_array(strtoupper($order->status), ['PAID','SETTLEMENT']))
        @php
          $todayDate     = \Carbon\Carbon::today();
          $isActiveToday = $order->start_date && $order->end_date
              && $todayDate->between($order->start_date, $order->end_date);
          $deliveryToday = null;
          if ($isActiveToday) {
              $deliveryToday = \App\Models\OrderDeliveryStatus::where('meal_package_id', $order->package_key)
                  ->whereDate('delivery_date', $todayDate)->first();
          }
          $pickupCfg = [
              'pending'  => ['color'=>'#6c757d','icon'=>'bx-time',        'label'=>'Menunggu'],
              'diterima' => ['color'=>'#17a2b8','icon'=>'bx-package',     'label'=>'Diterima HeartFit'],
              'diproses' => ['color'=>'#fd7e14','icon'=>'bx-bowl-hot',    'label'=>'Sedang Diproses'],
              'siap'     => ['color'=>'#6f42c1','icon'=>'bx-bell',        'label'=>'Siap Diambil 🔔'],
              'diambil'  => ['color'=>'#28a745','icon'=>'bx-check-circle','label'=>'Sudah Diambil ✓'],
          ];
        @endphp
        @if($isActiveToday)
        <div class="kv-row d-flex align-items-start py-2">
          <div class="kv-label">Status Hari Ini</div>
          <div class="kv-value">
            @if($deliveryToday)
              @php
                $cfgS = $pickupCfg[$deliveryToday->status_siang] ?? $pickupCfg['pending'];
                $cfgM = $pickupCfg[$deliveryToday->status_malam] ?? $pickupCfg['pending'];
              @endphp
              <div class="d-flex flex-wrap gap-2 mb-1">
                <span class="badge rounded-pill" style="background:{{ $cfgS['color'] }};color:#fff;font-size:11px;padding:5px 12px;">
                  <i class="bx {{ $cfgS['icon'] }} me-1"></i>Siang: {{ $cfgS['label'] }}
                </span>
                <span class="badge rounded-pill" style="background:{{ $cfgM['color'] }};color:#fff;font-size:11px;padding:5px 12px;">
                  <i class="bx {{ $cfgM['icon'] }} me-1"></i>Malam: {{ $cfgM['label'] }}
                </span>
              </div>
              <small class="text-muted"><i class="bx bx-bell me-1"></i>Notifikasi WhatsApp dikirim otomatis saat status berubah.</small>
            @else
              <span class="text-muted small">Jadwal pengambilan hari ini belum tersedia.</span>
            @endif
          </div>
        </div>
        @endif
        @endif

      </div>
    </div>

    {{-- ── Progres & Riwayat Pengambilan (hanya PAID/SETTLEMENT) ── --}}
    @if(in_array(strtoupper($order->status), ['PAID','SETTLEMENT']))
    @php
      $pickup  = $order->getPickupProgress();
      $history = $order->getPickupHistory();

      $statusCfg = [
          'pending'  => ['color'=>'#6c757d','icon'=>'bx-time',        'label'=>'Menunggu'],
          'diterima' => ['color'=>'#17a2b8','icon'=>'bx-package',     'label'=>'Diterima'],
          'diproses' => ['color'=>'#fd7e14','icon'=>'bx-bowl-hot',    'label'=>'Diproses'],
          'siap'     => ['color'=>'#6f42c1','icon'=>'bx-bell',        'label'=>'Siap Diambil 🔔'],
          'diambil'  => ['color'=>'#28a745','icon'=>'bx-check-circle','label'=>'Diambil ✓'],
      ];

      // Map delivery_date (string Y-m-d) => row
      $historyMap = $history->keyBy(fn($r) => \Carbon\Carbon::parse($r->delivery_date)->toDateString());

      // Semua tanggal dalam periode order
      $periodDates = [];
      if ($order->start_date && $order->end_date) {
          $cur = (\Carbon\Carbon::parse($order->start_date))->copy();
          $eod =  \Carbon\Carbon::parse($order->end_date);
          while ($cur->lte($eod)) {
              $periodDates[] = $cur->toDateString();
              $cur->addDay();
          }
      }
    @endphp

    <div class="card border-0 shadow-sm mt-4 mx-auto" style="max-width:760px;">
      <div class="card-body">
        <h5 class="fw-semibold mb-4 text-center section-title">
          <i class="bx bx-store-alt text-success me-2"></i>Progres Pengambilan
        </h5>

        {{-- Summary angka --}}
        <div class="row g-3 mb-4 text-center">
          <div class="col-4">
            <div class="p-3 rounded-3" style="background:#f0fff4;border:1px solid #d4edda;">
              <div class="fw-bold fs-4 text-success">{{ $pickup['total_diambil'] }}</div>
              <div class="text-muted small">Sesi Diambil</div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-3 rounded-3" style="background:#f8f9fa;border:1px solid #dee2e6;">
              <div class="fw-bold fs-4 text-secondary">{{ $pickup['total_sesi'] }}</div>
              <div class="text-muted small">Total Sesi</div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-3 rounded-3" style="background:{{ $pickup['persen'] == 100 ? '#f0fff4' : '#fff8e1' }};border:1px solid {{ $pickup['persen'] == 100 ? '#d4edda' : '#ffe082' }};">
              <div class="fw-bold fs-4" style="color:{{ $pickup['persen'] == 100 ? '#28a745' : '#ffc107' }};">
                {{ $pickup['persen'] }}%
              </div>
              <div class="text-muted small">Selesai</div>
            </div>
          </div>
        </div>

        {{-- Progress bar --}}
        <div class="mb-1 d-flex justify-content-between small text-muted">
          <span><i class="bx bx-sun me-1 text-warning"></i>Siang: <strong>{{ $pickup['diambil_siang'] }}/{{ $pickup['siang_total'] }}</strong></span>
          <span><i class="bx bx-moon me-1 text-primary"></i>Malam: <strong>{{ $pickup['diambil_malam'] }}/{{ $pickup['malam_total'] }}</strong></span>
        </div>
        <div class="progress mb-4" style="height:12px;border-radius:6px;">
          <div class="progress-bar {{ $pickup['persen'] == 100 ? 'bg-success' : 'bg-warning' }}"
               role="progressbar" style="width:{{ $pickup['persen'] }}%;border-radius:6px;"
               aria-valuenow="{{ $pickup['persen'] }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        {{-- Tabel per-hari --}}
        @if(count($periodDates) > 0)
        <div class="table-responsive">
          <table class="table table-sm table-bordered align-middle mb-0" style="font-size:13px;">
            <thead class="table-light text-center">
              <tr>
                <th style="width:36px;">#</th>
                <th class="text-start">Tanggal</th>
                <th><i class="bx bx-sun text-warning"></i> Siang</th>
                <th><i class="bx bx-moon text-primary"></i> Malam</th>
              </tr>
            </thead>
            <tbody>
              @foreach($periodDates as $idx => $date)
              @php
                $row  = $historyMap[$date] ?? null;
                $cS   = $statusCfg[$row->status_siang ?? 'pending'] ?? $statusCfg['pending'];
                $cM   = $statusCfg[$row->status_malam ?? 'pending'] ?? $statusCfg['pending'];
                $isToday  = $date === \Carbon\Carbon::today()->toDateString();
                $bothDone = $row && $row->status_siang === 'diambil' && $row->status_malam === 'diambil';
              @endphp
              <tr class="{{ $bothDone ? 'table-success' : ($isToday ? 'table-info' : '') }}">
                <td class="text-muted text-center">{{ $idx + 1 }}</td>
                <td class="fw-semibold">
                  {{ \Carbon\Carbon::parse($date)->locale('id')->isoFormat('ddd, D MMM Y') }}
                  @if($isToday)<span class="badge bg-primary ms-1" style="font-size:9px;">Hari ini</span>@endif
                </td>
                <td class="text-center">
                  @if($row)
                  <span class="badge rounded-pill" style="background:{{ $cS['color'] }};color:#fff;font-size:11px;padding:4px 10px;">
                    <i class="bx {{ $cS['icon'] }} me-1"></i>{{ $cS['label'] }}
                  </span>
                  @else
                  <span class="text-muted small">—</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($row)
                  <span class="badge rounded-pill" style="background:{{ $cM['color'] }};color:#fff;font-size:11px;padding:4px 10px;">
                    <i class="bx {{ $cM['icon'] }} me-1"></i>{{ $cM['label'] }}
                  </span>
                  @else
                  <span class="text-muted small">—</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
          <p class="text-muted text-center small mb-0">Data periode tidak tersedia.</p>
        @endif

      </div>
    </div>
    @endif

  </div>
</div>

{{-- Auto-refresh untuk pembayaran pending --}}
@if(in_array($order->status, ['UNPAID', 'EXPIRED']) && $order->payment_method === 'transfer')
@push('scripts')
<script>
let checkInterval;
let isChecking = false;

async function checkPaymentStatus() {
    if (isChecking) return;
    isChecking = true;
    try {
        const response = await fetch(`{{ route('orders.check-payment', $order) }}`);
        const data = await response.json();
        if (data.status_changed) {
            clearInterval(checkInterval);
            if (data.status === 'PAID') alert('✅ ' + data.message);
            else if (data.status === 'EXPIRED') alert('⏰ ' + data.message);
            window.location.reload();
        }
    } catch (e) {
        console.error('Error checking payment:', e);
    } finally {
        isChecking = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    checkInterval = setInterval(checkPaymentStatus, 5000);
    setTimeout(() => clearInterval(checkInterval), 300000);
});

function manualCheck() { checkPaymentStatus(); }
</script>
@endpush
@endif
@endsection
