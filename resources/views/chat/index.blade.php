<link rel="stylesheet" href="{{ asset('css/chat.css') }}">

@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 800px;">
    <h4 class="mb-3 fw-bold">Chats</h4>

    @if(!$hasActiveSub && isset($trialStats))
        <div class="alert alert-info py-2 small">
            Free Trial • Tegur: {{ $trialStats['participants_used'] ?? 0 }}/{{ $trialStats['participants_limit'] ?? 10 }}
            • Reply: {{ $trialStats['responders_used'] ?? 0 }}/{{ $trialStats['responders_limit'] ?? 2 }}
            • (Bila 2 calon dah balas, calon lain akan dikunci)
        </div>
    @endif

    @if ($conversations->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <img src="{{ asset('assets/photos/flat_no_message.jpg') }}" alt="No chats yet"
                     class="img-fluid chat-empty-illustration" style="max-width: 220px; opacity: 0.8;">
            </div>
            <h5 class="text-muted mb-2">Belum ada perbualan</h5>
            <p class="text-muted small px-3">
                Apabila anda mula berbual dengan calon, perbualan akan muncul di sini.
            </p>
        </div>
    @else
        <div class="chat-list">
            @foreach ($conversations as $conv)
                @php
                    $other = $conv->user_one_id === $me->id ? $conv->userTwo : $conv->userOne;

                    $last = $conv->messages->first();
                    $unread = $conv->unread_count ?? 0;
                    $hasPhoto = $other->photo_1 ?? null;
                    $avatar = $hasPhoto ? asset($other->photo_1) : asset('assets/photos/default_avatar.png');

                    $displayName = $conv->other_display_name ?? ($other->public_id ?? ($other->nickname ?? ($other->name ?? 'User')));

                    $locked = (!$hasActiveSub) && (isset($conv->can_send_free) && !$conv->can_send_free);
                @endphp

                <a href="{{ route('chat.room.show', $conv->uuid) }}"
                   class="chat-item d-flex align-items-start p-3 text-decoration-none rounded-3 mb-2 position-relative"
                   tabindex="0">

                    <div class="me-3 flex-shrink-0">
                        <div class="avatar-container">
                            <img src="{{ $avatar }}" alt="{{ $displayName }}" class="avatar">
                            @if ($other->is_online ?? false)
                                <span class="online-indicator"></span>
                            @endif
                            @if ($unread > 0)
                                <span class="unread-badge bg-danger"></span>
                            @endif
                        </div>
                    </div>

                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="mb-0 fw-semibold text-truncate">
                                {{ $displayName }}

                                @if($locked)
                                    <span class="badge bg-warning text-dark ms-2">Locked</span>
                                @endif
                            </h6>
                            @if ($last && $last->created_at)
                                <small class="text-muted flex-shrink-0 ms-2">
                                    {{ $last->created_at->format('d/m H:i') }}
                                </small>
                            @endif
                        </div>

                        @if ($last)
                            <div class="text-muted small mt-1 text-truncate">
                                @if ($last->sender_id == $me->id)
                                    <span class="text-primary me-1">You:</span>
                                @endif
                                {{ \Illuminate\Support\Str::limit($last->body, 45) }}
                            </div>
                        @endif
                    </div>

                    @if ($unread > 0)
                        <div class="ms-2 flex-shrink-0">
                            <span class="badge bg-danger rounded-pill fw-bold">{{ $unread }}</span>
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>

@include('partials.footer-nav', ['active' => 'chat'])
@endsection
