<link rel="stylesheet" href="{{ asset('css/chat.css') }}">

@extends('layouts.app')

@section('content')
@php
    $hasPhoto = $other->photo_1 ?? null;
    $avatar = $hasPhoto ? asset($other->photo_1) : asset('assets/photos/default_avatar.png');

    $displayName = $otherDisplayName ?? ($other->public_id ?? ($other->nickname ?? $other->name ?? 'User'));

    $canSendBool = (bool)($canSend ?? true);
    $blockedReason = $blockedReason ?? null;
    $remaining = (int)($remaining ?? 0);

    $trialMeta = $trialMeta ?? [];
    $participantsUsed = (int)($trialMeta['participants_used'] ?? 0);
    $participantsLimit = (int)($trialMeta['participants_limit'] ?? 10);
    $respondersUsed = (int)($trialMeta['responders_used'] ?? 0);
    $respondersLimit = (int)($trialMeta['responders_limit'] ?? 2);
@endphp

<div class="container py-4" style="max-width: 800px;">
    <div class="chat-header mb-2 rounded-3">
        <a href="{{ route('chat.index') }}" class="btn btn-sm btn-light">
            ←
        </a>

        <div class="chat-avatar">
            <img src="{{ $avatar }}" alt="{{ $displayName }}" class="chat-avatar-img">
        </div>

        <div class="min-w-0">
            <div class="fw-semibold text-truncate" style="max-width: 520px;">
                {{ $displayName }}
            </div>

            @if(!$hasActiveSub)
                <small class="opacity-75 d-block">
                    Free Trial • Tegur: {{ $participantsUsed }}/{{ $participantsLimit }} • Reply: {{ $respondersUsed }}/{{ $respondersLimit }}
                    • Baki mesej: {{ $remaining }}/{{ $freeLimit ?? 5 }}
                </small>
            @else
                <small class="opacity-75">Online</small>
            @endif
        </div>
    </div>

    <audio id="chat-sound" src="{{ asset('sounds/new-message.wav') }}" preload="auto"></audio>

    <div class="chat-card mb-3">
        <div id="messages" class="chat-body">
            @foreach($messages as $message)
                @php $fromMe = $message->sender_id === $me->id; @endphp
                <div class="d-flex {{ $fromMe ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="msg {{ $fromMe ? 'me' : 'other' }}">
                        {{ $message->body }}
                        <small>
                            {{ $message->created_at->format('H:i') }}
                            @if($fromMe)
                                • {{ $message->read_at ? 'Dibaca' : 'Dihantar' }}
                            @endif
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <form id="chat-form" action="{{ route('chat.room.send', $conversation->uuid) }}" method="POST">
        @csrf
        <div class="chat-input">
            <textarea id="chat-body"
                      name="body"
                      rows="1"
                      class="form-control"
                      placeholder="Tulis mesej..."></textarea>

            <button class="btn-send-chat" id="chat-send-btn" type="submit">
                ➤
            </button>
        </div>
    </form>
</div>

{{-- ✅ POPUP: SEMAK & IMBANG SALAH LAKU (muncul setiap kali mula chat) --}}
<div id="semakModalOverlay" class="jm-modal-overlay" style="display:none;">
    <div class="jm-modal" role="dialog" aria-modal="true" aria-labelledby="semakModalTitle">
        <h5 class="mb-3" id="semakModalTitle">Semak &amp; Imbang Salah Laku</h5>

        <ul class="jm-modal-list">
            <li>
                Calon diberi ruang melaporkan sebarang salah laku dalam tempoh
                <strong>5 hari</strong> dari perbualan pertama direkodkan.
            </li>
            <li>
                Laporan dinilai secara manual oleh pihak JodohMurni.
            </li>
            <li>
                Tindakan yang boleh diambil:
                <ul class="jm-modal-sublist">
                    <li>Amaran <strong>Kad Merah</strong></li>
                    <li><strong>Penggantungan akaun</strong> 15–30 hari</li>
                    <li><strong>Penamatan akaun</strong></li>
                </ul>
            </li>
        </ul>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" id="btnCloseSemakModal">
                Tutup
            </button>
            <button type="button" class="btn btn-success" id="btnContinueSemakModal">
                Teruskan
            </button>
        </div>
    </div>
</div>

{{-- ✅ POPUP LIMIT --}}
<div id="chatLimitOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9998;"></div>
<div id="chatLimitModal" style="display:none; position:fixed; left:50%; top:50%; transform:translate(-50%,-50%); width:min(420px, 92vw); background:#fff; border-radius:14px; padding:18px; z-index:9999; box-shadow:0 10px 30px rgba(0,0,0,.25);">
    <div id="limitTitle" style="font-weight:700; font-size:18px; margin-bottom:8px;">
        Sila upgrade
    </div>
    <div id="limitDesc" style="font-size:14px; color:#555; margin-bottom:14px;">
        Sila subscribe untuk teruskan.
    </div>
    <div style="display:flex; gap:10px; justify-content:flex-end;">
        <button type="button" id="btnCloseChatLimit" class="btn btn-light">
            Tutup
        </button>
        <a href="{{ route('subscription.index') }}" class="btn btn-warning fw-bold">
            Upgrade
        </a>
    </div>
