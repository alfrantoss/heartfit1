<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class OrdersExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    ShouldAutoSize,
    WithTitle
{
    protected $dateFrom;
    protected $dateTo;
    protected $q;
    protected $status;

    public function __construct(?string $dateFrom = null, ?string $dateTo = null, ?string $q = null, ?string $status = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
        $this->q        = $q;
        $this->status   = $status;
    }

    public function collection()
    {
        return Order::with([
            'user:id,name,email',
            'user.detail:id,user_id,hp,alamat',
        ])
        ->when($this->q, function ($query) {
            $q = $this->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('package_label', 'like', "%{$q}%")
                    ->orWhere('package_category', 'like', "%{$q}%")
                    ->orWhereHas('user', fn($uq) => $uq->withTrashed()
                        ->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%"));
            });
        })
        ->when($this->status, fn($q) => $q->where('status', $this->status))
        ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
        ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
        ->latest('id')
        ->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'No. Order',
            'Nama Customer',
            'Email',
            'WhatsApp',
            'Paket',
            'Kategori',
            'Mulai',
            'Selesai',
            'Durasi (Hari)',
            'Harga Paket',
            'Total Bayar',
            'Metode Bayar',
            'Status',
            'Tanggal Bayar',
            'Tanggal Dibuat',
            'Catatan',
        ];
    }

    protected int $rowIndex = 1;

    public function map($order): array
    {
        return [
            $this->rowIndex++,
            $order->order_number ?? '-',
            $order->user?->name ?? '—',
            $order->user?->email ?? '—',
            $order->whatsapp ?? ($order->user?->detail?->hp ?? '-'),
            $order->package_label ?? '-',
            ucfirst($order->package_category ?? '-'),
            $order->start_date ? $order->start_date->format('d/m/Y') : '-',
            $order->end_date ? $order->end_date->format('d/m/Y') : '-',
            (int) ($order->days ?? 0),
            (int) ($order->package_price ?? 0),
            (int) ($order->amount_total ?? $order->package_price ?? 0),
            strtoupper(str_replace('_', ' ', $order->payment_method ?? '-')),
            strtoupper($order->status ?? '-'),
            $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : '-',
            $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-',
            $order->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row
            1 => [
                'font'    => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF28A745']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Orders';
    }
}
