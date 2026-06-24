<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateDailyDeliveryStatuses extends Command
{
    protected $signature = 'heartfit:generate-delivery-statuses {--date=} {--all}';
    protected $description = 'Generate pending (siang & malam) delivery rows per menu_makanan (1x per menu, bukan per meal_package)';

    public function handle()
    {
        $tz   = 'Asia/Jakarta';
        $date = $this->option('date') ?: now($tz)->toDateString();
        $all  = $this->option('all');

        // Nama hari Indonesia & angka ISO (1=Senin..7=Minggu)
        $hariNama  = now($tz)->locale('id')->isoFormat('dddd'); // "Senin", "Selasa", ...
        $hariAngka = (int) now($tz)->isoWeekday();              // 1..7

        // 1. Ambil semua order aktif (PAID/SETTLEMENT) yang masuk dalam periode tanggal
        $activeOrders = DB::table('orders as o')
            ->join('meal_packages as mp', 'o.package_key', '=', 'mp.id')
            ->select('o.id as order_id', 'mp.batch as batch')
            ->whereIn('o.status', ['PAID', 'SETTLEMENT'])
            ->where('o.start_date', '<=', $date)
            ->where('o.end_date', '>=', $date)
            ->get();

        $inserted = 0;

        // 2. Loop per order
        foreach ($activeOrders as $order) {
            // Cari menu untuk batch order ini dan hari ini
            $menus = DB::table('menu_makanans as mm')
                ->where('mm.batch', $order->batch)
                ->where(function ($q) use ($hariNama, $hariAngka, $all) {
                    if (!$all) {
                        $q->whereRaw('JSON_CONTAINS(mm.serve_days, JSON_QUOTE(?))', [$hariNama])
                          ->orWhereRaw('JSON_CONTAINS(mm.serve_days, ?)', [json_encode($hariAngka)]);
                    }
                })
                ->get();

            // Biasanya ada 1 menu per hari untuk satu batch
            foreach ($menus as $menu) {
                // Cek apakah sudah digenerate untuk order ini + menu ini + tanggal ini
                $exists = DB::table('order_delivery_statuses')
                    ->where('order_id', $order->order_id)
                    ->where('menu_makanan_id', $menu->id)
                    ->where('delivery_date', $date)
                    ->exists();

                if (!$exists) {
                    DB::table('order_delivery_statuses')->insert([
                        'order_id'        => $order->order_id,
                        'menu_makanan_id' => $menu->id,
                        'delivery_date'   => $date,
                        'status_siang'    => 'pending',
                        'status_malam'    => 'pending',
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                    $inserted++;
                }
            }
        }

        $this->info("{$date} | hari: {$hariNama} ({$hariAngka}) | inserted: {$inserted} (per order/user)");
        return self::SUCCESS;
    }
}
