<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MechanicLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $dispatchId;
    public float $lat;
    public float $lng;

    public function __construct(int $dispatchId, float $lat, float $lng)
    {
        $this->dispatchId = $dispatchId;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('dispatch-status.' . $this->dispatchId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'mechanic.location';
    }

    public function broadcastWith(): array
    {
        return [
            'dispatch_id' => $this->dispatchId,
            'lat'         => $this->lat,
            'lng'         => $this->lng,
        ];
    }
}
