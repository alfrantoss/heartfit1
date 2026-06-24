@extends('layouts.app')

@section('title', 'Detail Order Personal — Konsultasi Gizi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Breadcrumb --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-clipboard me-2 text-primary"></i>Konsultasi Gizi — Paket Personal
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('ahli_gizi.orders') }}">Dashboard Ahli Gizi</a></li>
                    <li class="breadcrumb-item active">Detail Order #{{ $order->order_number }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('ahli_gizi.orders') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </div>

    @if(session('konsul_saved'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> <strong>Session konsultasi berhasil disimpan!</strong>
            Menu yang dipilih sudah tercatat untuk sesi konsultasi ini.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('konsul_cleared'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bx bx-info-circle me-1"></i> Session konsultasi berhasil direset.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 justify-content-center">

        {{-- ─── KOLOM TENGAH: Info Order + Customer ─── --}}
        <div class="col-lg-8">

            {{-- Info Customer --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header bg-white d-flex align-items-center gap-2 py-3" style="border-radius:14px 14px 0 0; border-bottom:1px solid #f0f0f0;">
                    <div style="width:36px;height:36px;border-radius:10px;background:#e3f2fd;display:flex;align-items:center;justify-content:center;color:#0d6efd;">
                        <i class="bx bx-user fs-5"></i>
                    </div>
                    <h6 class="mb-0 fw-bold">Informasi Customer</h6>
                </div>
                <div class="card-body py-4">
                    <table class="table table-borderless mb-0 align-middle">
                        <tr>
                            <td class="text-muted py-2" style="width:35%;font-size:14px;">Nama Lengkap</td>
                            <td class="fw-semibold text-dark py-2">{{ $order->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">Alamat Email</td>
                            <td class="py-2">{{ $order->user?->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">No. HP / WA</td>
                            <td class="py-2">
                                @if($order->user?->detail?->hp)
                                    <span class="me-2 fw-medium">{{ $order->user->detail->hp }}</span>
                                    <a href="{{ route('ahli_gizi.wa', $order->user->id) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-success py-1 px-3 rounded-pill" style="font-size:12px;">
                                        <i class="bx bxl-whatsapp me-1"></i>Hubungi
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">Alamat</td>
                            <td class="py-2">
                                <div class="bg-light p-2 rounded-2" style="font-size:13.5px;">
                                    {{ $order->user?->detail?->alamat ?? '-' }}
                                </div>
                            </td>
                        </tr>
                        @if($order->user?->detail?->nik)
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">NIK</td>
                            <td class="py-2 font-monospace">{{ $order->user->detail->nik }}</td>
                        </tr>
                        @endif
                        @if($order->user?->detail?->mr)
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">No. Rekam Medis (MR)</td>
                            <td class="py-2"><span class="badge bg-info px-3 py-2 rounded-pill">{{ $order->user->detail->mr }}</span></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Info Order --}}
            <div class="card mb-4 border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header bg-white d-flex align-items-center gap-2 py-3" style="border-radius:14px 14px 0 0; border-bottom:1px solid #f0f0f0;">
                    <div style="width:36px;height:36px;border-radius:10px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;color:#198754;">
                        <i class="bx bx-receipt fs-5"></i>
                    </div>
                    <h6 class="mb-0 fw-bold">Informasi Pesanan</h6>
                </div>
                <div class="card-body py-4">
                    <table class="table table-borderless mb-0 align-middle">
                        <tr>
                            <td class="text-muted py-2" style="width:35%;font-size:14px;">No. Order</td>
                            <td class="py-2 fw-bold text-dark font-monospace">{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">Status</td>
                            <td class="py-2">
                                @if(in_array($order->status, ['PAID','SETTLEMENT']))
                                    <span class="badge bg-success px-3 py-2 rounded-pill"><i class="bx bx-check-circle me-1"></i>LUNAS</span>
                                @else
                                    <span class="badge bg-warning px-3 py-2 rounded-pill">{{ $order->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">Paket</td>
                            <td class="py-2 fw-semibold text-primary">{{ $order->package_label }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">Kategori</td>
                            <td class="py-2"><span class="badge bg-label-primary px-3 py-2 rounded-pill text-uppercase">{{ str_replace('_', ' ', $order->package_key ?? 'PERSONAL') }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">Periode Program</td>
                            <td class="py-2">
                                <div class="d-inline-flex align-items-center gap-2 bg-light px-3 py-2 rounded-pill" style="font-size:13.5px;">
                                    <i class="bx bx-calendar text-muted"></i>
                                    <span>{{ $order->start_date ? $order->start_date->locale('id')->isoFormat('D MMM Y') : '-' }}</span>
                                    <span class="text-muted">s.d</span>
                                    <span>{{ $order->end_date ? $order->end_date->locale('id')->isoFormat('D MMM Y') : '-' }}</span>
                                    <span class="badge bg-secondary ms-1">{{ $order->days ?? 0 }} hari</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">Metode Bayar</td>
                            <td class="py-2 text-uppercase fw-semibold text-secondary">{{ str_replace('_', ' ', $order->payment_method ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-2" style="font-size:14px;">Total Tagihan</td>
                            <td class="py-2 fw-bold text-success" style="font-size:16px;">
                                Rp {{ number_format($order->amount_total ?? $order->package_price ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Catatan Customer --}}
            @if(!empty($order->notes))
            <div class="card mb-4 border-0 shadow-sm" style="border-radius:14px; background:#fffdf2;">
                <div class="card-header bg-transparent d-flex align-items-center gap-2 py-3 border-0">
                    <div style="width:36px;height:36px;border-radius:10px;background:#fff3cd;display:flex;align-items:center;justify-content:center;color:#ffc107;">
                        <i class="bx bx-comment-dots fs-5"></i>
                    </div>
                    <h6 class="mb-0 fw-bold text-warning">Catatan dari Customer</h6>
                </div>
                <div class="card-body pt-0 pb-4">
                    <div class="p-3 bg-white rounded-3 border" style="font-size:14.5px;color:#555;">
                        "{{ $order->notes }}"
                    </div>
                </div>
            </div>
            @endif

            {{-- Menu Unik dari Order --}}
            @if($order->unique_menus && count($order->unique_menus) > 0)
            <div class="card mb-4 border-0 shadow-sm" style="border-radius:14px;">
                <div class="card-header bg-white d-flex align-items-center gap-2 py-3" style="border-radius:14px 14px 0 0; border-bottom:1px solid #f0f0f0;">
                    <div style="width:36px;height:36px;border-radius:10px;background:#f3e5f5;display:flex;align-items:center;justify-content:center;color:#9c27b0;">
                        <i class="bx bx-food-menu fs-5"></i>
                    </div>
                    <h6 class="mb-0 fw-bold">Menu Terpilih Saat Order <span class="badge bg-secondary ms-1 rounded-pill">{{ $order->unique_menu_count ?? count($order->unique_menus) }}</span></h6>
                </div>
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($order->unique_menus as $menu)
                            <div class="bg-light px-3 py-2 rounded-pill fw-medium text-dark border" style="font-size:13.5px;">
                                <i class="bx bx-check-circle text-success me-1"></i> {{ $menu }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>{{-- /kolom tengah --}}

    </div>{{-- /row --}}

</div>
@endsection
