<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
      data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <title>Masuk — HeartFit</title>
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
        /* ── Split layout ── */
        html, body { height: 100%; margin: 0; }
        .login-split { display: flex; min-height: 100vh; }

        /* Left panel — pakai warna Sneat biru */
        .login-left {
            width: 40%;
            background: linear-gradient(145deg, #4a4de6 0%, #696cff 50%, #7b6ff0 100%);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 48px 40px;
            position: relative; overflow: hidden;
        }
        /* Pattern subtle */
        .login-left::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3Ccircle cx='30' cy='30' r='10'/%3E%3C/g%3E%3C/svg%3E");
        }
        /* Animated blobs */
        .lb { position:absolute; border-radius:50%; filter:blur(70px); opacity:.2;
              animation: lbFloat 7s ease-in-out infinite; }
        .lb-1 { width:280px;height:280px;background:#fff;top:-80px;right:-60px; }
        .lb-2 { width:220px;height:220px;background:#a78bfa;bottom:-50px;left:-50px;animation-delay:3s; }
        .lb-3 { width:150px;height:150px;background:#c4b5fd;top:55%;left:15%;animation-delay:5s; }
        @keyframes lbFloat { 0%,100%{transform:translateY(0) scale(1);} 50%{transform:translateY(-18px) scale(1.05);} }

        /* Floating dots */
        .ldot { position:absolute; border-radius:50%; background:rgba(255,255,255,.25);
                animation:ldotRise linear infinite; }
        @keyframes ldotRise {
            0%   { transform:translateY(105%) scale(0); opacity:0; }
            15%  { opacity:.6; }
            85%  { opacity:.6; }
            100% { transform:translateY(-80px) scale(1.2); opacity:0; }
        }

        .login-left-content { position:relative; z-index:1; text-align:center; color:#fff; }
        .login-logo { max-width:150px; margin-bottom:20px;
                      filter:drop-shadow(0 6px 16px rgba(0,0,0,.25));
                      animation:logoFloat 4s ease-in-out infinite; }
        @keyframes logoFloat { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-8px);} }
        .login-left-content h2 { font-size:26px; font-weight:800; margin-bottom:10px; }
        .login-left-content p  { font-size:14px; opacity:.85; line-height:1.7; max-width:270px; margin:0 auto; }

        .chip-row { display:flex; flex-wrap:wrap; gap:8px; justify-content:center; margin-top:20px; }
        .chip {
            background:rgba(255,255,255,.18); backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,.3); border-radius:20px;
            padding:6px 14px; font-size:12px; font-weight:600; color:#fff;
            display:flex; align-items:center; gap:5px;
        }

        /* Right panel */
        .login-right {
            flex:1; display:flex; align-items:center; justify-content:center;
            padding:40px 24px; background:#f5f5f9;
        }
        .login-form-wrap { width:100%; max-width:400px; }
        .login-card {
            border:none; border-radius:12px;
            box-shadow:0 4px 18px rgba(105,108,255,.12);
            animation: cardIn .45s cubic-bezier(.16,1,.3,1) both;
        }
        @keyframes cardIn { from{opacity:0;transform:translateY(18px);} to{opacity:1;transform:none;} }

        @media (max-width:768px) {
            .login-left { display:none; }
            .login-right { background:#fff; padding:24px 16px; }
        }
    </style>
</head>
<body>
<div class="login-split">

    {{-- ── Left: Visual ── --}}
    <div class="login-left">
        <div class="lb lb-1"></div>
        <div class="lb lb-2"></div>
        <div class="lb lb-3"></div>
        <div id="ldots"></div>
        <div class="login-left-content">
            <img src="{{ asset('assets/img/favicon/heartfit_logo.png') }}" class="login-logo" alt="HeartFit">
            <h2>HeartFit Nutrition</h2>
            <p>Makan sehat setiap hari dengan paket catering bergizi yang diantarkan langsung ke pintu Anda.</p>
            <div class="chip-row">
                <div class="chip"><i class="bx bx-bowl-hot"></i> Menu Bergizi</div>
                {{-- <div class="chip"><i class="bx bx-truck"></i> Antar Rumah</div> --}}
                <div class="chip"><i class="bx bx-user-voice"></i> Ahli Gizi</div>
                <div class="chip"><i class="bx bx-heart"></i> Hidup Sehat</div>
            </div>
        </div>
    </div>

    {{-- ── Right: Form ── --}}
    <div class="login-right">
        <div class="login-form-wrap">
            <div class="text-center mb-4">
                <h4 class="fw-bold mb-1">Selamat Datang Kembali! 👋</h4>
                <p class="text-muted small">Masuk untuk melanjutkan perjalanan sehat Anda</p>
            </div>

            @if($errors->any())
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                <i class="bx bx-error-circle fs-5 flex-shrink-0"></i>
                <div><strong>Login gagal!</strong> {{ $errors->first() }}</div>
            </div>
            @endif

            @if(session('status'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
                <i class="bx bx-check-circle fs-5 flex-shrink-0"></i>
                <div>{{ session('status') }}</div>
            </div>
            @endif

            <div class="card login-card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email / Username</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                <input type="text" class="form-control" name="email"
                                       placeholder="Email atau username" value="{{ old('email') }}" autofocus required/>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold mb-0">Password</label>
                                <a href="{{ route('password.request') }}" class="small">Lupa password?</a>
                            </div>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-lock"></i></span>
                                <input type="password" id="pwLogin" class="form-control"
                                       name="password" placeholder="Masukkan password" required/>
                                <span class="input-group-text cursor-pointer" id="pwToggle">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary d-grid w-100" id="loginBtn">
                            Masuk Sekarang
                        </button>
                    </form>

                    <div class="text-center mt-3 pt-2 border-top" style="font-size:13px;">
                        Belum punya akun?
                        <a href="{{ url('registrasi') }}" class="fw-semibold">Daftar gratis</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('toast_success'))
<div class="bs-toast toast toast-placement-ex m-3 bg-success top-0 end-0 show position-fixed"
     role="alert" style="z-index:9999;" data-bs-delay="3500" id="toastLogout">
    <div class="toast-header bg-success text-white">
        <i class="bx bx-check-circle me-2"></i>
        <div class="me-auto fw-semibold">Berhasil</div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body">{{ session('toast_success') }}</div>
</div>
@endif

<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<script>
// Toggle password
document.getElementById('pwToggle').addEventListener('click', function() {
    const pw = document.getElementById('pwLogin');
    const ic = this.querySelector('i');
    pw.type = pw.type === 'password' ? 'text' : 'password';
    ic.className = pw.type === 'text' ? 'bx bx-show' : 'bx bx-hide';
});

// Submit loading state
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
});

// Floating dots
(function() {
    const c = document.getElementById('ldots');
    for (let i = 0; i < 18; i++) {
        const d = document.createElement('div');
        const s = 3 + Math.random() * 6;
        d.className = 'ldot';
        d.style.cssText = `width:${s}px;height:${s}px;left:${Math.random()*100}%;`
            + `animation-duration:${7+Math.random()*9}s;animation-delay:${Math.random()*7}s;`;
        c.appendChild(d);
    }
})();

// Toast
@if(session('toast_success'))
(function() {
    const el = document.getElementById('toastLogout');
    if (el && bootstrap) {
        new bootstrap.Toast(el).show();
    }
})();
@endif
</script>
</body>
</html>
