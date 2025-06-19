<?php

namespace App\Notifications;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $chatMessage;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\ChatMessage $chatMessage
     */
    public function __construct(ChatMessage $chatMessage)
    {
        $this->chatMessage = $chatMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // add 'broadcast' if using websockets
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received a new chat message from ' . $this->chatMessage->sender->name . '.')
            ->line('Message: "' . $this->chatMessage->message . '"')
            ->action('View Message', url('/chat/' . $this->chatMessage->sender_id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'chat_message_id' => $this->chatMessage->id,
            'sender_id' => $this->chatMessage->sender_id,
            'sender_name' => $this->chatMessage->sender->name,
            'message' => $this->chatMessage->message,
            'sent_at' => $this->chatMessage->created_at,
        ];
    }
}