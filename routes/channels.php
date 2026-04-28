<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

// Channel untuk chat room (kekalkan yang lama)
Broadcast::channel('conversation.{conversationKey}', function ($user, $conversationKey) {
    $conversation = Conversation::where('uuid', $conversationKey)->first();

    if (! $conversation) {
        return false;
    }

    return $conversation->user_one_id === $user->id
        || $conversation->user_two_id === $user->id;
});

// 🔹 Channel global per-user untuk notification
Broadcast::channel('user.{id}', function ($user, $id) {
    return (string) $user->id === (string) $id;
});
