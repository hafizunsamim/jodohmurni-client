<link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}">

@extends('layouts.app')

@section('content')
    <div class="login-wrapper d-flex justify-content-center align-items-center">
        <div class="login-card shadow-lg">
            

            <h3 class="text-center mb-4 fw-bold">Selamat Datang</h3>
            <div class="login-video-wrap text-center mb-3">
                <video class="login-video" src="{{ asset('assets/videos/login-jodoh-murni.mp4') }}" autoplay muted playsinline></video>
            </div>
            <p class="text-center text-muted mb-4">Sila log masuk untuk meneruskan</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com"
                        value="{{ old('email') }}" required>
                    <label for="email">E-MAIL</label>
                </div>

                <!-- Password -->
                <div class="form-floating mb-3 position-relative">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password"
                        required>
                    <label for="password">Kata Laluan</label>

                    <!-- Eye icon -->
                    <span class="toggle-eye" onclick="togglePassword()">
                        <i id="password-icon" class="bi bi-eye"></i>
                    </span>
                </div>

                <!-- Remember Me -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-decoration-none small">
                            Lupa kata laluan?
                        </a>
                    @endif
                </div>

                <!-- Button -->
                <button class="btn btn-primary w-100 py-2 login-btn">
                    Login
                </button>

                @if (Route::has('onboarding.state'))
                    <p class="text-center mt-3 mb-0 small text-muted">
                        Belum ada akaun?
                        <a href="{{ route('onboarding.state') }}" class="text-decoration-none fw-medium">Mula taaruf</a>
                    </p>
                @endif
            </form>

        </div>
    </div>
    <script>

        
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('password-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Add loading state on form submit
        document.querySelector('form').addEventListener('submit', function() {
            const button = this.querySelector('.login-btn');
            button.classList.add('loading');
            button.disabled = true;
            // Optional: Re-enable after 3s if no redirect (for demo)
            setTimeout(() => {
                button.classList.remove('loading');
                button.disabled = false;
            }, 3000);
        });
    </script>
@endsection
