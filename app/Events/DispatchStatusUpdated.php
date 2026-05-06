<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DispatchStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $dispatchId,
        public readonly string $status
    ) {}

    public function broadcastOn(): array
    {
        // Public channel — guests (unauthenticated motorists) can subscribe
        return [
            new Channel('dispatch-status.' . $this->dispatchId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'dispatch.status';
    }

    public function broadcastWith(): array
    {
        return [
            'dispatch_id' => $this->dispatchId,
            'status'      => $this->status,
        ];
    }
}
