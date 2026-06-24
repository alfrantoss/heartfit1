<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentSuccessNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'payment_success',
            'title'        => 'Pembayaran Berhasil',
            'message'      => "Pembayaran pesanan #{$this->order->order_number} sebesar Rp " . number_format($this->order->amount_total ?? $this->order->package_price, 0, ',', '.') . " telah dikonfirmasi.",
            'order_id'     => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount'       => $this->order->amount_total ?? $this->order->package_price,
            'status'       => 'PAID',
            'icon'         => 'bx-check-circle',
            'color'        => 'success',
            'url'          => route('customer.orders.index'),
        ];
    }
}
