<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DispatchStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $dispatchId;
    public string $status;
    public ?int $shopId;

    /**
     * @param int $dispatchId
     * @param string $status
     * @param int|null $shopId
     */
    public function __construct(int $dispatchId, string $status, ?int $shopId = null)
    {
        $this->dispatchId = $dispatchId;
        $this->status = $status;
        $this->shopId = $shopId;
    }

    public function broadcastOn(): array
    {
        $channels = [
            new Channel('dispatch-status.' . $this->dispatchId),
        ];

        if ($this->shopId) {
            $channels[] = new PrivateChannel('shop.' . $this->shopId);
        }

        return $channels;
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
            'shop_id'     => $this->shopId,
        ];
    }
}
