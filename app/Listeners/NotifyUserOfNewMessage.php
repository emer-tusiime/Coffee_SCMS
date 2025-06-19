<?php

namespace App\Listeners;

use App\Events\ChatMessageSent;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewChatMessageNotification;

class NotifyUserOfNewMessage implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\ChatMessageSent  $event
     * @return void
     */
    public function handle(ChatMessageSent $event)
    {
        // Get the receiver of the message
        $receiver = User::find($event->chatMessage->receiver_id);

        if ($receiver) {
            Notification::send($receiver, new NewChatMessageNotification($event->chatMessage));
        }
    }
}