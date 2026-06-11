<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('dispatch.' . $this->message->dispatch_id);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'dispatch_id' => $this->message->dispatch_id,
                'sender_type' => $this->message->sender_type,
                'sender_name' => $this->message->sender_name,
                'conversation_type' => $this->message->conversation_type,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at->toIso8601String(),
                'is_read' => $this->message->is_read,
            ],
        ];
    }
}
