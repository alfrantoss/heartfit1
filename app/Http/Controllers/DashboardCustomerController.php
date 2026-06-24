<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDeliveryStatus;
use App\Models\MealPackages;
use App\Models\PackageType;
use App\Models\MenuMakanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardCustomerController extends Controller
{
    public function index(Request $request)
    {
        $tz   = 'Asia/Jakarta';
        $date = now($tz)->toDateString();
        $userId = Auth::id();

        // Ambil order aktif customer hari ini (status PAID, tanggal masih berlaku)
        $activeOrders = Order::where('user_id', $userId)
            ->whereIn('status', ['PAID', 'SETTLEMENT'])
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->get();

        // Delivery status hari ini — berdasarkan order_id
        $orderIds = $activeOrders->pluck('id')->filter()->unique()->values();

        if ($orderIds->isNotEmpty()) {
            $items = OrderDeliveryStatus::with(['order', 'menuMakanan'])
                ->whereDate('delivery_date', $date)
                ->whereIn('order_id', $orderIds)
                ->orderByRaw("FIELD(status_siang, 'pending','diterima','diproses','siap','diambil')")
                ->orderByRaw("FIELD(status_malam, 'pending','diterima','diproses','siap','diambil')")
                ->get();
        } else {
            $items = collect();
        }

        // Riwayat order unpaid (sudah buat pesanan tapi belum bayar)
        $unpaidOrders = Order::where('user_id', $userId)
            ->where('status', 'UNPAID')
            ->latest()
            ->take(5)
            ->get();

        // Riwayat penerimaan pesanan per hari (delivery history)
        $deliveryHistory = OrderDeliveryStatus::with(['order', 'menuMakanan'])
            ->whereIn('order_id', 
                Order::where('user_id', $userId)
                    ->whereIn('status', ['PAID', 'SETTLEMENT'])
                    ->pluck('id')
            )
            ->whereDate('delivery_date', '<=', $date)
            ->orderByDesc('delivery_date')
            ->take(10)
            ->get()
            ->groupBy(fn($item) => $item->delivery_date->toDateString());

        // Paket aktif saat ini
        $hasActiveOrder = $activeOrders->isNotEmpty();

        // Ambil data paket dari DB untuk tampilan katalog
        $packageTypes      = PackageType::orderBy('id')->get();
        $mealPackagesByType = MealPackages::all()->groupBy('package_type_id');

        $menus = MenuMakanan::where('batch', 'I')
            ->get(['id', 'nama_menu', 'serve_days', 'spec_menu', 'foto_makanan'])
            ->map(function ($m) {
                $serve = is_array($m->serve_days) ? $m->serve_days : [];
                $serve = array_values(array_filter(array_map(fn($v) => (int) $v, $serve), fn($n) => $n >= 1 && $n <= 31));
                return [
                    'id'           => $m->id,
                    'nama_menu'    => $m->nama_menu,
                    'serve_days'   => $serve,
                    'spec_menu'    => $m->spec_menu ?? [],
                    'foto_makanan' => $m->foto_makanan ?? [],
                ];
            })
            ->values()
            ->toArray();

        $packages = [];
        foreach ($packageTypes as $type) {
            $key = strtolower($type->packageType);
            $typeMeals = $mealPackagesByType->get($type->id, collect());
            $packages[$key] = [
                'id'            => $type->id,
                'type'          => $type->packageType,
                'meal_packages' => $typeMeals,
                'menus'         => array_values(array_filter($menus, fn($m) =>
                    isset($m['spec_menu']) && is_array($m['spec_menu']) && !empty($m['spec_menu'])
                )),
            ];
        }

        return view('customers.dashboard', compact(
            'items', 'date', 'packages',
            'hasActiveOrder', 'unpaidOrders', 'deliveryHistory'
        ));
    }
}
