<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'package_key',
        'package_label',
        'package_category',
        'package_batch',
        'package_price',
        'amount_total',
        'start_date',
        'end_date',
        'days',
        'service_dates',
        'unique_menus',
        'unique_menu_count',
        'payment_method',
        'status',
        'paid_at',
        'meta',
        'notes',
        'whatsapp',
    ];

    protected $casts = [
        'start_date'        => 'datetime',
        'end_date'          => 'datetime',
        'paid_at'           => 'datetime',
        'service_dates'     => 'array',
        'unique_menus'      => 'array',
        'unique_menu_count' => 'integer',
        'package_price'     => 'integer',
        'amount_total'      => 'integer',
        'meta'              => 'array',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    // ── Accessors / Mutators ────────────────────────────────────────

    protected function packagePrice(): Attribute
    {
        return Attribute::make(
            set: fn($value) => is_null($value) ? null : (int) $value
        );
    }

    protected function amountTotal(): Attribute
    {
        return Attribute::make(
            set: fn($value) => is_null($value) ? null : (int) $value
        );
    }

    protected function period(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->start_date && $this->end_date
                ? $this->start_date->format('Y-m-d') . ' s/d ' . $this->end_date->format('Y-m-d')
                : null
        );
    }

    // ── Relasi ──────────────────────────────────────────────────────

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // ── Pickup Progress ─────────────────────────────────────────────

    /**
     * Hitung progres pengambilan order ini dari tabel order_delivery_statuses.
     *
     * Lookup berdasarkan rentang tanggal saja (bukan package_key) karena
     * GenerateDailyDeliveryStatuses memakai MIN(id) per batch sebagai representative,
     * bukan per package_key — sehingga meal_package_id tidak selalu cocok dengan
     * package_key yang ada di order.
     */
    public function getPickupProgress(): array
    {
        if (!$this->start_date || !$this->end_date) {
            return $this->_emptyProgress();
        }

        $start = $this->start_date instanceof \Carbon\Carbon
            ? $this->start_date->toDateString()
            : \Carbon\Carbon::parse($this->start_date)->toDateString();

        $end = $this->end_date instanceof \Carbon\Carbon
            ? $this->end_date->toDateString()
            : \Carbon\Carbon::parse($this->end_date)->toDateString();

        $rows = \App\Models\OrderDeliveryStatus::whereBetween('delivery_date', [$start, $end])
            ->orderBy('delivery_date')
            ->get();

        $siangDiambil = $rows->where('status_siang', 'diambil')->count();
        $malamDiambil = $rows->where('status_malam', 'diambil')->count();
        $rowCount     = $rows->count();
        $totalSesi    = $rowCount * 2;
        $totalDiambil = $siangDiambil + $malamDiambil;
        $hariDiambil  = $rows->filter(
            fn($r) => $r->status_siang === 'diambil' && $r->status_malam === 'diambil'
        )->count();

        $hariTotal = (int) round(
            \Carbon\Carbon::parse($start)->diffInDays(\Carbon\Carbon::parse($end))
        ) + 1;

        $persen = $totalSesi > 0 ? (int) round(($totalDiambil / $totalSesi) * 100) : 0;

        return [
            'total_sesi'    => $totalSesi,
            'total_diambil' => $totalDiambil,
            'diambil_siang' => $siangDiambil,
            'diambil_malam' => $malamDiambil,
            'siang_total'   => $rowCount,
            'malam_total'   => $rowCount,
            'hari_total'    => $hariTotal,
            'hari_diambil'  => $hariDiambil,
            'persen'        => $persen,
            'rows'          => $rows,
        ];
    }

    /**
     * Ambil semua baris ODS dalam rentang tanggal order.
     * Digunakan untuk tabel riwayat per-hari di view.
     */
    public function getPickupHistory()
    {
        if (!$this->start_date || !$this->end_date) {
            return collect();
        }

        $start = $this->start_date instanceof \Carbon\Carbon
            ? $this->start_date->toDateString()
            : \Carbon\Carbon::parse($this->start_date)->toDateString();

        $end = $this->end_date instanceof \Carbon\Carbon
            ? $this->end_date->toDateString()
            : \Carbon\Carbon::parse($this->end_date)->toDateString();

        return \App\Models\OrderDeliveryStatus::whereBetween('delivery_date', [$start, $end])
            ->orderBy('delivery_date')
            ->get();
    }

    private function _emptyProgress(): array
    {
        return [
            'total_sesi'    => 0,
            'total_diambil' => 0,
            'diambil_siang' => 0,
            'diambil_malam' => 0,
            'siang_total'   => 0,
            'malam_total'   => 0,
            'hari_total'    => 0,
            'hari_diambil'  => 0,
            'persen'        => 0,
            'rows'          => collect(),
        ];
    }
}
