  <link rel="stylesheet" href="{{ asset('css/search.css') }}">
@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 900px;">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0 fw-bold" data-translate="search_title">🔍 Cari Calon</h4>
    </div>

    {{-- ✅ Subscription gate (Option B): boleh buka page, tapi search locked --}}
    @if(empty($hasActiveSub))
        <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
            <div>
                <span data-translate="search_gate_1">Untuk guna fungsi</span> <b data-translate="search_gate_feature">Carian Calon</b>,
                <span data-translate="search_gate_2">sila aktifkan subscription terlebih dahulu.</span>
            </div>
            <a href="{{ route('subscription.index') }}" class="btn btn-sm btn-success">
                <span data-translate="search_go_subscription">Pergi Subscription</span>
            </a>
        </div>
    @endif

    {{-- Search Box --}}
    <div class="search-card mb-4">
        <form method="GET" action="{{ route('search.index') }}">
            <div class="input-group">
                <input type="text"
                       name="q"
                       class="form-control search-input"
                       placeholder="Cari nama atau email calon..." data-translate-placeholder="search_placeholder"
                       value="{{ old('q', $term) }}"
                       @if(empty($hasActiveSub)) disabled @endif>

                <button class="btn btn-success search-btn" type="submit"
                        @if(empty($hasActiveSub)) disabled @endif>
                    <span data-translate="search_btn">Cari</span>
                </button>
            </div>

            <small class="text-muted d-block mt-2">
                @if(empty($hasActiveSub))
                    <span data-translate="search_locked_note">*Carian dikunci. Aktifkan subscription untuk cari calon.</span>
                @else
                    <span data-translate="search_example">Contoh</span>: <strong>Aisyah</strong>, <strong>ahmad@gmail.com</strong>
                @endif
            </small>
        </form>
    </div>

    {{-- Results --}}
    @if($term === '')
        <div class="empty-state">
            <img class="search-content" src="{{ asset('assets/photos/search_flat.jpg') }}">
            <p>
                @if(empty($hasActiveSub))
                    <span data-translate="search_empty_need_sub">Aktifkan subscription untuk mula mencari calon.</span>
                @else
                    <span data-translate="search_empty_prompt">Masukkan nama atau email untuk mencari calon yang sesuai.</span>
                @endif
            </p>
        </div>
    @else
        <h6 class="mb-3">
            <span data-translate="search_results_for">Keputusan untuk</span>:
            <span class="fw-semibold text-success">"{{ $term }}"</span>
        </h6>

        @if($users->isEmpty())
            <div class="empty-state">
                <h6 data-translate="search_no_match_title">😕 Tiada padanan</h6>
                <p data-translate="search_no_match_desc">Cuba kata kunci lain.</p>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($users as $user)
                    @php
                        $photo = $user->photo_1
                            ? asset($user->photo_1)
                            : asset('images/default.jpg');

                        // ✅ nama ikut subscription: belum subscribe -> public_id
                        $displayName = $user->public_id ? ('ID ' . $user->public_id) : \App\Support\JmI18n::t('search_candidate');

                        if (!empty($hasActiveSub)) {
                            $displayName = trim(($user->nickname ?? '') . ' ' . ($user->name ?? ''));
                            if ($displayName === '') $displayName = $user->name ?? \App\Support\JmI18n::t('search_candidate');
                        }
                    @endphp

                    <div class="user-card p-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $photo }}" class="avatar" alt="{{ e($displayName) }}">

                            <div>
                                <div class="fw-semibold">
                                    {{ $displayName }}

                                    @if(!empty($hasActiveSub) && !empty($user->nickname))
                                        <small class="text-muted">({{ $user->nickname }})</small>
                                    @endif
                                </div>

                                <div class="small text-muted">
                                    @if(!empty($hasActiveSub))
                                        {{ $user->email }}
                                    @else
                                        <span class="text-muted" data-translate="search_email_locked">Email dikunci</span>
                                    @endif
                                </div>

                                <span class="badge badge-match mt-1 d-inline-block">
                                    <span data-translate="search_potential_match">Padanan Berpotensi</span>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            @if(!empty($hasActiveSub))
                                <a href="{{ route('candidates.show', $user->id) }}"
                                   class="btn btn-sm btn-outline-secondary action-btn">
                                    <span data-translate="search_profile">Profil</span>
                                </a>

                                <a href="{{ route('chat.openWithUser', $user->id) }}"
                                   class="btn btn-sm btn-success action-btn">
                                    💬 <span data-translate="search_chat">Chat</span>
                                </a>
                            @else
                                <a href="{{ route('subscription.index') }}"
                                   class="btn btn-sm btn-success action-btn">
                                    <span data-translate="search_unlock">Unlock</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>

@include('partials.footer-nav', ['active' => 'search'])

@endsection
