<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DispatchMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $dispatchId;
    public array $message;

    public function __construct(int $dispatchId, array $message)
    {
        $this->dispatchId = $dispatchId;
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('dispatch.' . $this->dispatchId)];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'dispatch_id' => $this->dispatchId,
            'message' => $this->message,
        ];
    }
}
