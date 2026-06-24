<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
    <title>Lupa Password — HeartFit Nutrition</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/heartfit_logo.png') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}"/>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--green:#28a745;--green-d:#1e7e34;--text:#1a202c;--muted:#718096;--border:#e2e8f0;--bg:#f0fdf4;--shadow:0 20px 60px rgba(40,167,69,.12)}
        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
        .bg-blob{position:fixed;border-radius:50%;filter:blur(80px);opacity:.3;pointer-events:none;z-index:0;animation:float 8s ease-in-out infinite}
        .blob-1{width:380px;height:380px;background:#28a745;top:-100px;right:-80px}
        .blob-2{width:280px;height:280px;background:#20c997;bottom:-60px;left:-60px;animation-delay:4s}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-25px)}}
        .auth-wrapper{position:relative;z-index:10;width:100%;max-width:420px;padding:20px}
        .auth-card{background:rgba(255,255,255,.92);backdrop-filter:blur(20px);border-radius:24px;box-shadow:var(--shadow);border:1px solid rgba(255,255,255,.8);animation:slideUp .5s cubic-bezier(.16,1,.3,1) both}
        @keyframes slideUp{from{opacity:0;transform:translateY(40px) scale(.96)}to{opacity:1;transform:none}}
        .auth-header{background:linear-gradient(135deg,#1a5c2a,#28a745 50%,#20c997);padding:30px 36px 24px;text-align:center;position:relative;overflow:hidden;border-radius:24px 24px 0 0}
        .auth-header::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E")}
        .auth-logo{max-width:120px;filter:drop-shadow(0 4px 12px rgba(0,0,0,.2));position:relative;z-index:1}
        .auth-header-title{color:#fff;font-size:20px;font-weight:800;margin-top:10px;position:relative;z-index:1}
        .auth-header-sub{color:rgba(255,255,255,.8);font-size:13px;margin-top:3px;position:relative;z-index:1}
        .auth-body{padding:28px 36px 32px}
        .form-label{font-size:13px;font-weight:600;color:var(--text);margin-bottom:6px;display:block}
        .input-group{display:flex;border:1.5px solid var(--border);border-radius:12px;overflow:hidden;transition:border-color .2s,box-shadow .2s;background:#fff}
        .input-group:focus-within{border-color:var(--green);box-shadow:0 0 0 3px rgba(40,167,69,.15)}
        .input-group-text{padding:0 14px;background:transparent;border:none;color:var(--muted);font-size:18px;display:flex;align-items:center}
        .form-control{flex:1;border:none;padding:12px 14px 12px 4px;font-size:14px;background:transparent;outline:none;color:var(--text);font-family:'Plus Jakarta Sans',sans-serif}
        .form-control::placeholder{color:#b0bec5}
        .alert{border-radius:12px;padding:12px 16px;font-size:13px;display:flex;align-items:flex-start;gap:10px;margin-bottom:18px;animation:fadeIn .3s ease both}
        @keyframes fadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1}}
        .alert-danger{background:#fff0f0;color:#c0392b;border-left:4px solid #e74c3c}
        .alert-success{background:#f0fff4;color:#155724;border-left:4px solid #28a745}
        .alert-icon{font-size:18px;flex-shrink:0}
        .btn-auth{width:100%;padding:14px;border:none;border-radius:12px;background:linear-gradient(135deg,var(--green),#20c997);color:#fff;font-size:15px;font-weight:700;font-family:'Plus Jakarta Sans',sans-serif;cursor:pointer;position:relative;overflow:hidden;transition:transform .2s,box-shadow .2s;box-shadow:0 4px 20px rgba(40,167,69,.35);display:flex;align-items:center;justify-content:center;gap:8px}
        .btn-auth:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(40,167,69,.45)}
        .btn-auth::after{content:'';position:absolute;inset:0;background:linear-gradient(rgba(255,255,255,.15),transparent)}
        .btn-auth i,.btn-auth span{position:relative;z-index:1}
        .btn-auth i{font-size:18px}
        .back-link{display:flex;align-items:center;justify-content:center;gap:5px;font-size:13px;color:var(--muted);text-decoration:none;margin-top:20px;font-weight:500;transition:color .2s}
        .back-link:hover{color:var(--green)}
        .mb-3{margin-bottom:18px}
        .mb-4{margin-bottom:24px}
    </style>
</head>
<body>
<div class="bg-blob blob-1"></div>
<div class="bg-blob blob-2"></div>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <img src="{{ asset('assets/img/favicon/heartfit_logo.png') }}" class="auth-logo" alt="HeartFit">
            <div class="auth-header-title">Lupa Password? 🔑</div>
            <div class="auth-header-sub">Kami kirimkan link reset ke email Anda</div>
        </div>
        <div class="auth-body">

            @if(session('status'))
            <div class="alert alert-success">
                <i class="bx bx-check-circle alert-icon"></i>
                <div>{{ session('status') }}</div>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger">
                <i class="bx bx-x-circle alert-icon"></i>
                <div>{{ session('error') }}</div>
            </div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger">
                <i class="bx bx-x-circle alert-icon"></i>
                <div>{{ $errors->first() }}</div>
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Alamat Email Terdaftar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                        <input type="email" class="form-control" name="email"
                               placeholder="email@contoh.com" value="{{ old('email') }}" autofocus required/>
                    </div>
                </div>
                <button type="submit" class="btn-auth">
                    <i class="bx bx-send"></i>
                    <span>Kirim Link Reset Password</span>
                </button>
            </form>

            <a href="{{ route('login') }}" class="back-link">
                <i class="bx bx-chevron-left"></i> Kembali ke halaman login
            </a>
        </div>
    </div>
</div>
</body>
</html>
