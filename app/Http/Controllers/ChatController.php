<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // ✅ rule free trial
    private const FREE_MESSAGE_LIMIT      = 5;   // per conversation (mesej yang ME hantar)
    private const FREE_PARTICIPANT_LIMIT  = 10;  // max calon yang ditegur (first message)
    private const FREE_RESPONDER_LIMIT    = 2;   // bila 2 calon reply, yang lain lock

    private function hasActiveSubForUser(?User $me): bool
    {
        if (!$me) return false;

        return \App\Models\Subscription::where('user_id', $me->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->exists();
    }

    private function displayNameForUser(?User $other, bool $hasActiveSub): string
    {
        if (!$other) return 'User';

        if ($hasActiveSub) {
            $name = trim((string) (($other->nickname ?? '') . ' ' . ($other->name ?? '')));
            if ($name === '') $name = (string) ($other->name ?? '');
            if (trim($name) === '') $name = (string) ($other->nickname ?? '');
            if (trim($name) === '') $name = 'User';
            return $name;
        }

        $pid = trim((string) ($other->public_id ?? ''));
        return $pid !== '' ? $pid : 'Calon';
    }

    private function otherUserIdInConversation(Conversation $conversation, int $meId): int
    {
        return ($conversation->user_one_id === $meId)
            ? (int) $conversation->user_two_id
            : (int) $conversation->user_one_id;
    }

    /**
     * ✅ kira mesej yang ME hantar dalam conversation ini sahaja
     */
    private function freeMessagesSentCountInConversation(int $userId, int $conversationId): int
    {
        return (int) Message::where('conversation_id', $conversationId)
            ->where('sender_id', $userId)
            ->count();
    }

    /**
     * ✅ set of conversation_id yang "ME pernah hantar mesej"
     */
    private function myMessagedConversationIds(int $meId): array
    {
        $convIds = Conversation::query()
            ->where('user_one_id', $meId)
            ->orWhere('user_two_id', $meId)
            ->pluck('id')
            ->all();

        if (!$convIds) return [];

        return Message::query()
            ->whereIn('conversation_id', $convIds)
            ->where('sender_id', $meId)
            ->distinct()
            ->pluck('conversation_id')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    /**
     * ✅ set of "other user id" yang ME dah tegur (pernah hantar mesej)
     * = participantUsed
     */
    private function freeParticipantsMessagedSet(int $meId): array
    {
        $myConvIds = $this->myMessagedConversationIds($meId);
        if (!$myConvIds) return [];

        $convs = Conversation::query()
            ->whereIn('id', $myConvIds)
            ->get(['id', 'user_one_id', 'user_two_id']);

        $set = [];
        foreach ($convs as $c) {
            $set[] = ($c->user_one_id == $meId) ? (int) $c->user_two_id : (int) $c->user_one_id;
        }

        return array_values(array_unique($set));
    }

    /**
     * ✅ set of "other user id" yang pernah reply (ada at least 1 message from other)
     * = responderSet
     */
    private function freeRespondersSet(int $meId): array
    {
        $convIds = Conversation::query()
            ->where('user_one_id', $meId)
            ->orWhere('user_two_id', $meId)
            ->pluck('id')
            ->all();

        if (!$convIds) return [];

        // conversation yang ada message dari orang lain
        $convIdsWithOtherMsg = Message::query()
            ->whereIn('conversation_id', $convIds)
            ->where('sender_id', '!=', $meId)
            ->distinct()
            ->pluck('conversation_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        if (!$convIdsWithOtherMsg) return [];

        $convs = Conversation::query()
            ->whereIn('id', $convIdsWithOtherMsg)
            ->get(['id', 'user_one_id', 'user_two_id']);

        $set = [];
        foreach ($convs as $c) {
            $set[] = ($c->user_one_id == $meId) ? (int) $c->user_two_id : (int) $c->user_one_id;
        }

        return array_values(array_unique($set));
    }

    /**
     * ✅ determine boleh send atau tidak (free trial rules)
     * return: [canSend(bool), reason(string|null), stats(array)]
     */
    private function freeTrialGateForConversation(User $me, Conversation $conversation): array
    {
        $meId = (int) $me->id;
        $otherId = $this->otherUserIdInConversation($conversation, $meId);

        $participantSet = $this->freeParticipantsMessagedSet($meId);
        $participantsUsed = count($participantSet);

        $responderSet = $this->freeRespondersSet($meId);
        $respondersUsed = count($responderSet);

        $iAlreadyMessagedThisConv = Message::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', $meId)
            ->exists();

        $isResponderConversation = in_array($otherId, $responderSet, true);

        // Rule B: bila 2 dah reply, lock calon lain (yang tak reply)
        if ($respondersUsed >= self::FREE_RESPONDER_LIMIT && !$isResponderConversation) {
            return [
                false,
                'responders_locked',
                [
                    'participants_used' => $participantsUsed,
                    'participants_limit' => self::FREE_PARTICIPANT_LIMIT,
                    'responders_used' => $respondersUsed,
                    'responders_limit' => self::FREE_RESPONDER_LIMIT,
                    'is_responder_conversation' => $isResponderConversation,
                    'already_messaged_this_conversation' => $iAlreadyMessagedThisConv,
                ],
            ];
        }

        // Rule A: max 10 peserta ditegur (only check bila nak send first message dalam conversation ini)
        if (!$iAlreadyMessagedThisConv && $participantsUsed >= self::FREE_PARTICIPANT_LIMIT) {
            return [
                false,
                'participants_limit',
                [
                    'participants_used' => $participantsUsed,
                    'participants_limit' => self::FREE_PARTICIPANT_LIMIT,
                    'responders_used' => $respondersUsed,
                    'responders_limit' => self::FREE_RESPONDER_LIMIT,
                    'is_responder_conversation' => $isResponderConversation,
                    'already_messaged_this_conversation' => $iAlreadyMessagedThisConv,
                ],
            ];
        }

        return [
            true,
            null,
            [
                'participants_used' => $participantsUsed,
                'participants_limit' => self::FREE_PARTICIPANT_LIMIT,
                'responders_used' => $respondersUsed,
                'responders_limit' => self::FREE_RESPONDER_LIMIT,
                'is_responder_conversation' => $isResponderConversation,
                'already_messaged_this_conversation' => $iAlreadyMessagedThisConv,
            ],
        ];
    }

    public function index()
    {
        $me = Auth::user();
        if (!$me) return redirect()->route('login');
        if (!($me->lite_education_seen ?? false)) return redirect()->route('membership.education');

        $hasActiveSub = $this->hasActiveSubForUser($me);

        $conversations = Conversation::with([
                'userOne',
                'userTwo',
                'messages' => function ($q) {
                    $q->latest()->limit(1);
                },
            ])
            ->withCount([
                'messages as unread_count' => function ($q) use ($me) {
                    $q->whereNull('read_at')
                      ->where('sender_id', '!=', $me->id);
                },
            ])
            ->where(function ($q) use ($me) {
                $q->where('user_one_id', $me->id)
                  ->orWhere('user_two_id', $me->id);
            })
            ->orderByDesc('updated_at')
            ->get();

        // untuk badge lock di index
        $trialStats = [
            'participants_used' => 0,
            'participants_limit' => self::FREE_PARTICIPANT_LIMIT,
            'responders_used' => 0,
            'responders_limit' => self::FREE_RESPONDER_LIMIT,
        ];

        if ($me->isLite()) {
            $trialStats['participants_used'] = count($this->freeParticipantsMessagedSet((int)$me->id));
            $trialStats['responders_used']   = count($this->freeRespondersSet((int)$me->id));
        }

        foreach ($conversations as $conv) {
            $other = $conv->user_one_id === $me->id ? $conv->userTwo : $conv->userOne;
            $conv->other_display_name = $this->displayNameForUser($other, $hasActiveSub);

            // can_send flag untuk index view
            $conv->can_send_free = true;
            $conv->blocked_reason = null;

            if ($me->isLite()) {
                [$ok, $reason] = $this->freeTrialGateForConversation($me, $conv);
                $sentCount = $this->freeMessagesSentCountInConversation((int)$me->id, (int)$conv->id);
                $remaining = max(0, self::FREE_MESSAGE_LIMIT - $sentCount);

                if (!$ok) {
                    $conv->can_send_free = false;
                    $conv->blocked_reason = $reason;
                } elseif ($remaining <= 0) {
                    $conv->can_send_free = false;
                    $conv->blocked_reason = 'message_limit';
                }
            }
        }

        return view('chat.index', [
            'me'            => $me,
            'conversations' => $conversations,
            'hasActiveSub'  => $hasActiveSub,
            'trialStats'    => $trialStats,
        ]);
    }

    public function openWithUser(User $user)
    {
        $me = Auth::user();
        if (!$me) return redirect()->route('login');
        if (!($me->lite_education_seen ?? false)) return redirect()->route('membership.education');

        if ($me->id === $user->id) {
            return redirect()->route('chat.index');
        }

        $conversation = Conversation::where(function ($q) use ($me, $user) {
                $q->where('user_one_id', $me->id)
                  ->where('user_two_id', $user->id);
            })
            ->orWhere(function ($q) use ($me, $user) {
                $q->where('user_one_id', $user->id)
                  ->where('user_two_id', $me->id);
            })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $me->id,
                'user_two_id' => $user->id,
            ]);
        }

        return redirect()->route('chat.room.show', $conversation->uuid);
    }

    public function showRoom(Conversation $conversation)
    {
        $me = Auth::user();
        if (!$me) return redirect()->route('login');
        if (!($me->lite_education_seen ?? false)) return redirect()->route('membership.education');

        if ($conversation->user_one_id !== $me->id && $conversation->user_two_id !== $me->id) {
            abort(403);
        }

        $hasActiveSub = $this->hasActiveSubForUser($me);

        $other = $conversation->user_one_id === $me->id ? $conversation->userTwo : $conversation->userOne;
        $otherDisplayName = $this->displayNameForUser($other, $hasActiveSub);

        // ✅ per-conversation limit
        $sentCount = $this->freeMessagesSentCountInConversation((int) $me->id, (int) $conversation->id);
        $remaining = max(0, self::FREE_MESSAGE_LIMIT - $sentCount);

        $blockedReason = null;
        $trialMeta = [
            'participants_used' => 0,
            'participants_limit' => self::FREE_PARTICIPANT_LIMIT,
            'responders_used' => 0,
            'responders_limit' => self::FREE_RESPONDER_LIMIT,
            'is_responder_conversation' => false,
        ];

        if (!$me->isLite()) {
            $canSend = true;
        } else {
            [$ok, $reason, $meta] = $this->freeTrialGateForConversation($me, $conversation);
            $trialMeta = array_merge($trialMeta, $meta);

            if (!$ok) {
                $canSend = false;
                $blockedReason = $reason;
            } elseif ($remaining <= 0) {
                $canSend = false;
                $blockedReason = 'message_limit';
            } else {
                $canSend = true;
            }
        }

        // mark as read
        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $me->id)
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.show', [
            'me'               => $me,
            'other'            => $other,
            'otherDisplayName' => $otherDisplayName,
            'conversation'     => $conversation,
            'messages'         => $messages,
            'hasActiveSub'     => $hasActiveSub,

            // ✅ client-side control
            'canSend'          => $canSend,
            'remaining'        => $remaining,
            'freeLimit'        => self::FREE_MESSAGE_LIMIT,
            'blockedReason'    => $blockedReason,
            'trialMeta'        => $trialMeta,
        ]);
    }

    public function sendToRoom(Conversation $conversation, Request $request)
    {
        $me = Auth::user();
        if (!$me) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthenticated'], 401)
                : redirect()->route('login');
        }

        if ($conversation->user_one_id !== $me->id && $conversation->user_two_id !== $me->id) {
            abort(403);
        }

        $data = $request->validate([
            'body' => 'required|string',
        ]);

        $hasActiveSub = $this->hasActiveSubForUser($me);

        // ✅ Had chatting Ahli LITE: 10 sapaan, 2 calon reply, 5 mesej per calon
        if ($me->isLite()) {
            // 1) responder/participant gate
            [$ok, $reason] = $this->freeTrialGateForConversation($me, $conversation);
            if (!$ok) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'blocked' => true,
                        'reason'  => $reason, // responders_locked | participants_limit
                    ], 403);
                }

                return redirect()
                    ->route('subscription.index')
                    ->with('error', 'Sila upgrade untuk teruskan.');
            }

            // 2) per-conversation message limit (5)
            $sentCount = $this->freeMessagesSentCountInConversation((int)$me->id, (int)$conversation->id);
            if ($sentCount >= self::FREE_MESSAGE_LIMIT) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'blocked' => true,
                        'reason'  => 'message_limit',
                        'limit'   => self::FREE_MESSAGE_LIMIT,
                    ], 403);
                }

                return redirect()
                    ->route('subscription.index')
                    ->with('error', 'Had mesej percuma telah tamat. Sila subscribe.');
            }
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $me->id,
            'body'            => $data['body'],
            'read_at'         => null,
        ]);

        $conversation->touch();
        broadcast(new MessageSent($message));

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id'           => $message->id,
                    'body'         => $message->body,
                    'sender_id'    => $message->sender_id,
                    'created_at'   => $message->created_at->toDateTimeString(),
                    'conversation' => $conversation->uuid,
                ],
            ]);
        }

        return redirect()
            ->route('chat.room.show', $conversation->uuid)
            ->with('success', 'Message sent');
    }

    public function markAsRead(Conversation $conversation, Request $request)
    {
        $me = Auth::user();

        if ($conversation->user_one_id !== $me->id && $conversation->user_two_id !== $me->id) {
            abort(403);
        }

        $updated = $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $me->id)
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }
}
