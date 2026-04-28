  <link rel="stylesheet" href="{{ asset('css/search.css') }}">
@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 900px;">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0 fw-bold">
            🔍 Cari Calon
        </h4>
    </div>

    {{-- ✅ Subscription gate (Option B): boleh buka page, tapi search locked --}}
    @if(empty($hasActiveSub))
        <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
            <div>
                Untuk guna fungsi <b>Carian Calon</b>, sila aktifkan subscription terlebih dahulu.
            </div>
            <a href="{{ route('subscription.index') }}" class="btn btn-sm btn-success">
                Pergi Subscription
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
                       placeholder="Cari nama atau email calon..."
                       value="{{ old('q', $term) }}"
                       @if(empty($hasActiveSub)) disabled @endif>

                <button class="btn btn-success search-btn" type="submit"
                        @if(empty($hasActiveSub)) disabled @endif>
                    Cari
                </button>
            </div>

            <small class="text-muted d-block mt-2">
                @if(empty($hasActiveSub))
                    *Carian dikunci. Aktifkan subscription untuk cari calon.
                @else
                    Contoh: <strong>Aisyah</strong>, <strong>ahmad@gmail.com</strong>
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
                    Aktifkan subscription untuk mula mencari calon.
                @else
                    Masukkan nama atau email untuk mencari calon yang sesuai.
                @endif
            </p>
        </div>
    @else
        <h6 class="mb-3">
            Keputusan untuk:
            <span class="fw-semibold text-success">"{{ $term }}"</span>
        </h6>

        @if($users->isEmpty())
            <div class="empty-state">
                <h6>😕 Tiada padanan</h6>
                <p>Cuba kata kunci lain.</p>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($users as $user)
                    @php
                        $photo = $user->photo_1
                            ? asset($user->photo_1)
                            : asset('images/default.jpg');

                        // ✅ nama ikut subscription: belum subscribe -> public_id
                        $displayName = $user->public_id ? ('ID ' . $user->public_id) : 'Calon';

                        if (!empty($hasActiveSub)) {
                            $displayName = trim(($user->nickname ?? '') . ' ' . ($user->name ?? ''));
                            if ($displayName === '') $displayName = $user->name ?? 'Calon';
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
                                        <span class="text-muted">Email dikunci</span>
                                    @endif
                                </div>

                                <span class="badge badge-match mt-1 d-inline-block">
                                    Padanan Berpotensi
                                </span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            @if(!empty($hasActiveSub))
                                <a href="{{ route('candidates.show', $user->id) }}"
                                   class="btn btn-sm btn-outline-secondary action-btn">
                                    Profil
                                </a>

                                <a href="{{ route('chat.openWithUser', $user->id) }}"
                                   class="btn btn-sm btn-success action-btn">
                                    💬 Chat
                                </a>
                            @else
                                <a href="{{ route('subscription.index') }}"
                                   class="btn btn-sm btn-success action-btn">
                                    Unlock
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
