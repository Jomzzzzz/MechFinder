<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShopStatusUpdated implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public readonly int $shopId,
    public readonly string $status
  ) {}

  public function broadcastOn(): array
  {
    // Public channel — any motorist can subscribe without auth
    return [new Channel("shops-status")];
  }

  public function broadcastAs(): string
  {
    return "shop.status";
  }

  public function broadcastWith(): array
  {
    return [
      "shop_id" => $this->shopId,
      "status" => $this->status,
    ];
  }
}
