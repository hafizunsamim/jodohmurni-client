<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class NewMessageNotification extends Notification
{
    use Queueable;

    public Message $message;

    public function __construct(Message $message)
    {
        // Pastikan sender + conversation loaded
        $this->message = $message->loadMissing('sender', 'conversation');
    }

    public function via($notifiable): array
    {
        // Email je dulu (Mailtrap)
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $sender       = $this->message->sender;
        $conversation = $this->message->conversation;

        $senderName = $sender->nickname
            ?? $sender->name
            ?? 'Pengguna';

        return (new MailMessage)
            ->subject('Mesej baru di JodohMurni')
            ->greeting('Hi ' . ($notifiable->name ?? ''))
            ->line('Anda menerima mesej baru daripada ' . $senderName . '.')
            ->line('Mesej: "' . Str::limit($this->message->body, 80) . '"')
            ->action('Buka chat', route('chat.room.show', $conversation->uuid))
            ->line('Jika anda tidak mengenali mesej ini, abaikan sahaja email ini.');
    }

    public function toArray($notifiable): array
    {
        $sender       = $this->message->sender;
        $conversation = $this->message->conversation;

        return [
            'message_id'        => $this->message->id,
            'conversation_uuid' => $conversation->uuid,
            'sender_id'         => $this->message->sender_id,
            'sender_name'       => $sender->nickname ?? $sender->name ?? null,
            'body'              => $this->message->body,
        ];
    }
}
