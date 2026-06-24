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

        // Delivery status hari ini — hanya untuk paket yg dimiliki customer
        $mealPackageIds = $activeOrders->pluck('package_key')->filter()->unique()->values();

        if ($mealPackageIds->isNotEmpty()) {
            $items = OrderDeliveryStatus::with(['mealPackage', 'menuMakanan'])
                ->whereDate('delivery_date', $date)
                ->whereIn('meal_package_id', $mealPackageIds)
                ->orderByRaw("FIELD(status_siang, 'pending','sedang dikirim','sampai','gagal dikirim')")
                ->orderByRaw("FIELD(status_malam, 'pending','sedang dikirim','sampai','gagal dikirim')")
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
        $deliveryHistory = OrderDeliveryStatus::with(['mealPackage'])
            ->whereIn('meal_package_id', 
                Order::where('user_id', $userId)
                    ->whereIn('status', ['PAID', 'SETTLEMENT'])
                    ->pluck('package_key')
                    ->filter()
                    ->unique()
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
