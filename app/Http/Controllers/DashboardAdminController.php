<?php

namespace App\Http\Controllers;

use App\Models\OrderDeliveryStatus;
use App\Models\Order;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DashboardAdminController extends Controller
{
    public function index(Request $request)
    {
        $tz   = 'Asia/Jakarta';
        $date = $request->get('date', now($tz)->toDateString());

        $items = OrderDeliveryStatus::with(['order.user', 'order.user.detail', 'menuMakanan', 'confirmer'])
            ->whereDate('delivery_date', $date)
            ->orderByRaw("FIELD(status_siang, 'pending','diterima','diproses','siap','diambil')")
            ->orderByRaw("FIELD(status_malam, 'pending','diterima','diproses','siap','diambil')")
            ->get();

        $groupedDeliveries = $items->groupBy('menu_makanan_id');

        $total = max(1, $items->count());
        $agg = [
            'siang' => [
                'pending'  => $items->where('status_siang', 'pending')->count(),
                'diterima' => $items->where('status_siang', 'diterima')->count(),
                'diproses' => $items->where('status_siang', 'diproses')->count(),
                'siap'     => $items->where('status_siang', 'siap')->count(),
                'diambil'  => $items->where('status_siang', 'diambil')->count(),
            ],
            'malam' => [
                'pending'  => $items->where('status_malam', 'pending')->count(),
                'diterima' => $items->where('status_malam', 'diterima')->count(),
                'diproses' => $items->where('status_malam', 'diproses')->count(),
                'siap'     => $items->where('status_malam', 'siap')->count(),
                'diambil'  => $items->where('status_malam', 'diambil')->count(),
            ],
            'total' => $total
        ];

        // ── Preview 3 hari ke depan: order aktif per user ──
        $upcomingDays = [];
        for ($i = 1; $i <= 3; $i++) {
            $futureDate = Carbon::parse($date, $tz)->addDays($i)->toDateString();
            $futureOrders = Order::with(['user:id,name', 'user.detail:user_id,hp'])
                ->whereIn('status', ['PAID', 'SETTLEMENT'])
                ->whereDate('start_date', '<=', $futureDate)
                ->whereDate('end_date', '>=', $futureDate)
                ->orderBy('package_label')
                ->orderBy('user_id')
                ->get();

            $upcomingDays[] = [
                'date'    => $futureDate,
                'label'   => Carbon::parse($futureDate, $tz)->locale('id')->isoFormat('D MMMM Y'),
                'total'   => $futureOrders->count(),
                'orders'  => $futureOrders,
            ];
        }

        // KPI
        $today = Carbon::today($tz);
        $kpi = [
            'total_orders'       => Order::count(),
            'paid_orders'        => Order::whereIn('status', ['PAID', 'SETTLEMENT'])->count(),
            'unpaid_orders'      => Order::where('status', 'UNPAID')->count(),
            'active_today'       => Order::whereIn('status', ['PAID', 'SETTLEMENT'])
                                         ->whereDate('start_date', '<=', $today)
                                         ->whereDate('end_date', '>=', $today)
                                         ->count(),
            'revenue_this_month' => Order::whereIn('status', ['PAID', 'SETTLEMENT'])
                                         ->whereYear('paid_at', $today->year)
                                         ->whereMonth('paid_at', $today->month)
                                         ->sum('amount_total'),
        ];

        return view('admin.dashboard', compact('groupedDeliveries', 'date', 'agg', 'kpi', 'upcomingDays'));
    }

    public function updateStatus(Request $request, OrderDeliveryStatus $delivery)
    {
        $allowed = config('settings.delivery.update_status', []);
        if (!in_array(Auth::user()->role, $allowed)) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status pesanan.');
        }

        $validated = $request->validate([
            'field' => ['required', Rule::in(['status_siang', 'status_malam'])],
            'value' => ['required', Rule::in(['pending', 'diterima', 'diproses', 'siap', 'diambil'])],
            'note'  => ['nullable', 'string']
        ]);

        $field    = $validated['field'];
        $newValue = $validated['value'];

        $delivery->{$field} = $newValue;
        $delivery->confirmed_by = Auth::id();
        $delivery->confirmed_at = now('Asia/Jakarta');
        if (array_key_exists('note', $validated)) {
            $delivery->note = $validated['note'];
        }
        $delivery->save();

        // Kirim notifikasi WhatsApp ke customer jika status bukan 'pending'
        if ($newValue !== 'pending') {
            try {
                $delivery->load(['order.user', 'order.user.detail', 'menuMakanan']);
                app(FonnteService::class)->sendPickupStatusUpdate($delivery, $field, $newValue);
            } catch (\Exception $e) {
                Log::warning('[Fonnte] Gagal kirim notif status pickup', ['error' => $e->getMessage()]);
            }
        }

        return redirect()
            ->route('dashboard.admin')
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function generateDelivery(Request $request)
    {
        $allowed = config('settings.delivery.generate', []);
        if (!in_array(Auth::user()->role, $allowed)) {
            abort(403, 'Anda tidak memiliki akses untuk membuat delivery.');
        }

        $tz   = 'Asia/Jakarta';
        $date = $request->input('date', now($tz)->toDateString());

        $exitCode = Artisan::call('heartfit:generate-delivery-statuses', [
            '--date' => $date,
        ]);

        if ($exitCode === 0) {
            return redirect()
                ->route('dashboard.admin', ['date' => $date])
                ->with('success', "Generate delivery berhasil");
        }

        return redirect()
            ->route('dashboard.admin', ['date' => $date])
            ->with('error', "Generate delivery gagal");
    }
}
