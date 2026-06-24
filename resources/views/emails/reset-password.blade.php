<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - HeartFit Nutrition</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f7fa; color: #333; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #28a745, #20c997); padding: 32px 40px; text-align: center; }
        .header h1 { color: #fff; font-size: 24px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); font-size: 13px; margin-top: 4px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; font-weight: 600; margin-bottom: 12px; }
        .text { font-size: 14px; color: #555; line-height: 1.6; margin-bottom: 20px; }
        .btn-wrap { text-align: center; margin: 28px 0; }
        .btn { display: inline-block; background: #28a745; color: #fff !important; text-decoration: none; padding: 14px 36px; border-radius: 8px; font-size: 15px; font-weight: 600; letter-spacing: 0.3px; }
        .btn:hover { background: #218838; }
        .note { background: #fff8e1; border-left: 4px solid #ffc107; padding: 12px 16px; border-radius: 6px; font-size: 13px; color: #7a6200; margin-bottom: 20px; }
        .link-fallback { font-size: 12px; color: #888; word-break: break-all; margin-top: 8px; }
        .footer { background: #f5f7fa; padding: 20px 40px; text-align: center; font-size: 12px; color: #aaa; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>HeartFit Nutrition 🌿</h1>
        <p>Makan Sehat, Hidup Sehat</p>
    </div>
    <div class="body">
        <p class="greeting">Halo, {{ $user->name ?? 'Pengguna' }}!</p>
        <p class="text">
            Kami menerima permintaan reset password untuk akun Anda yang terdaftar dengan email <strong>{{ $user->email }}</strong>.
        </p>
        <p class="text">
            Klik tombol di bawah untuk membuat password baru. Link ini hanya berlaku selama <strong>60 menit</strong>.
        </p>
        <div class="btn-wrap">
            <a href="{{ $resetUrl }}" class="btn">Reset Password Saya</a>
        </div>
        <div class="note">
            ⚠️ Jika Anda tidak meminta reset password, abaikan email ini. Password Anda tidak akan berubah.
        </div>
        <p class="text" style="font-size:12px;">
            Jika tombol di atas tidak berfungsi, salin dan tempel link berikut di browser Anda:
        </p>
        <p class="link-fallback">{{ $resetUrl }}</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} HeartFit Nutrition. Email ini dikirim secara otomatis, harap tidak membalas.
    </div>
</div>
</body>
</html>
