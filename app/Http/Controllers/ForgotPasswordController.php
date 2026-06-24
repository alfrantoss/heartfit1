<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * Tampilkan form lupa password
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses kirim link reset password ke email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Selalu tampilkan pesan sukses untuk keamanan (mencegah email enumeration)
        if (!$user) {
            return back()->with('status', 'Jika email terdaftar, link reset password telah dikirim. Cek inbox atau spam Anda.');
        }

        // Hapus token lama dan buat yang baru
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

        try {
            Mail::send('emails.reset-password', [
                'resetUrl' => $resetUrl,
                'user'     => $user,
            ], function ($m) use ($user) {
                $m->to($user->email, $user->name)
                  ->subject('Reset Password — HeartFit Nutrition');
            });
        } catch (\Exception $e) {
            \Log::error('[ForgotPassword] Gagal kirim email', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
        }

        return back()->with('status', 'Link reset password telah dikirim ke email Anda. Cek inbox atau folder spam.');
    }

    /**
     * Tampilkan form reset password
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Proses reset password baru
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 6 karakter.',
        ]);

        // Cari token reset
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Token tidak ditemukan atau sudah digunakan.']);
        }

        // Verifikasi token
        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid.']);
        }

        // Cek expired (1 jam)
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->diffInMinutes(now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Token sudah kedaluwarsa. Silakan minta link baru.']);
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User tidak ditemukan.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Hapus token setelah digunakan
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('status', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }
}
