<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use App\Services\FonnteService;
use App\Services\MRGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'hp' => ['required', 'string', 'max:20', 'regex:/^[0-9]{9,15}$/'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'tanggal_lahir' => ['nullable', 'date'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
        ], [
            'hp.required' => 'Nomor WhatsApp wajib diisi.',
            'hp.regex' => 'Format nomor tidak valid. Gunakan angka saja (9–15 digit).',
            'email.unique' => 'Email sudah terdaftar. Gunakan email lain atau login.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $generatedMr = null;
        $user = null;

        DB::transaction(function () use ($request, &$generatedMr, &$user) {
            $user = User::create([
                'name' => $request->username,
                'email' => $request->email,
                'role' => 'customer',
                'password' => Hash::make($request->password),
                'created_by' => null,
            ]);

            $generatedMr = MRGeneratorService::generateUnique();

            // Proses bb_tb jika ada
            $bbTb = null;
            if ($request->filled('berat_badan') || $request->filled('tinggi_badan')) {
                $bb = trim($request->input('berat_badan', ''));
                $tb = trim($request->input('tinggi_badan', ''));
                $bbTb = ($bb && $tb) ? "{$bb}/{$tb}" : ($bb ?: $tb);
            }

            // Normalisasi HP
            $hp = preg_replace('/[^0-9]/', '', $request->hp);
            if (str_starts_with($hp, '0')) {
                $hp = '62'.substr($hp, 1);
            } elseif (! str_starts_with($hp, '62')) {
                $hp = '62'.$hp;
            }

            UserDetail::create([
                'user_id' => $user->id,
                'mr' => $generatedMr,
                'nik' => null,
                'alamat' => $request->input('alamat', null),
                'jenis_kelamin' => $request->input('jenis_kelamin', null),
                'tempat_lahir' => $request->input('tempat_lahir', null),
                'tanggal_lahir' => $request->input('tanggal_lahir', null),
                'bb_tb' => $bbTb,
                'hp' => $hp,
                'usia' => $request->input('usia', null),
                'created_by' => null,
            ]);
        });

        // Auto login
        Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Kirim notifikasi WA selamat datang
        try {
            $hp = $request->hp;
            $msg = "Halo *{$request->username}*! 👋\n\n"
                 ."✅ *Registrasi HeartFit Berhasil!*\n\n"
                 ."📋 *Nomor MR Anda:* {$generatedMr}\n"
                 ."📧 *Email:* {$request->email}\n\n"
                 ."Selamat datang di HeartFit Nutrition! Mulai perjalanan hidup sehat Anda dengan memesan paket makan sehat kami.\n\n"
                 .'_HeartFit Nutrition — Makan Sehat, Hidup Sehat_ 🌿';

            app(FonnteService::class)->send($hp, $msg);
        } catch (\Exception $e) {
            \Log::warning('[Register] Gagal kirim WA sambutan', ['error' => $e->getMessage()]);
        }

        return redirect()
            ->route('dashboard.customer')
            ->with('status', "Selamat datang, {$request->username}! Nomor MR Anda: {$generatedMr}. Silakan lengkapi data profil Anda.");
    }
}
