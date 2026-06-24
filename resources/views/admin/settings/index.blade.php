@extends('layouts.app')
@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bx bx-cog text-primary me-2"></i>Pengaturan Sistem</h4>
            <p class="text-muted mb-0 small">Kelola konfigurasi email, notifikasi WhatsApp, dan token API</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bx bx-check-circle fs-5"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bx bx-x-circle fs-5"></i>
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- ===== KOLOM KIRI ===== --}}
            <div class="col-lg-6">

                {{-- Fonnte WhatsApp Token --}}
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar" style="width:38px;height:38px;background:linear-gradient(135deg,#25D366,#128C7E);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                <i class="bx bxl-whatsapp text-white fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Fonnte WhatsApp API</h6>
                                <small class="text-muted">Notifikasi otomatis ke customer via WA</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Token API Fonnte</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bx bx-key"></i></span>
                                <input type="password" id="fonnteTokenInput" class="form-control font-monospace"
                                       name="fonnte_token"
                                       value="{{ $fonnteToken }}"
                                       placeholder="Masukkan token dari fonnte.com">
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleVisibility('fonnteTokenInput', this)">
                                    <i class="bx bx-hide"></i>
                                </button>
                            </div>
                            <div class="form-text">
                                <i class="bx bx-info-circle me-1"></i>
                                Dapatkan token dari <a href="https://fonnte.com" target="_blank" class="text-primary">fonnte.com</a>
                                → Dashboard → Device → Token
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <i class="bx bx-check-shield text-success"></i>
                            <small class="text-success">Token digunakan untuk kirim notifikasi order baru dan konfirmasi pembayaran</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== KOLOM KANAN ===== --}}
            <div class="col-lg-6">

                {{-- Email SMTP --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar" style="width:38px;height:38px;background:linear-gradient(135deg,#EA4335,#c62828);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                <i class="bx bx-envelope text-white fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Konfigurasi Email SMTP</h6>
                                <small class="text-muted">Untuk fitur lupa password & notifikasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-8">
                                <label class="form-label fw-semibold">SMTP Host</label>
                                <input type="text" class="form-control" name="mail_host"
                                       value="{{ $mailHost }}" placeholder="smtp.gmail.com">
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Port</label>
                                <input type="text" class="form-control" value="587" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Username (Email Pengirim)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bx bx-at"></i></span>
                                    <input type="email" class="form-control" name="mail_username"
                                           value="{{ $mailUser }}" placeholder="email@gmail.com">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">App Password Gmail</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bx bx-lock"></i></span>
                                    <input type="password" id="mailPassInput" class="form-control"
                                           name="mail_password" placeholder="Kosongkan jika tidak ingin mengubah">
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleVisibility('mailPassInput', this)">
                                        <i class="bx bx-hide"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    <i class="bx bx-info-circle me-1"></i>
                                    Gmail: aktifkan 2FA lalu buat
                                    <a href="https://myaccount.google.com/apppasswords" target="_blank">App Password</a>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <label class="form-label fw-semibold">Nama Pengirim</label>
                                <input type="text" class="form-control" name="mail_from_name"
                                       value="{{ $mailName }}" placeholder="HeartFit Nutrition">
                            </div>
                            <div class="col-sm-7">
                                <label class="form-label fw-semibold">Alamat Pengirim</label>
                                <input type="email" class="form-control" name="mail_from_address"
                                       value="{{ $mailFrom }}" placeholder="no-reply@heartfit.id">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== SAVE BUTTON ===== --}}
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bx bx-save me-2"></i>Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- ===== TEST EMAIL ===== --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar" style="width:38px;height:38px;background:linear-gradient(135deg,#4CAF50,#1B5E20);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bx bx-send text-white fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">Test Kirim Email</h6>
                    <small class="text-muted">Verifikasi konfigurasi SMTP sudah benar</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.test-email') }}" method="POST" class="d-flex gap-2 align-items-end">
                @csrf
                <div class="flex-grow-1" style="max-width:380px;">
                    <label class="form-label fw-semibold">Kirim email test ke:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bx bx-envelope"></i></span>
                        <input type="email" class="form-control" name="test_email"
                               placeholder="email@contoh.com" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bx bx-paper-plane me-1"></i>Kirim Test
                </button>
            </form>
        </div>
    </div>

    {{-- ===== TEST WA FONNTE ===== --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar" style="width:38px;height:38px;background:linear-gradient(135deg,#25D366,#128C7E);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bx bxl-whatsapp text-white fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">Test Kirim WhatsApp</h6>
                    <small class="text-muted">Verifikasi token Fonnte dan koneksi WA berjalan</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.test-wa') }}" method="POST" class="d-flex gap-2 align-items-end flex-wrap">
                @csrf
                <div class="flex-grow-1" style="max-width:380px;">
                    <label class="form-label fw-semibold">Kirim WA test ke nomor:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bxl-whatsapp bx" style="color:#25D366;"></i></span>
                        <input type="text" class="form-control" name="test_wa"
                               placeholder="628123456789 (tanpa + atau spasi)"
                               pattern="[0-9]{9,15}" required
                               title="Masukkan nomor tanpa tanda + atau spasi, contoh: 628123456789">
                    </div>
                    <div class="form-text">Format: 62xxxxxxxxxx (awali dengan 62, bukan 0)</div>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bx bxl-whatsapp me-1"></i>Kirim Test WA
                </button>
            </form>

            @if(empty($fonnteToken))
            <div class="alert alert-warning mt-3 py-2 mb-0 d-flex align-items-center gap-2">
                <i class="bx bx-error-circle"></i>
                <span>Token Fonnte belum dikonfigurasi. Isi token terlebih dahulu lalu simpan.</span>
            </div>
            @endif
        </div>
    </div>

</div>

@push('scripts')
<script>
function toggleVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bx-hide', 'bx-show');
    } else {
        input.type = 'password';
        icon.classList.replace('bx-show', 'bx-hide');
    }
}
</script>
@endpush
@endsection
