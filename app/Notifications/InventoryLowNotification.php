<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InventoryLowNotification extends Notification
{
    use Queueable;

    public $product;
    public $threshold;
    public $method;

    public function __construct(Product $product, int $threshold, string $method)
    {
        $this->product = $product;
        $this->threshold = $threshold;
        $this->method = $method;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Low Inventory Alert')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Low inventory alert for product: ' . $this->product->name)
            ->line('Current Stock: ' . $this->product->current_stock)
            ->line('Threshold: ' . $this->threshold)
            ->action('View Product', url('/products/' . $this->product->id))
            ->line('Please reorder soon to avoid stockouts.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->product->current_stock,
            'threshold' => $this->threshold,
            'alert_method' => $this->method,
            'created_at' => now()
        ];
    }
}
