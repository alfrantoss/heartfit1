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

    <div class="row g-4">

        {{-- ─── KOLOM KIRI: Info Order + Customer ─── --}}
        <div class="col-lg-5">

            {{-- Info Customer --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            <i class="bx bx-user"></i>
                        </span>
                    </span>
                    <h6 class="mb-0">Informasi Customer</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">Nama</td>
                            <td class="fw-semibold">{{ $order->user?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td>{{ $order->user?->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. HP / WA</td>
                            <td>
                                @if($order->user?->detail?->hp)
                                    <span class="me-2">{{ $order->user->detail->hp }}</span>
                                    <a href="{{ route('ahli_gizi.wa', $order->user->id) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-success py-0 px-2">
                                        <i class="bx bxl-whatsapp me-1"></i>WA
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>{{ $order->user?->detail?->alamat ?? '-' }}</td>
                        </tr>
                        @if($order->user?->detail?->nik)
                        <tr>
                            <td class="text-muted">NIK</td>
                            <td>{{ $order->user->detail->nik }}</td>
                        </tr>
                        @endif
                        @if($order->user?->detail?->mr)
                        <tr>
                            <td class="text-muted">No. MR</td>
                            <td><span class="badge bg-label-info">{{ $order->user->detail->mr }}</span></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Info Order --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-label-info">
                            <i class="bx bx-receipt"></i>
                        </span>
                    </span>
                    <h6 class="mb-0">Informasi Order</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:40%">No. Order</td>
                            <td class="fw-semibold font-monospace">{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
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
                            <td class="text-muted">Paket</td>
                            <td>{{ $order->package_label }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kategori</td>
                            <td>
                                <span class="badge bg-label-primary">{{ $order->package_category ?? '-' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Periode</td>
                            <td>
                                {{ $order->start_date ? $order->start_date->format('d M Y') : '-' }}
                                s.d
                                {{ $order->end_date ? $order->end_date->format('d M Y') : '-' }}
                                <small class="text-muted">({{ $order->days ?? 0 }} hari)</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Metode Bayar</td>
                            <td class="text-uppercase">{{ str_replace('_', ' ', $order->payment_method ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total</td>
                            <td class="fw-bold text-success">
                                Rp {{ number_format($order->amount_total ?? $order->package_price ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Catatan Customer --}}
            @if(!empty($order->notes))
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning bg-opacity-10 d-flex align-items-center gap-2">
                    <i class="bx bx-comment-dots text-warning"></i>
                    <h6 class="mb-0">Catatan dari Customer</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->notes }}</p>
                </div>
            </div>
            @endif

            {{-- Menu Unik dari Order --}}
            @if($order->unique_menus && count($order->unique_menus) > 0)
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bx bx-food-menu text-primary"></i>
                    <h6 class="mb-0">Menu Terpilih Saat Order ({{ $order->unique_menu_count ?? count($order->unique_menus) }})</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($order->unique_menus as $menu)
                            <span class="badge bg-label-info">{{ $menu }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>{{-- /kolom kiri --}}

        {{-- ─── KOLOM KANAN: Session Konsultasi Menu ─── --}}
        <div class="col-lg-7">

            {{-- Session Konsultasi Panel --}}
            <div class="card border-primary">
                <div class="card-header bg-primary bg-opacity-10 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-clipboard-check text-primary fs-5"></i>
                        <div>
                            <h6 class="mb-0 fw-bold text-primary">Session Konsultasi Menu Personal</h6>
                            <small class="text-muted">
                                Pilih menu yang direkomendasikan untuk customer ini. Tersimpan selama sesi browser.
                            </small>
                        </div>
                    </div>
                    @if(!empty($sessionMenus))
                        <span class="badge bg-primary rounded-pill">{{ count($sessionMenus) }} menu dipilih</span>
                    @endif
                </div>

                <div class="card-body">

                    {{-- Tampilkan menu yang sudah dipilih di session --}}
                    @if(!empty($sessionMenus))
                    <div class="mb-4">
                        <h6 class="text-success fw-semibold mb-2">
                            <i class="bx bx-check-circle me-1"></i>Menu Konsultasi Tersimpan
                        </h6>
                        <div class="d-flex flex-wrap gap-2 p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                            @foreach($sessionMenus as $idx => $sm)
                                <div class="d-flex align-items-center gap-1 badge bg-success text-white fs-6 fw-normal py-2 px-3">
                                    <span>{{ $sm }}</span>
                                    <form method="POST"
                                          action="{{ route('ahli_gizi.session.remove', $order->id) }}"
                                          class="d-inline ms-1">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="menu_idx" value="{{ $idx }}">
                                        <button type="submit"
                                                class="btn p-0 border-0 bg-transparent text-white lh-1"
                                                title="Hapus dari session"
                                                style="font-size:14px;">&times;</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        {{-- Catatan konsultasi --}}
                        @if(session('konsul_note_' . $order->id))
                        <div class="mt-3 p-3 bg-light rounded-3 border">
                            <div class="small text-muted mb-1"><i class="bx bx-note me-1"></i>Catatan Konsultasi:</div>
                            <div>{{ session('konsul_note_' . $order->id) }}</div>
                        </div>
                        @endif

                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('ahli_gizi.session.share', $order->id) }}"
                               class="btn btn-success btn-sm"
                               target="_blank">
                                <i class="bx bxl-whatsapp me-1"></i>Kirim via WA
                            </a>
                            <form method="POST" action="{{ route('ahli_gizi.session.clear', $order->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Reset session konsultasi ini?')">
                                    <i class="bx bx-trash me-1"></i>Reset Session
                                </button>
                            </form>
                        </div>
                    </div>

                    <hr>
                    @endif

                    {{-- Form tambah menu ke session --}}
                    <div class="mb-3">
                        <h6 class="fw-semibold mb-3">
                            <i class="bx bx-plus-circle me-1 text-primary"></i>
                            Tambah Menu ke Session Konsultasi
                        </h6>

                        <form method="POST" action="{{ route('ahli_gizi.session.add', $order->id) }}">
                            @csrf

                            {{-- Pilih dari daftar menu yang ada --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pilih Menu dari Daftar</label>
                                <select name="menu_from_list[]"
                                        id="menuSelect"
                                        class="form-select"
                                        multiple
                                        size="6">
                                    @foreach($availableMenus as $am)
                                        <option value="{{ $am->nama_menu }}"
                                            {{ in_array($am->nama_menu, $sessionMenus ?? []) ? 'selected' : '' }}>
                                            {{ $am->nama_menu }}
                                            @if($am->spec_menu && is_array($am->spec_menu) && count($am->spec_menu) > 0)
                                                — ({{ implode(', ', array_keys($am->spec_menu)) }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Tahan Ctrl/Cmd untuk pilih lebih dari satu</small>
                            </div>

                            {{-- Input menu manual --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Atau Tambah Menu Manual</label>
                                <input type="text"
                                       name="menu_manual"
                                       class="form-control"
                                       placeholder="Contoh: Nasi Merah + Ayam Bakar Kecap">
                                <small class="text-muted">Isi jika menu tidak ada di daftar</small>
                            </div>

                            {{-- Catatan konsultasi --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Catatan Konsultasi <span class="text-muted fw-normal">(opsional)</span></label>
                                <textarea name="konsul_note"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Mis: Kurangi garam, hindari gorengan, tambah porsi sayur...">{{ session('konsul_note_' . $order->id) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i>Simpan ke Session
                            </button>
                        </form>
                    </div>

                    {{-- Preview menu yang tersedia --}}
                    @if($availableMenus->isNotEmpty())
                    <hr>
                    <div>
                        <h6 class="fw-semibold mb-2 d-flex align-items-center justify-content-between">
                            <span><i class="bx bx-food-menu me-1 text-muted"></i>Referensi Menu Tersedia</span>
                            <button class="btn btn-sm btn-outline-secondary py-0"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#menuPreviewCollapse">
                                <i class="bx bx-chevron-down"></i> Lihat
                            </button>
                        </h6>
                        <div class="collapse" id="menuPreviewCollapse">
                            <div class="row g-2 mt-1">
                                @foreach($availableMenus as $am)
                                <div class="col-sm-6">
                                    <div class="rounded-3 p-2 border bg-light d-flex align-items-start gap-2">
                                        @if(!empty($am->foto_makanan[0]))
                                            <img src="{{ asset('storage/' . $am->foto_makanan[0]) }}"
                                                 class="rounded"
                                                 style="width:44px;height:44px;object-fit:cover;"
                                                 alt="{{ $am->nama_menu }}">
                                        @else
                                            <div class="rounded bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0"
                                                 style="width:44px;height:44px;">
                                                <i class="bx bx-bowl-hot text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="overflow-hidden">
                                            <div class="fw-semibold text-truncate small">{{ $am->nama_menu }}</div>
                                            @if($am->spec_menu && is_array($am->spec_menu))
                                                @foreach($am->spec_menu as $section => $items)
                                                <div class="text-muted" style="font-size:11px;">
                                                    <span class="fw-semibold">{{ $section }}:</span>
                                                    {{ is_array($items) ? implode(', ', $items) : $items }}
                                                </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                </div>{{-- /card-body --}}
            </div>{{-- /card konsultasi --}}

        </div>{{-- /kolom kanan --}}

    </div>{{-- /row --}}

</div>
@endsection
