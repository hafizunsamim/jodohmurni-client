<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast serta-merta (tanpa queue) supaya mesej sampai real-time
 * tanpa perlu jalankan queue:work.
 */
class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Mesej yang dihantar
     */
    public Message $message;

    /**
     * UUID untuk conversation (untuk channel conversation.{uuid})
     */
    public string $conversationUuid;

    /**
     * ID penerima (untuk channel user.{id})
     */
    public string $recipientId;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        // Pastikan sender + conversation diload
        $this->message = $message->loadMissing('sender', 'conversation');
        $conversation  = $this->message->conversation;

        $this->conversationUuid = $conversation->uuid;

        // Cari penerima: kalau sender = user_one → recipient = user_two, dan sebaliknya
        $this->recipientId = ($this->message->sender_id === $conversation->user_one_id)
            ? (string) $conversation->user_two_id
            : (string) $conversation->user_one_id;
    }

    /**
     * Channel broadcast
     */
    public function broadcastOn(): array
    {
        return [
            // Channel untuk chat room (page show.blade)
            new PrivateChannel('conversation.' . $this->conversationUuid),

            // Channel global per-user (untuk dashboard, footer listener)
            new PrivateChannel('user.' . $this->recipientId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Payload yang dihantar ke Echo (e.message ...)
     */
    public function broadcastWith(): array
    {
        $sender = $this->message->sender;

        return [
            'message' => [
                'id'                => $this->message->id,
                'body'              => $this->message->body,
                'sender_id'         => $this->message->sender_id,
                'sender_name'       => $sender->nickname ?? $sender->name ?? 'Pengguna',
                'created_at'        => optional($this->message->created_at)->toIso8601String(),
                'conversation_uuid' => $this->conversationUuid,
            ],
        ];
    }
}
