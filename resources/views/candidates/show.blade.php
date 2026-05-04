@extends('layouts.app')

@php
    function maskEmail(?string $email): string {
        if (!$email || !str_contains($email, '@')) return '-';
        [, $domain] = explode('@', $email, 2);
        $domain = trim($domain);
        if ($domain === '') return '******@-';
        return '******@' . $domain;
    }

    function maskPhone(?string $phone): string {
        if (!$phone) return '-';
        $digits = preg_replace('/\D+/', '', $phone);
        if (!$digits) return '-';
        $last3 = substr($digits, -3);
        if (strlen($digits) <= 3) return $last3;
        return '*******' . $last3;
    }

    $me = auth()->user();
    $isSelf = $me && $user && ($me->id === $user->id);

    // ✅ HIDE nama sebenar kalau tak subscribe
    $maskedRealName = !empty($hasActiveSub) ? ($user->name ?? '-') : '******';
    $maskedNickName = !empty($hasActiveSub) ? ($user->nickname ?? '-') : '******';
@endphp

@section('content')
<section class="section-main mb-4">

    {{-- ✅ ACTION BUTTONS --}}
    <div class="d-flex gap-2 flex-wrap mb-3">
        <a href="{{ url('/dashboard') }}" class="btn text-white" style="background:#2e7d32;">
            ← Back to Dashboard
        </a>

        @if(!$isSelf)
            <a href="{{ route('chat.openWithUser', $user->id) }}"
               class="btn text-white"
               style="background:#1565c0;">
                💬 Sila Chat
            </a>
        @else
            <button class="btn text-white" style="background:#90a4ae;" disabled>
                💬 Sila Chat
            </button>
        @endif
    </div>

    <img class="single-d-main-img {{ $canViewClearImage ? '' : 'profile-img-blur' }}" src="{{ $mainPhoto }}" alt="{{ e($displayName) }}" style="{{ $canViewClearImage ? '' : 'filter: blur(20px); pointer-events: none;' }}">

    <h2 class="single-profile-nam">{{ $displayName }}</h2>

    <div class="romio-girl-data-main single-isabell">
        @if($age)
            <p><img src="{{ asset('assets/images/svg/gender-icon2.svg') }}" alt="age"> {{ $age }} yr</p>
        @endif

        <p><img src="{{ asset('assets/images/svg/home-flo.svg') }}" alt="country"> {{ strtoupper($user->country ?? '-') }}</p>

        @if(!is_null($distanceKm))
            <p><img src="{{ asset('assets/images/svg/location-home.svg') }}" alt="location"> {{ round($distanceKm, 1) }} km</p>
        @endif
    </div>

    {{-- MINAT --}}
    @if(!empty($interests))
        <div class="about-box">
            <p class="issAbout-me">Minat</p>
            <div class="inte-mian">
                @foreach(array_slice($interests, 0, 10) as $interest)
                    <p class="int-box">{{ $interest }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- GAMBAR --}}
    @if(!empty($photos))
        <div class="about-box">
            <p class="issAbout-me">Gambar</p>
            <div class="masonry-grid">
                @foreach($photos as $photo)
                    <img src="{{ asset($photo) }}" alt="Gambar profil" class="{{ $canViewClearImage ? '' : 'profile-img-blur' }}" style="{{ $canViewClearImage ? '' : 'filter: blur(20px);' }}">
                @endforeach
            </div>
        </div>
    @endif

    {{-- USER TABLE (SEMUA INFO) --}}
    <div class="about-box">
        <p class="issAbout-me">Maklumat User</p>
        <div class="issAbout">
            <p><strong>Nama:</strong> {{ $maskedRealName }}</p>
            <p><strong>Nickname:</strong> {{ $maskedNickName }}</p>

            <p><strong>Email:</strong> {{ maskEmail($user->email) }}</p>
            <p><strong>Phone:</strong> {{ maskPhone($user->phone) }}</p>

            <hr>

            <p><strong>Gender:</strong> {{ $user->gender ?? '-' }}</p>
            <p><strong>Marital:</strong> {{ $user->marital_status_label  ?? '-' }}</p>

            <p><strong>Country:</strong> {{ $user->country ?? '-' }}</p>
            <p><strong>State:</strong> {{ $user->state ?? '-' }}</p>
            <p><strong>District:</strong> {{ $user->district ?? '-' }}</p>

            <hr>

            <p><strong>Occupation:</strong> {{ $user->occupation_type ?? '-' }}</p>
            <p><strong>Education:</strong> {{ $user->education_level ?? '-' }}</p>
            <p><strong>Hobbies:</strong> {{ $user->hobbies ?? '-' }}</p>
            <p><strong>Social:</strong> {{ $user->social_activities ?? '-' }}</p>
        </div>
    </div>

    {{-- USER_PREFERENCES TABLE --}}
    <div class="about-box">
        <p class="issAbout-me">User Preference</p>

        @if($user->preferences)
            <div class="issAbout">
                <p><strong>Age Min:</strong> {{ $user->preferences->age_min ?? '-' }}</p>
                <p><strong>Age Max:</strong> {{ $user->preferences->age_max ?? '-' }}</p>
                <p><strong>Radius:</strong> {{ $user->preferences->location_radius ?? '-' }}</p>
                <p><strong>Relocate:</strong>
                    {{ is_null($user->preferences->willing_to_relocate) ? '-' : ($user->preferences->willing_to_relocate ? 'Yes' : 'No') }}
                </p>
            </div>
        @else
            <p class="issAbout">User ini belum set preference.</p>
        @endif
    </div>

</section>

@if(config('analytics.google_measurement_id'))
@push('scripts')
<script>
(function () {
    if (typeof gtag !== 'function') return;
    gtag('event', 'view_profile', { profile_type: 'candidate' });
})();
</script>
@endpush
@endif

@endsection
