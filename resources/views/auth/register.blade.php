<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
      data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <title>Daftar Akun — HeartFit Nutrition</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/heartfit_logo.png') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css"/>
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}"/>
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <style>
        .authentication-wrapper { min-height: 100vh; }
        .register-split {
            display: flex; min-height: 100vh;
        }
        /* Left panel — branding */
        .register-left {
            width: 38%; min-height: 100vh;
            background: linear-gradient(145deg, #4a4de6 0%, #696cff 50%, #7b6ff0 100%);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 48px 40px; position: relative; overflow: hidden;
        }
        .register-left::before {
            content: ''; position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3Ccircle cx='30' cy='30' r='10'/%3E%3C/g%3E%3C/svg%3E");
        }
        .register-left .blob {
            position: absolute; border-radius: 50%; filter: blur(60px); opacity: .2;
        }
        .register-left .blob-1 { width:300px;height:300px;background:#fff;top:-80px;right:-60px; }
        .register-left .blob-2 { width:200px;height:200px;background:#a78bfa;bottom:-40px;left:-40px; }
        .register-left-content { position: relative; z-index: 1; text-align: center; color: #fff; }
        .register-left-content img { max-width: 160px; margin-bottom: 24px; filter:drop-shadow(0 6px 20px rgba(0,0,0,.2)); animation: float 4s ease-in-out infinite; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .register-left-content h2 { font-size: 26px; font-weight: 800; margin-bottom: 12px; }
        .register-left-content p { font-size: 14px; opacity: .85; line-height: 1.7; margin-bottom: 0; }
        .feature-item { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
        .feature-item i { font-size:20px; flex-shrink:0; background:rgba(255,255,255,.2); border-radius:8px; padding:6px; }

        /* Right panel — form */
        .register-right {
            flex: 1; display: flex; align-items: flex-start; justify-content: center;
            padding: 40px 24px; overflow-y: auto;
            background: #f5f5f9;
        }
        .register-form-wrap { width: 100%; max-width: 560px; }
        .register-form-wrap .card { border: none; border-radius: 12px; box-shadow: 0 4px 18px rgba(105,108,255,.1); }
        .section-label {
            font-size: 11px; font-weight: 700; letter-spacing: 1px;
            color: #696cff; text-transform: uppercase;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 8px; margin-bottom: 16px; margin-top: 4px;
        }
        .step-badge {
            width: 24px; height: 24px; border-radius: 50%; background: #28a745;
            color: #fff; font-size: 12px; font-weight: 700;
            display: inline-flex; align-items: center; justify-content: center;
            margin-right: 8px; flex-shrink: 0;
        }
        .strength-bar { height: 4px; border-radius: 2px; background: #e9ecef; margin-top: 6px; }
        .strength-fill { height: 100%; border-radius: 2px; transition: width .3s, background .3s; width: 0; }
        .strength-text { font-size: 11px; margin-top: 4px; font-weight: 600; }

        @media (max-width: 768px) {
            .register-left { display: none; }
            .register-right { padding: 24px 16px; }
        }
    </style>
</head>
<body>
<div class="register-split">

    {{-- ── LEFT: Branding ── --}}
    <div class="register-left">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="register-left-content">
            <img src="{{ asset('assets/img/favicon/heartfit_logo.png') }}" alt="HeartFit">
            <h2>HeartFit Nutrition</h2>
            <p class="mb-4">Makan sehat, hidup lebih baik.<br>Bergabung bersama ribuan pelanggan sehat kami.</p>
            <div class="text-start" style="max-width:280px;">
                <div class="feature-item"><i class="bx bx-check-circle"></i><span>Paket makan sehat harian</span></div>
                <div class="feature-item"><i class="bx bx-bowl-hot"></i><span>Menu bergizi & lezat</span></div>
                <div class="feature-item"><i class="bx bx-truck"></i><span>Diantar langsung ke pintu</span></div>
                <div class="feature-item"><i class="bx bx-user-voice"></i><span>Konsultasi ahli gizi</span></div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: Form ── --}}
    <div class="register-right">
        <div class="register-form-wrap">
            <div class="text-center mb-4">
                <h4 class="fw-bold mb-1">Buat Akun Baru 🌱</h4>
                <p class="text-muted small">Isi data di bawah untuk mendaftar</p>
            </div>

            @if($errors->any())
            <div class="alert alert-danger d-flex align-items-start gap-2 mb-3" role="alert">
                <i class="bx bx-x-circle fs-5 flex-shrink-0"></i>
                <div>
                    <strong>Periksa kembali data Anda:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $err)
                        <li style="font-size:13px;">{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-body p-4">

                    <form method="POST" action="{{ route('registrasi.post') }}" id="registerForm">
                        @csrf

                        {{-- ── STEP 1: Akun ── --}}
                        <div class="section-label d-flex align-items-center">
                            <span class="step-badge">1</span> Informasi Akun
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-user"></i></span>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                           name="username" placeholder="Nama pengguna"
                                           value="{{ old('username') }}" required/>
                                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           name="email" placeholder="email@contoh.com"
                                           value="{{ old('email') }}" required/>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-lock"></i></span>
                                    <input type="password" id="pwInput"
                                           class="form-control @error('password') is-invalid @enderror"
                                           name="password" placeholder="Min. 6 karakter"
                                           required oninput="checkStrength(this.value)"/>
                                    <span class="input-group-text cursor-pointer" onclick="togglePw('pwInput',this)"><i class="bx bx-hide"></i></span>
                                </div>
                                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                                <div class="strength-text" id="strengthText"></div>
                                @error('password')<div class="text-danger" style="font-size:12px;">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-lock-open"></i></span>
                                    <input type="password" id="pwConf" class="form-control"
                                           name="password_confirmation" placeholder="Ulangi password" required/>
                                    <span class="input-group-text cursor-pointer" onclick="togglePw('pwConf',this)"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                        </div>

                        {{-- ── STEP 2: Kontak ── --}}
                        <div class="section-label d-flex align-items-center mt-2">
                            <span class="step-badge">2</span> Kontak & Alamat
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">
                                    No. WhatsApp <span class="text-danger">*</span>
                                    <i class="bxl-whatsapp bx ms-1" style="color:#25D366;"></i>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-success text-white" style="font-size:12px;font-weight:600;">+62</span>
                                    <input type="text" class="form-control @error('hp') is-invalid @enderror"
                                           name="hp" placeholder="8123456789"
                                           value="{{ ltrim(old('hp',''), '620') }}"
                                           required maxlength="15"
                                           oninput="this.value=this.value.replace(/[^0-9]/g,'')"/>
                                    @error('hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-text">Untuk notifikasi order & pengantaran via WA</div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-male-female"></i></span>
                                    <select class="form-select" name="jenis_kelamin">
                                        <option value="">-- Pilih --</option>
                                        <option value="L" {{ old('jenis_kelamin')==='L'?'selected':'' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin')==='P'?'selected':'' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Pengiriman</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-home"></i></span>
                                    <textarea class="form-control" name="alamat" rows="2"
                                              placeholder="Alamat lengkap untuk pengiriman...">{{ old('alamat') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ── STEP 3: Data Diri (opsional) ── --}}
                        <div class="section-label d-flex align-items-center mt-2">
                            <span class="step-badge" style="background:#6c757d;">3</span>
                            Data Diri <small class="text-muted fw-normal ms-2">(opsional, bisa diisi nanti)</small>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Tempat Lahir</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-map-pin"></i></span>
                                    <input type="text" class="form-control" name="tempat_lahir"
                                           placeholder="Kota kelahiran" value="{{ old('tempat_lahir') }}"/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold">Tanggal Lahir</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                    <input type="date" class="form-control" name="tanggal_lahir"
                                           value="{{ old('tanggal_lahir') }}"/>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Berat Badan (kg)</label>
                                <input type="number" class="form-control" name="berat_badan"
                                       placeholder="60" min="1" max="300" value="{{ old('berat_badan') }}"/>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Tinggi Badan (cm)</label>
                                <input type="number" class="form-control" name="tinggi_badan"
                                       placeholder="165" min="1" max="250" value="{{ old('tinggi_badan') }}"/>
                            </div>
                            <div class="col-sm-4">
                                <label class="form-label fw-semibold">Usia (tahun)</label>
                                <input type="number" class="form-control" name="usia"
                                       placeholder="25" min="1" max="120" value="{{ old('usia') }}"/>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary d-grid w-100 py-2">
                            <span class="d-flex align-items-center justify-content-center gap-2">
                                <i class="bx bx-user-plus fs-5"></i>
                                <span class="fw-bold">Daftar Sekarang</span>
                            </span>
                        </button>
                    </form>

                    <p class="text-center mt-3 mb-0" style="font-size:13px;">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-primary fw-semibold">Masuk di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<script>
function togglePw(id, el) {
    const input = document.getElementById(id);
    const icon  = el.querySelector ? el.querySelector('i') : el;
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'text' ? 'bx bx-show' : 'bx bx-hide';
}

function checkStrength(pw) {
    const fill = document.getElementById('strengthFill');
    const txt  = document.getElementById('strengthText');
    let s = 0;
    if (pw.length >= 6)  s++;
    if (pw.length >= 10) s++;
    if (/[A-Z]/.test(pw)) s++;
    if (/[0-9]/.test(pw)) s++;
    if (/[^A-Za-z0-9]/.test(pw)) s++;
    const L = [
        {w:'0%',bg:'#e9ecef',l:'',c:''},
        {w:'20%',bg:'#dc3545',l:'Sangat lemah',c:'#dc3545'},
        {w:'45%',bg:'#fd7e14',l:'Lemah',c:'#fd7e14'},
        {w:'65%',bg:'#ffc107',l:'Cukup',c:'#856404'},
        {w:'85%',bg:'#28a745',l:'Kuat',c:'#28a745'},
        {w:'100%',bg:'#1e7e34',l:'Sangat kuat',c:'#1e7e34'},
    ];
    const lv = L[s]||L[0];
    fill.style.width=lv.w; fill.style.background=lv.bg;
    txt.textContent=lv.l; txt.style.color=lv.c;
}
</script>
</body>
</html>
