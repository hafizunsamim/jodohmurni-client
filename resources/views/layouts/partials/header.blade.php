
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JodohMurni</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
  <meta name="theme-color" content="#ffffff">

  <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
  <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
  <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('icons/icon-512.png') }}">

  <link rel="icon" type="image/x-icon" href="{{ asset('assets/photos/logo_no-background.png') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/swap.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('assets/css/media_query.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ time() }}">
  @if(request()->routeIs('landing'))
  <link rel="stylesheet" type="text/css" href="{{ asset('css/landing.css') }}?v={{ time() }}">
@endif
  @if(request()->routeIs('onboarding.*') || request()->routeIs('register') || request()->routeIs('login') || request()->routeIs('keahlian.info') || request()->routeIs('affiliate.public') || request()->routeIs('affiliate.external.dashboard'))
  <link rel="stylesheet" type="text/css" href="{{ asset('css/onboarding.css') }}?v={{ time() }}">
  @endif
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">





</head>

<body
  @if(request()->routeIs('onboarding.*')) data-page="onboarding"
  @elseif(request()->routeIs('register')) data-page="register"
  @elseif(request()->routeIs('landing')) data-page="landing"
  @elseif(request()->routeIs('login')) data-page="login"
  @elseif(request()->routeIs('keahlian.info')) data-page="keahlian"
  @elseif(request()->routeIs('affiliate.public') || request()->routeIs('affiliate.external.dashboard')) data-page="affiliate"
  @endif
>
<div class="site_content">

  <!-- Keep everything else the same -->
<header id="top-navbar" class="site_lr-spacer homeHeader-main">
  <!-- Logo -->
  <a href="{{ Auth::check() ? route('dashboard') : route('landing') }}" class="logo-home-main">
    <img class="nav-header" src="{{ asset('assets/photos/logo.jpeg') }}" alt="homeLogo">
    <p style="color:rgb(10, 126, 62);">JodohMurni</p>
  </a>

  <!-- Right group: Language + Burger -->
  <div class="header-controls">
    <!-- Language Dropdown -->
    <div class="lang-dropdown">
      <button class="lang-toggle" id="langToggle" type="button">
        @php
            // Map country code to flag icon
            $countryFlagMap = [
                'MY' => asset('flag/malaysia.png'),
                'ID' => asset('flag/indonesia.png'),
                'SG' => asset('flag/singapore.png'),
                'BN' => asset('flag/brunei.png'),
            ];
            
            // Use the country flag if language is NOT English
            // Session 'landing.locale' is what your controller sets
            $currentLocale = session('landing.locale', 'ms');
            $currentCountry = session('landing.country', 'MY');
            
            if ($currentLocale === 'en') {
        $displayFlag = asset('flag/united-kingdom.png');
    } else {
        // This will now catch both 'ms' and 'id' locales
        $displayFlag = $countryFlagMap[$currentCountry] ?? asset('flag/malaysia.png');
    }
        @endphp
        
        <img src="{{ $displayFlag }}" alt="flag" id="currentFlag">
        <span class="caret">▾</span>
    </button>
    <ul class="lang-menu" id="langMenu">
      <li data-lang="en" data-flag="{{ asset('flag/united-kingdom.png') }}">
          <img src="{{ asset('flag/united-kingdom.png') }}" alt="English">
          <span>English</span>
      </li>
      
      @if($currentCountry === 'ID')
          <li data-lang="id" data-flag="{{ asset('flag/indonesia.png') }}">
              <img src="{{ asset('flag/indonesia.png') }}" alt="ID">
              <span>Bahasa Indonesia</span>
          </li>
      @else
          <li data-lang="ms" data-flag="{{ $countryFlagMap[$currentCountry] ?? asset('flag/malaysia.png') }}">
              <img src="{{ $countryFlagMap[$currentCountry] ?? asset('flag/malaysia.png') }}" alt="BM">
              <span>Bahasa Malaysia</span>
          </li>
      @endif
  </ul>
    </div>

    <!-- Login / Logout (sebelah button tukar bahasa) -->
    @auth
      <form action="{{ route('logout') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn-nav-login">Log Keluar</button>
      </form>
    @else
      <a href="{{ route('login') }}" class="btn-nav-login">
        <span class="btn-nav-login-main">Log Masuk</span>
        <span class="btn-nav-login-hint">(Jika sudah ada akaun)</span>
      </a>
    @endauth

    <!-- Burger Button -->
    <button class="burger-toggle" id="burgerToggle" aria-label="Toggle menu">
      ☰
    </button>

    <!-- NEW: Full-screen Burger Menu Drawer (hidden by default) -->
    <div class="burger-drawer" id="burgerDrawer">
      <div class="drawer-header">
        <span class="logo-text">JodohMurni</span>
        <button class="close-drawer" id="closeDrawer">×</button>
      </div>
    
      <div class="drawer-links">
        <a href="#" class="menu-item">Tentang Kami</a>
        <a href="{{ route('affiliate.public') }}" class="menu-item">Affiliate</a>
        @auth
          <a href="{{ route('subscription.index') }}" class="menu-item">Subscription</a>
        @endauth
        <div class="dropdown-wrapper">
          <div class="dropdown-item" id="kursusToggle">
            <span>Perkhidmatan Yang Ditawarkan</span>
            <span class="dropdown-arrow">▼</span>
          </div>
          <div class="dropdown-content" id="kursusContent">
            <a href="#">Kursus & Seminar</a>
            <a href="#">Ebook</a>
            <a href="#">Konsultasi</a>
            <a href="#">Set Temu Janji</a>
          </div>
        </div>
        <a href="{{ route('keahlian.info') }}" class="menu-item">Keahlian</a>
      </div>
    </div>
  </div>
