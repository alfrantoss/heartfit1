<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected string $apiToken;
    protected string $baseUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->apiToken = config('services.fonnte.token', '');
    }

    /**
     * Kirim pesan WhatsApp via Fonnte
     */
    public function send(string $phone, string $message): bool
    {
        if (empty($this->apiToken)) {
            Log::warning('[Fonnte] API token tidak dikonfigurasi.');
            return false;
        }

        // Normalisasi nomor: pastikan format 62xxxxxxx
        $phone = $this->normalizePhone($phone);

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
            ])->post($this->baseUrl, [
                'target'  => $phone,
                'message' => $message,
                'delay'   => '2',
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false)) {
                Log::info('[Fonnte] Pesan terkirim', ['phone' => $phone]);
                return true;
            }

            Log::warning('[Fonnte] Gagal kirim pesan', [
                'phone'    => $phone,
                'response' => $result,
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('[Fonnte] Exception saat kirim pesan', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Kirim notifikasi order baru (UNPAID)
     */
    public function sendOrderCreated(\App\Models\Order $order): bool
    {
        $phone = $order->whatsapp ?? $order->user?->detail?->hp;
        if (!$phone) return false;

        $nama   = $order->user?->name ?? 'Pelanggan';
        $nomor  = $order->order_number;
        $paket  = $order->package_label ?? '-';
        $total  = 'Rp ' . number_format($order->amount_total ?? $order->package_price, 0, ',', '.');
        $metode = strtoupper($order->payment_method ?? '-');
        $mulai  = $order->start_date ? $order->start_date->format('d/m/Y') : '-';
        $selesai = $order->end_date ? $order->end_date->format('d/m/Y') : '-';

        $message = "Halo *{$nama}*! 👋\n\n"
            . "✅ *Pesanan Berhasil Dibuat*\n\n"
            . "📋 *No. Order:* {$nomor}\n"
            . "🥗 *Paket:* {$paket}\n"
            . "📅 *Periode:* {$mulai} s/d {$selesai}\n"
            . "💰 *Total:* {$total}\n"
            . "💳 *Metode:* {$metode}\n\n"
            . ($order->payment_method === 'transfer'
                ? "⏳ Silakan segera lakukan pembayaran untuk mengkonfirmasi pesanan Anda.\n\n"
                : "📦 Pesanan Anda akan segera diproses.\n\n")
            . "_HeartFit Nutrition — Makan Sehat, Hidup Sehat_ 🌿";

        return $this->send($phone, $message);
    }

    /**
     * Kirim notifikasi pembayaran berhasil
     */
    public function sendPaymentSuccess(\App\Models\Order $order): bool
    {
        $phone = $order->whatsapp ?? $order->user?->detail?->hp;
        if (!$phone) return false;

        $nama   = $order->user?->name ?? 'Pelanggan';
        $nomor  = $order->order_number;
        $paket  = $order->package_label ?? '-';
        $total  = 'Rp ' . number_format($order->amount_total ?? $order->package_price, 0, ',', '.');
        $mulai  = $order->start_date ? $order->start_date->format('d/m/Y') : '-';
        $selesai = $order->end_date ? $order->end_date->format('d/m/Y') : '-';
        $paidAt = $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i');

        $message = "Halo *{$nama}*! 🎉\n\n"
            . "✅ *Pembayaran Berhasil Dikonfirmasi!*\n\n"
            . "📋 *No. Order:* {$nomor}\n"
            . "🥗 *Paket:* {$paket}\n"
            . "📅 *Periode:* {$mulai} s/d {$selesai}\n"
            . "💰 *Total Dibayar:* {$total}\n"
            . "🕐 *Waktu Bayar:* {$paidAt}\n\n"
            . "Pesanan Anda sedang dipersiapkan. Kami akan menginformasikan status pengambilan setiap harinya.\n\n"
            . "_HeartFit Nutrition — Makan Sehat, Hidup Sehat_ 🌿";

        return $this->send($phone, $message);
    }

    /**
     * Kirim notifikasi perubahan status pickup (pengambilan di tempat).
     *
     * Karena ODS tidak per-order melainkan per-menu-per-tanggal,
     * notif dikirim ke SEMUA order PAID yang aktif pada delivery_date tersebut.
     */
    public function sendPickupStatusUpdate(\App\Models\OrderDeliveryStatus $delivery, string $field, string $newStatus): bool
    {
        // Ambil semua order aktif pada tanggal delivery (tanpa filter package_key)
        $deliveryDate = \Carbon\Carbon::parse($delivery->delivery_date)->toDateString();

        $orders = \App\Models\Order::with(['user', 'user.detail'])
            ->whereIn('status', ['PAID', 'SETTLEMENT'])
            ->whereDate('start_date', '<=', $deliveryDate)
            ->whereDate('end_date', '>=', $deliveryDate)
            ->get();

        if ($orders->isEmpty()) {
            \Illuminate\Support\Facades\Log::info('[Fonnte] Tidak ada order aktif pada tanggal '.$deliveryDate);
            return false;
        }

        $menuNama  = $delivery->menuMakanan->nama_menu ?? 'Menu';
        $paketNama = $delivery->mealPackage->nama_meal_package ?? 'Paket';
        $tanggal   = \Carbon\Carbon::parse($deliveryDate)->locale('id')->isoFormat('D MMMM Y');
        $sesi      = $field === 'status_siang' ? 'Siang' : 'Malam';

        [$icon, $judul, $keterangan] = match($newStatus) {
            'diterima' => [
                '📥',
                'Pesanan Diterima HeartFit',
                "Pesanan Anda telah diterima oleh tim HeartFit dan sedang dalam antrian persiapan.",
            ],
            'diproses' => [
                '👨‍🍳',
                'Pesanan Sedang Diproses',
                "Tim dapur HeartFit sedang mempersiapkan pesanan Anda. Harap tunggu informasi selanjutnya.",
            ],
            'siap' => [
                '🔔',
                'Pesanan Siap Diambil!',
                "Pesanan Anda *sudah siap* dan menunggu untuk diambil. Segera datang ke lokasi HeartFit sebelum waktu pengambilan habis.",
            ],
            'diambil' => [
                '✅',
                'Pesanan Telah Diambil',
                "Pesanan Anda telah berhasil diambil. Selamat menikmati! Semoga membantu perjalanan sehat Anda 💪",
            ],
            default => ['🔔', 'Status Pesanan Diperbarui', "Status pesanan Anda telah diperbarui."],
        };

        $success = false;
        foreach ($orders as $order) {
            $phone = $order->whatsapp ?? $order->user?->detail?->hp;
            if (!$phone) continue;

            $nama    = $order->user?->name ?? 'Pelanggan';
            $nomor   = $order->order_number;
            $mulai   = $order->start_date ? $order->start_date->format('d/m/Y') : '-';
            $selesai = $order->end_date   ? $order->end_date->format('d/m/Y')   : '-';
            $total   = 'Rp ' . number_format($order->amount_total ?? $order->package_price, 0, ',', '.');

            $message = "Halo *{$nama}*! 👋\n\n"
                . "{$icon} *{$judul}*\n\n"
                . "📋 *No. Order:* {$nomor}\n"
                . "🥗 *Paket:* {$order->package_label}\n"
                . "📅 *Periode:* {$mulai} s/d {$selesai}\n"
                . "💰 *Total:* {$total}\n"
                . "🍱 *Menu ({$sesi}):* {$menuNama}\n"
                . "📆 *Tanggal:* {$tanggal}\n\n"
                . "{$keterangan}\n\n"
                . "_HeartFit Nutrition — Makan Sehat, Hidup Sehat_ 🌿";

            if ($this->send($phone, $message)) {
                $success = true;
            }
        }

        return $success;
    }

    /**
     * Test koneksi Fonnte dengan kirim pesan ke nomor tertentu
     */
    public function testSend(string $phone, string $message = null): array
    {
        if (empty($this->apiToken)) {
            return ['success' => false, 'message' => 'Token Fonnte belum dikonfigurasi.'];
        }

        $phone   = $this->normalizePhone($phone);
        $message = $message ?? "✅ *Test Notifikasi HeartFit*\n\nKoneksi WhatsApp via Fonnte berhasil!\n\n_Dikirim pada: " . now()->format('d/m/Y H:i') . "_";

        try {
            $response = Http::timeout(15)->withHeaders([
                'Authorization' => $this->apiToken,
            ])->post($this->baseUrl, [
                'target'  => $phone,
                'message' => $message,
                'delay'   => '1',
            ]);

            $result = $response->json();
            $status = $result['status'] ?? false;

            Log::info('[Fonnte] Test send', ['phone' => $phone, 'status' => $status, 'response' => $result]);

            return [
                'success'  => $response->successful() && $status,
                'message'  => $status ? 'Pesan test berhasil dikirim ke ' . $phone : ('Gagal: ' . ($result['reason'] ?? $result['message'] ?? 'Respon tidak dikenal')),
                'response' => $result,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Normalisasi nomor HP ke format 62xxxxxxx
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
