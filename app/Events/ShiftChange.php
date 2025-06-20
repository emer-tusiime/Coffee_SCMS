<?php

namespace App\Events;

use App\Models\Workforce;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShiftChange implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $workforce;

    public function __construct(Workforce $workforce)
    {
        $this->workforce = $workforce;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('workforce.' . auth()->id());
    }

    public function broadcastAs()
    {
        return 'shift-change';
    }
}