</header>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
  const burgerToggle = document.getElementById('burgerToggle');
  const closeDrawer = document.getElementById('closeDrawer');
  const burgerDrawer = document.getElementById('burgerDrawer');

   /* =====================
     DRAWER OPEN / CLOSE
  ====================== */
  burgerToggle?.addEventListener('click', function (e) {
    e.stopPropagation();
    burgerDrawer.classList.add('show');
    document.body.style.overflow = 'hidden';
  });

  closeDrawer?.addEventListener('click', function () {
    burgerDrawer.classList.remove('show');
    document.body.style.overflow = '';
  });

  /* CLICK OUTSIDE CLOSE */
  document.addEventListener('click', function (e) {
    if (
      burgerDrawer.classList.contains('show') &&
      !burgerDrawer.contains(e.target) &&
      e.target !== burgerToggle
    ) {
      burgerDrawer.classList.remove('show');
      document.body.style.overflow = '';
    }
  });

  /* =====================
     DROPDOWN TOGGLE
  ====================== */
  const kursusToggle = document.getElementById('kursusToggle');
  const kursusContent = document.getElementById('kursusContent');

  kursusToggle?.addEventListener('click', function (e) {
    e.stopPropagation(); // 🔥 VERY IMPORTANT
    kursusContent.classList.toggle('open');
    kursusToggle.classList.toggle('open');
  });

  // Optional: Theme toggle
  const themeToggle = document.getElementById('themeToggle');
  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      document.body.classList.toggle('dark-mode');
    });
  }

  // Optional: Mobile language toggle
  const mobileLangToggle = document.getElementById('mobileLangToggle');
  const langToggle = document.getElementById('langToggle');
  if (mobileLangToggle && langToggle) {
    mobileLangToggle.addEventListener('click', function () {
      langToggle.click();
    });
  }
});
  </script>

  @if (!request()->routeIs('landing')
      && !request()->routeIs('keahlian.info')
      && !request()->routeIs('membership.education')
      && !request()->routeIs('affiliate.public')
      && !request()->routeIs('affiliate.external.dashboard'))
  <div class="container mb-5" style="margin-top: 20px;">
  @endif
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- ✅ main.js load once (BUANG koma) --}}
    <script src="{{ asset('assets/js/main.js') }}?v={{ time() }}"></script>

