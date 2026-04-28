<footer class="footer-nav">
    <a href="{{ route('dashboard') }}" class="nav-item {{ ($active ?? '') === 'dashboard' ? 'active' : '' }}">
        <img src="{{ asset('assets/images/svg/home.svg') }}" alt="Home">
        @if(($active ?? '') === 'dashboard')
            <span class="indicator"></span>
        @endif
    </a>

    <a href="{{ route('search.index') }}" class="nav-item {{ ($active ?? '') === 'search' ? 'active' : '' }}">
        <img src="{{ asset('assets/photos/magnifying.png') }}" alt="discover">
        @if(($active ?? '') === 'search')
            <span class="indicator"></span>
        @endif
    </a>

    <a href="{{ route('swipes.index') }}" class="nav-item {{ ($active ?? '') === 'likes' ? 'active' : '' }}">
        <img src="{{ asset('assets/images/svg/favorate.svg') }}" alt="likes">
        @if(($active ?? '') === 'likes')
            <span class="indicator"></span>
        @endif
    </a>

    <a href="{{ route('chat.index') }}" class="nav-item {{ ($active ?? '') === 'chat' ? 'active' : '' }}">
        <img src="{{ asset('assets/images/svg/message.svg') }}" alt="messages">
        @if(($active ?? '') === 'chat')
            <span class="indicator"></span>
        @endif
    </a>

    <a href="{{ route('profile.show') }}" class="nav-item {{ ($active ?? '') === 'profile' ? 'active' : '' }}">
        <img src="{{ asset('assets/images/svg/account.svg') }}" alt="account">
        @if(($active ?? '') === 'profile')
            <span class="indicator"></span>
        @endif
    </a>
</footer>
