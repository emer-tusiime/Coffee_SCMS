<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChangedNotification extends Notification
{
    use Queueable;

    public $order;
    public $status;

    public function __construct(Order $order, string $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Status Update')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your order #' . $this->order->id . ' has been updated.')
            ->line('New Status: ' . $this->status)
            ->line('Order Details: ')
            ->line('Supplier: ' . optional($this->order->supplier)->name)
            ->line('Total Items: ' . $this->order->items->count())
            ->line('Estimated Delivery: ' . optional($this->order->estimated_delivery_date)->format('Y-m-d'))
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('Thank you for using our supply chain management system!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->status,
            'supplier_name' => optional($this->order->supplier)->name,
            'total_items' => $this->order->items->count(),
            'estimated_delivery_date' => optional($this->order->estimated_delivery_date)->format('Y-m-d')
        ];
    }
}
