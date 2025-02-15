<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $messageId;
    public $userId;

    public function __construct($messageId, $userId)
    {
        $this->messageId = $messageId;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.read.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'message.read';
    }
}
