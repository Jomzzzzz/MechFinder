<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DispatchRequestCreated implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(public readonly array $request) {}

  public function broadcastOn(): array
  {
    // Public channel — all open shop dashboards subscribe
    return [new Channel("shop-requests")];
  }

  public function broadcastAs(): string
  {
    return "dispatch.new";
  }

  public function broadcastWith(): array
  {
    return $this->request;
  }
}
