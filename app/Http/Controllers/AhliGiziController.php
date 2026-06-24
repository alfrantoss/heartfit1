<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\MenuMakanan;
use App\Models\PackageType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AhliGiziController extends Controller
{
    /**
     * Dashboard Ahli Gizi - Tampilkan order customer yang tipe paketnya is_personal = true
     */
    public function index(Request $request)
    {
        $q       = $request->input('q');
        $perPage = (int) $request->input('per_page', 10);

        // Ambil package_key (id meal_packages) yang tipe paketnya is_personal = true
        $personalPackageKeys = \App\Models\MealPackages::whereHas('packageType', function ($q) {
            $q->where('is_personal', true);
        })->pluck('id');

        $orders = Order::query()
            ->whereHas('user', function ($query) {
                $query->where('role', 'customer');
            })
            ->whereIn('package_key', $personalPackageKeys)
            ->with([
                'user:id,name,email',
                'user.detail:user_id,mr,nik,hp'
            ])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('order_number', 'like', "%{$q}%")
                        ->orWhere('package_label', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($userQ) use ($q) {
                            $userQ->where('name', 'like', "%{$q}%")
                                  ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $today = Carbon::today();

        $summary = [
            'total_customers'   => User::where('role', 'customer')->count(),
            'total_orders'      => Order::whereIn('package_key', $personalPackageKeys)->count(),
            'active_today'      => Order::where('status', 'PAID')
                ->whereIn('package_key', $personalPackageKeys)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count(),
            'orders_this_month' => Order::whereIn('package_key', $personalPackageKeys)
                ->whereYear('created_at', $today->year)
                ->whereMonth('created_at', $today->month)
                ->count(),
        ];

        return view('ahli_gizi.orders.index', compact('orders', 'perPage', 'summary'));
    }

    /**
     * Detail order — halaman konsultasi gizi
     */
    public function show(Order $order)
    {
        // Pastikan tipe paket order ini is_personal = true
        $isPersonal = \App\Models\MealPackages::where('id', $order->package_key)
            ->whereHas('packageType', fn($q) => $q->where('is_personal', true))
            ->exists();

        if (!$isPersonal) {
            return redirect()->route('ahli_gizi.orders')
                ->with('error', 'Halaman ini hanya untuk order paket yang perlu konsultasi ahli gizi.');
        }

        $order->load(['user', 'user.detail']);

        $availableMenus = MenuMakanan::orderBy('nama_menu')->get();

        $sessionKey   = 'konsul_menus_' . $order->id;
        $sessionMenus = session($sessionKey, []);

        return view('ahli_gizi.orders.show', compact('order', 'availableMenus', 'sessionMenus'));
    }

    /**
     * Simpan pilihan menu ke session konsultasi
     */
    public function sessionAdd(Request $request, Order $order)
    {
        $sessionKey = 'konsul_menus_' . $order->id;
        $noteKey    = 'konsul_note_' . $order->id;

        $current = session($sessionKey, []);

        $fromList = $request->input('menu_from_list', []);
        if (is_array($fromList)) {
            foreach ($fromList as $menu) {
                $menu = trim($menu);
                if ($menu !== '' && !in_array($menu, $current)) {
                    $current[] = $menu;
                }
            }
        }

        $manual = trim($request->input('menu_manual', ''));
        if ($manual !== '' && !in_array($manual, $current)) {
            $current[] = $manual;
        }

        session([$sessionKey => array_values($current)]);

        $note = trim($request->input('konsul_note', ''));
        if ($note !== '') {
            session([$noteKey => $note]);
        }

        return redirect()->route('ahli_gizi.orders.show', $order->id)
            ->with('konsul_saved', true);
    }

    /**
     * Hapus satu menu dari session konsultasi
     */
    public function sessionRemove(Request $request, Order $order)
    {
        $sessionKey = 'konsul_menus_' . $order->id;
        $current    = session($sessionKey, []);
        $idx        = (int) $request->input('menu_idx', -1);

        if (isset($current[$idx])) {
            array_splice($current, $idx, 1);
            session([$sessionKey => array_values($current)]);
        }

        return redirect()->route('ahli_gizi.orders.show', $order->id);
    }

    /**
     * Reset seluruh session konsultasi untuk order ini
     */
    public function sessionClear(Order $order)
    {
        session()->forget([
            'konsul_menus_' . $order->id,
            'konsul_note_'  . $order->id,
        ]);

        return redirect()->route('ahli_gizi.orders.show', $order->id)
            ->with('konsul_cleared', true);
    }

    /**
     * Buat link WA untuk berbagi hasil konsultasi menu ke customer
     */
    public function sessionShare(Order $order)
    {
        $order->load(['user', 'user.detail']);

        $phoneNumber = $order->user?->detail?->hp ?? null;

        if (!$phoneNumber) {
            return back()->with('error', 'Customer tidak memiliki nomor HP.');
        }

        $sessionMenus = session('konsul_menus_' . $order->id, []);
        $note         = session('konsul_note_' . $order->id, '');

        if (empty($sessionMenus)) {
            return back()->with('error', 'Belum ada menu yang dipilih di session konsultasi.');
        }

        $customerName = $order->user->name ?? 'Customer';
        $menuList     = collect($sessionMenus)->map(fn($m, $i) => ($i + 1) . '. ' . $m)->implode("\n");

        $message  = "Halo *{$customerName}*,\n\n";
        $message .= "Berikut rekomendasi menu dari konsultasi gizi Anda (Order: *{$order->order_number}*):\n\n";
        $message .= $menuList . "\n";

        if ($note) {
            $message .= "\n📝 *Catatan:*\n{$note}\n";
        }

        $message .= "\nSemoga bermanfaat! 🌿 — Tim Ahli Gizi HeartFit";

        $waNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        $waUrl    = 'https://wa.me/62' . ltrim($waNumber, '0') . '?text=' . rawurlencode($message);

        return redirect()->away($waUrl);
    }

    /**
     * Redirect ke WA customer
     */
    public function redirectToWa($userId)
    {
        $user = User::with('detail')->findOrFail($userId);

        $phoneNumber = $user->detail->hp ?? null;

        if (!$phoneNumber) {
            return back()->with('error', 'Customer tidak memiliki nomor HP.');
        }

        $waNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        $waUrl    = 'https://wa.me/62' . ltrim($waNumber, '0');

        return redirect()->away($waUrl);
    }
}
