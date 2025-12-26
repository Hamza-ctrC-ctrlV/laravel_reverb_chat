<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $receiverId;
    public $senderId;

    public function __construct($receiverId, $senderId)
    {
        $this->receiverId = $receiverId;
        $this->senderId = $senderId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("chat.{$this->receiverId}");
    }

    public function broadcastWith()
    {
        return [
            'senderId' => $this->senderId,
        ];
    }
}