</div>

@endsection

@push('scripts')
<style>
.jm-modal-overlay{
    position: fixed;
    inset: 0;
    z-index: 9997;
    background: rgba(0,0,0,0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.jm-modal{
    width: min(520px, 92vw);
    background: #fff;
    border-radius: 16px;
    padding: 18px;
    box-shadow: 0 14px 40px rgba(0,0,0,0.28);
}
.jm-modal-list{
    margin: 0;
    padding-left: 1.1rem;
    color: #374151;
    line-height: 1.45;
    font-size: 0.95rem;
}
.jm-modal-list li{ margin-bottom: 10px; }
.jm-modal-sublist{
    margin-top: 8px;
    padding-left: 1.1rem;
}
.jm-modal-sublist li{ margin-bottom: 6px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const conversationKey = @json($conversation->uuid);
    const currentUserId   = @json($me->id);
    const otherUserId     = @json($other->id);
    const otherName       = @json($displayName);

    const messagesEl = document.getElementById('messages');
    const form       = document.getElementById('chat-form');
    const textarea   = document.getElementById('chat-body');
    const sendBtn    = document.getElementById('chat-send-btn');
    const soundEl    = document.getElementById('chat-sound');

    // ===== Semak & Imbang Modal =====
    const semakOverlay = document.getElementById('semakModalOverlay');
    const semakClose   = document.getElementById('btnCloseSemakModal');
    const semakGo      = document.getElementById('btnContinueSemakModal');

    function setChatEnabled(enabled) {
        if (enabled) {
            // jangan enable kalau memang server lock (free limit)
            if (!hasActiveSub && !canSend) return;
            textarea?.removeAttribute('disabled');
            sendBtn?.removeAttribute('disabled');
            return;
        }
        textarea?.setAttribute('disabled', 'disabled');
        sendBtn?.setAttribute('disabled', 'disabled');
    }

    function openSemakModal() {
        if (!semakOverlay) return;
        semakOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setChatEnabled(false);
    }

    function closeSemakModal({ navigateBack = false } = {}) {
        if (!semakOverlay) return;
        semakOverlay.style.display = 'none';
        document.body.style.overflow = '';
        if (navigateBack) {
            window.location.href = @json(route('chat.index'));
            return;
        }
        setChatEnabled(true);
        textarea?.focus?.();
    }

    const overlayEl  = document.getElementById('chatLimitOverlay');
    const modalEl    = document.getElementById('chatLimitModal');
    const closeBtn   = document.getElementById('btnCloseChatLimit');

    const titleEl    = document.getElementById('limitTitle');
    const descEl     = document.getElementById('limitDesc');

    const hasActiveSub = @json((bool)($hasActiveSub ?? false));
    let canSend = @json((bool)($canSend ?? true));
    let remaining = @json((int)($remaining ?? 0));
    const initialBlockedReason = @json($blockedReason ?? null);

    // buka semak modal setiap kali load bilik chat
    openSemakModal();
    semakClose?.addEventListener('click', () => closeSemakModal({ navigateBack: true }));
    semakGo?.addEventListener('click', () => closeSemakModal({ navigateBack: false }));

    function setModalByReason(reason) {
        // reason: message_limit | responders_locked | participants_limit | null
        if (reason === 'message_limit') {
            titleEl.textContent = 'Had mesej percuma telah tamat';
            descEl.textContent  = 'Anda telah capai 5 mesej untuk calon ini. Mesej seterusnya perlukan Upgrade.';
            return;
        }
        if (reason === 'responders_locked') {
            titleEl.textContent = 'Free Trial telah dikunci';
            descEl.textContent  = 'Bila 2 calon dah balas, calon lain akan dikunci. Untuk chat calon lain, sila Upgrade.';
            return;
        }
        if (reason === 'participants_limit') {
            titleEl.textContent = 'Had tegur peserta telah tamat';
            descEl.textContent  = 'Anda sudah menegur 10 peserta. Untuk tegur calon baru, sila Upgrade.';
            return;
        }
        titleEl.textContent = 'Sila Upgrade';
        descEl.textContent  = 'Sila subscribe untuk teruskan.';
    }

    function openLimitModal(reason) {
        setModalByReason(reason || initialBlockedReason);
        if (overlayEl) overlayEl.style.display = 'block';
        if (modalEl) modalEl.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeLimitModal() {
        if (overlayEl) overlayEl.style.display = 'none';
        if (modalEl) modalEl.style.display = 'none';
        document.body.style.overflow = '';
    }

    overlayEl?.addEventListener('click', closeLimitModal);
    closeBtn?.addEventListener('click', closeLimitModal);

    messagesEl.scrollTop = messagesEl.scrollHeight;

    function markAsRead() {
        fetch(@json(route('chat.room.read', $conversation->uuid)), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': @json(csrf_token()),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({})
        }).catch(() => {});
    }
    markAsRead();
    window.addEventListener('focus', markAsRead);

    // iOS unlock sound
    const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
    let soundUnlocked = !isIOS;
    function unlockSoundOnce() {
        if (!soundEl || soundUnlocked) return;
        soundEl.play().then(() => {
            soundUnlocked = true;
            soundEl.pause();
            soundEl.currentTime = 0;
        }).catch(() => {});
    }
    if (isIOS) {
        document.addEventListener('click', unlockSoundOnce, { passive: true });
        document.addEventListener('touchstart', unlockSoundOnce, { passive: true });
    }

    function appendMessage(msg, fromMe) {
        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex ' + (fromMe ? 'justify-content-end' : 'justify-content-start');

        const bubble = document.createElement('div');
        bubble.className = 'msg ' + (fromMe ? 'me' : 'other');
        bubble.appendChild(document.createTextNode(msg.body));

        const small = document.createElement('small');
        small.textContent = (msg.created_at || '') + (fromMe ? ' • Dihantar' : '');
        bubble.appendChild(document.createElement('br'));
        bubble.appendChild(small);

        wrapper.appendChild(bubble);
        messagesEl.appendChild(wrapper);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function lockInput(reason) {
        canSend = false;
        textarea?.setAttribute('disabled', 'disabled');
        textarea?.setAttribute('placeholder', 'Chat dikunci. Sila upgrade.');
        openLimitModal(reason);
    }

    // ✅ kalau dari server dah block (load page)
    if (!hasActiveSub && (!canSend)) {
        textarea?.setAttribute('disabled', 'disabled');
        textarea?.setAttribute('placeholder', 'Chat dikunci. Sila upgrade.');
        sendBtn?.setAttribute('disabled', 'disabled');
        if (initialBlockedReason) {
            // optional auto popup
            // openLimitModal(initialBlockedReason);
        }
    }

    if (form && textarea && window.axios) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!hasActiveSub && !canSend) {
                openLimitModal(initialBlockedReason);
                return;
            }

            const text = textarea.value.trim();
            if (!text) return;

            window.axios.post(form.action, { body: text }, {
                headers: { 'Accept': 'application/json' }
            }).then(function () {
                appendMessage({ body: text, created_at: (new Date()).toLocaleString() }, true);
                textarea.value = '';

                // ✅ update remaining locally (free user only)
                if (!hasActiveSub) {
                    remaining = Math.max(0, remaining - 1);
                    if (remaining <= 0) {
                        lockInput('message_limit');
                    }
                }
            }).catch(function (error) {
                if (error?.response?.status === 403 && error?.response?.data?.blocked) {
                    const reason = error?.response?.data?.reason || 'message_limit';
                    lockInput(reason);
                    return;
                }
                console.error('Gagal hantar mesej:', error);
            });
        });
    }

    // Real-time: listen event message.sent (broadcast selepas mesej disimpan di backend)
    // Had Ahli Lite tetap dikawal di server (sendToRoom); real-time hanya papar mesej.
    function subscribeChatChannel() {
        if (!window.Echo) {
            console.warn('[Chat] Echo not ready, retry in 1s');
            setTimeout(subscribeChatChannel, 1000);
            return;
        }
        const channelName = 'conversation.' + conversationKey;
        const ch = window.Echo.private(channelName);
        if (window.Echo.connector && window.Echo.connector.pusher) {
            const pusherChannel = window.Echo.connector.pusher.channel('private-' + channelName);
            if (pusherChannel) {
                pusherChannel.bind('pusher:subscription_succeeded', function () { console.log('[Chat] Subscribed:', channelName); });
                pusherChannel.bind('pusher:subscription_error', function (e) { console.warn('[Chat] Subscribe failed:', e); });
            }
        }
        ch.listen('.message.sent', (e) => {
                const msg = e.message || {};
                const senderId = msg.sender_id != null ? Number(msg.sender_id) : null;
                // Papar di kiri HANYA bila mesej dari penerima (other user), bukan dari aku
                if (senderId === null || senderId === Number(currentUserId)) return;
                if (senderId !== Number(otherUserId)) return;

                appendMessage({
                    body: msg.body || '',
                    created_at: msg.created_at ? (new Date(msg.created_at)).toLocaleString() : '',
                }, false);

                if (soundEl && soundUnlocked) {
                    soundEl.currentTime = 0;
                    soundEl.play().catch(() => {});
                }

                if (document.hidden && 'Notification' in window && Notification.permission === 'granted') {
                    try {
                        new Notification('Mesej baru dari ' + (msg.sender_name || otherName), {
                            body: msg.body || '',
                        });
                    } catch (err) {}
                } else {
                    markAsRead();
                }
            });
    }
    subscribeChatChannel();
});
</script>
@endpush
