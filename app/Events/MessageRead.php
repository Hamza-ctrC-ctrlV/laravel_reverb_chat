<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // CRITICAL
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast // MUST IMPLEMENT THIS
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @param int $readerId The person who is reading the message (Auth::id())
     * @param int $receiverId The person who needs to see the "Read" status
     */
    public function __construct(public $readerId, public $receiverId) {}

    public function broadcastOn(): array
    {
        // We broadcast to the private channel of the person who needs the update
        return [
            new PrivateChannel('chat.' . $this->receiverId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'readerId' => $this->readerId,
            'receiverId' => $this->receiverId,
        ];
    }
}