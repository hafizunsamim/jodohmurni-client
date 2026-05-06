<link rel="stylesheet" type="text/css" href="{{ asset('css/likes.css') }}">
@extends('layouts.app')

@section('content')
<div class="container main-likes-wrapper">
    <div class="header-section">
        <h3 class="page-title" data-translate="likes_title">Aktiviti Swipe</h3>

        <div class="filter-pills">
            <a href="{{ route('swipes.index', ['type' => 'all']) }}"
               class="pill-btn {{ $filter==='all' ? 'active' : '' }}">
                <span data-translate="likes_filter_all">Semua</span>
            </a>
            <a href="{{ route('swipes.index', ['type' => 'like']) }}"
               class="pill-btn {{ $filter==='like' ? 'active' : '' }}">
                <span data-translate="likes_filter_like">Like</span>
            </a>
            <a href="{{ route('swipes.index', ['type' => 'dislike']) }}"
               class="pill-btn {{ $filter==='dislike' ? 'active' : '' }}">
                <span data-translate="likes_filter_dislike">Dislike</span>
            </a>
        </div>
    </div>

    @if ($swipes->isEmpty())
    <div class="empty-state">
        <div class="empty-icon-wrapper">
            <span class="heart-icon">ðŸ’”</span>
        </div>
        <h5 data-translate="likes_empty_title">Tiada aktiviti lagi</h5>
        <p data-translate="likes_empty_desc">Mula swipe untuk lihat sejarah Like & Dislike anda.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary mt-3" data-translate="likes_start_swipe">Mula Swipe</a>
    </div>

    @else
        <div class="swipe-grid">
            @foreach ($swipes as $index => $s)
                @php
                    $u = $targets[$s->target_user_id] ?? null;
                    $photo = $u && $u->photo_1 ? asset($u->photo_1) : asset('images/default.jpg');

                    // ✅ Nama ikut subscription (macam dashboard)
                    $displayName = 'User tidak ditemui';
                    if ($u) {
                        if (!empty($hasActiveSub)) {
                            $displayName = trim(($u->nickname ?? '') . ' ' . ($u->name ?? ''));
                            if ($displayName === '') $displayName = $u->name ?? 'Calon';
                        } else {
                            $displayName = $u->public_id ?: 'Calon';
                        }
                    }
                @endphp

                <div class="swipe-card" style="animation-delay: {{ $index * 0.05 }}s">
                    <div class="photo-wrapper">
                        <div class="swipe-photo" style="background-image:url('{{ $photo }}');"></div>
                        <div class="photo-overlay"></div>
                        @if ($s->action === 'like')
                            <span class="badge-swipe badge-like">
                                <i class="fas fa-heart"></i> <span data-translate="likes_badge_like">LIKE</span>
                            </span>
                        @else
                            <span class="badge-swipe badge-dislike">
                                <i class="fas fa-times"></i> <span data-translate="likes_badge_dislike">DISLIKE</span>
                            </span>
                        @endif
                    </div>

                    <div class="swipe-body">
                        <div class="swipe-header">
                            {{-- ✅ Hanya tukar output nama (design kekal) --}}
                            <span class="swipe-name">{{ $displayName }}</span>
                        </div>

                        <div class="swipe-time">
                            <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($s->created_at)->diffForHumans() }}
                        </div>

                        @if($u)
                            <div class="swipe-actions">
                                <a href="{{ route('candidates.show', $u->id) }}" class="btn-action view">
                                    <span data-translate="likes_view_profile">Lihat Profil</span>
                                </a>
                                <a href="{{ route('chat.openWithUser', $u->id) }}" class="btn-action chat">
                                    <i class="fas fa-comment"></i> <span data-translate="likes_chat">Chat</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $swipes->withQueryString()->links() }}
        </div>
    @endif
</div>
@include('partials.footer-nav', ['active' => 'likes'])

@endsection
