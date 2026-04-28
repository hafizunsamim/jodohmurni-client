<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Notifications\NewMessageNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendUnreadMessageEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * php artisan chat:send-unread-emails
     */
    protected $signature = 'chat:send-unread-emails';

    /**
     * The console command description.
     */
    protected $description = 'Hantar email untuk mesej yang masih belum dibaca';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $totalChecked = 0;
        $totalSent    = 0;

        // Buat simple dulu: TIADA threshold masa.
        // Semua mesej yang:
        //  - read_at IS NULL
        //  - email_notified_at IS NULL
        Message::whereNull('read_at')
            ->whereNull('email_notified_at')
            ->with(['conversation.userOne', 'conversation.userTwo', 'sender'])
            ->chunkById(100, function ($messages) use (&$totalChecked, &$totalSent) {
                foreach ($messages as $message) {
                    $totalChecked++;

                    $conversation = $message->conversation;
                    if (! $conversation) {
                        continue;
                    }

                    // tentukan penerima
                    if ($message->sender_id === $conversation->user_one_id) {
                        $recipient = $conversation->userTwo;
                    } else {
                        $recipient = $conversation->userOne;
                    }

                    if (! $recipient || ! $recipient->email) {
                        continue;
                    }

                    try {
                        $recipient->notify(new NewMessageNotification($message));

                        $message->email_notified_at = now();
                        $message->save();

                        $totalSent++;
                    } catch (\Throwable $e) {
                        Log::error('chat:send-unread-emails gagal: '.$e->getMessage(), [
                            'message_id'   => $message->id,
                            'recipient_id' => $recipient->id ?? null,
                        ]);
                    }
                }
            });

        $this->info("Selesai check. Diperiksa: {$totalChecked}, email dihantar: {$totalSent}");

        return self::SUCCESS;
    }
}
