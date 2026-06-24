<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan (token Fonnte, dll)
     */
    public function index()
    {
        $fonnteToken = env('FONNTE_TOKEN', '');
        $mailHost    = env('MAIL_HOST', '');
        $mailUser    = env('MAIL_USERNAME', '');
        $mailFrom    = env('MAIL_FROM_ADDRESS', '');
        $mailName    = env('MAIL_FROM_NAME', '');

        return view('admin.settings.index', compact(
            'fonnteToken', 'mailHost', 'mailUser', 'mailFrom', 'mailName'
        ));
    }

    /**
     * Simpan perubahan setting ke .env
     */
    public function update(Request $request)
    {
        $request->validate([
            'fonnte_token'      => ['nullable', 'string', 'max:255'],
            'mail_host'         => ['nullable', 'string', 'max:255'],
            'mail_username'     => ['nullable', 'email', 'max:255'],
            'mail_password'     => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name'    => ['nullable', 'string', 'max:100'],
        ]);

        $updates = [
            'FONNTE_TOKEN'      => $request->input('fonnte_token', ''),
            'MAIL_HOST'         => $request->input('mail_host', 'smtp.gmail.com'),
            'MAIL_USERNAME'     => $request->input('mail_username', ''),
            'MAIL_FROM_ADDRESS' => $request->input('mail_from_address', ''),
            'MAIL_FROM_NAME'    => '"' . $request->input('mail_from_name', 'HeartFit Nutrition') . '"',
        ];

        // Hanya update password jika diisi (hindari overwrite dengan kosong)
        if ($request->filled('mail_password')) {
            $updates['MAIL_PASSWORD'] = $request->input('mail_password');
        }

        $this->updateEnvFile($updates);

        Artisan::call('config:clear');
        Artisan::call('config:cache');

        return back()->with('success', 'Pengaturan berhasil disimpan dan config cache diperbarui.');
    }

    /**
     * Test kirim WA Fonnte
     */
    public function testWa(Request $request)
    {
        $request->validate([
            'test_wa' => ['required', 'string', 'regex:/^[0-9]{9,15}$/'],
        ], [
            'test_wa.required' => 'Nomor WhatsApp wajib diisi.',
            'test_wa.regex'    => 'Format nomor tidak valid. Gunakan angka saja (contoh: 628123456789)',
        ]);

        $fonnte = app(\App\Services\FonnteService::class);
        $result = $fonnte->testSend($request->input('test_wa'));

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        return back()->with('error', $result['message']);
    }

    /**
     * Test kirim email
     */
    public function testEmail(Request $request)
    {
        $request->validate(['test_email' => ['required', 'email']]);

        try {
            \Illuminate\Support\Facades\Mail::send(
                'emails.reset-password',
                [
                    'resetUrl' => url('/'),
                    'user'     => (object) ['name' => 'Test User', 'email' => $request->test_email],
                ],
                function ($m) use ($request) {
                    $m->to($request->test_email)->subject('Test Email — HeartFit Nutrition');
                }
            );
            return back()->with('success', 'Email test berhasil dikirim ke ' . $request->test_email);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal kirim email: ' . $e->getMessage());
        }
    }

    /**
     * Helper: update baris di file .env
     */
    private function updateEnvFile(array $data): void
    {
        $envPath    = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            // Jika value mengandung spasi dan belum di-quote, wrap dengan ""
            $safeValue = (str_contains((string) $value, ' ') && !str_starts_with((string) $value, '"'))
                ? '"' . $value . '"'
                : $value;

            $pattern     = '/^' . preg_quote($key, '/') . '=.*/m';
            $replacement = $key . '=' . $safeValue;

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
