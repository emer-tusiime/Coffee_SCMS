<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use App\Models\Product;

class QualityComplaintNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $factory;
    public $product;
    public $messageText;

    public function __construct(User $factory, Product $product, $messageText)
    {
        $this->factory = $factory;
        $this->product = $product;
        $this->messageText = $messageText;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Factory Complaint: Poor Raw Material/Product')
            ->greeting('Hello Admin,')
            ->line('A factory has submitted a complaint about a supplier product.')
            ->line('Factory: ' . $this->factory->name)
            ->line('Product: ' . $this->product->name)
            ->line('Complaint: ' . $this->messageText)
            ->action('View Product', url('/admin/products/' . $this->product->id))
            ->line('Please review and take appropriate action.');
    }

    public function toArray($notifiable)
    {
        return [
            'factory_id' => $this->factory->id,
            'factory_name' => $this->factory->name,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'message' => $this->messageText,
        ];
    }
} 