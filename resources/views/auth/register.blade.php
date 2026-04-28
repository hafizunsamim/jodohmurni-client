<link href="{{ asset('css/register.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@extends('layouts.app')

@section('content')


    <div class="login-wrapper">
        <div class="login-card">
            <h1 class="text-center mb-3" style="font-weight: 600; color: #2d3748;">Daftar Akaun</h1>
            <p class="text-center mb-4" style="color: #5a7b70; font-size: 0.95rem;">Sila lengkapkan maklumat pendaftaran.</p>

            {{-- Ringkasan Onboarding --}}
            @if (!empty($onboarding))
                <div class="alert alert-info p-3 mb-4 rounded-3"
                    style="background: rgba(232, 245, 240, 0.7); border: 1px solid #b2d3c2;">
                    <h6 class="mb-2" style="color: #00966d;">Ringkasan Pilihan JodohMurni</h6>
                    <ul class="mb-0" style="font-size: 0.875rem; padding-left: 18px; color: #2d3748;">
                        @if (isset($onboarding['country']))
                            <li>Negara: {{ ucfirst($onboarding['country']) }}</li>
                        @endif
                        @if (isset($onboarding['state']))
                            <li>Negeri: {{ $onboarding['state'] }}</li>
                        @endif
                        @if (isset($onboarding['district']))
                            <li>Daerah: {{ $onboarding['district'] }}</li>
                        @endif
                        @if (isset($onboarding['gender']))
                            <li>Jantina: {{ $onboarding['gender'] === 'male' ? 'Lelaki' : 'Wanita' }}</li>
                        @endif
                        @if (isset($onboarding['path']))
                            <li>Laluan:
                                @switch($onboarding['path'])
                                    @case('monogami')
                                        Lelaki – Monogami
                                    @break

                                    @case('poligami')
                                        Lelaki – Poligami (Situasi {{ $onboarding['poligami_situation'] ?? '-' }})
                                    @break

                                    @case('wanita_monogami')
                                        Wanita – Monogami
                                    @break

                                    @case('wanita_poligami')
                                        Wanita – Poligami (Model {{ $onboarding['poligami_level'] ?? '-' }})
                                    @break

                                    @case('wanita_terbuka')
                                        Wanita – Terbuka
                                    @break
                                @endswitch
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger p-3 mb-4 rounded-3"
                    style="background: rgba(255, 240, 240, 0.9); border: 1px solid #f2c7c7;">
                    <ul class="mb-0" style="font-size: 0.875rem; padding-left: 18px; color: #e53e3e;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf

                <!-- Nama Penuh -->
                <div class="form-floating mb-3">
                    <input type="text" id="name" name="name" class="form-control" placeholder=" "
                        value="{{ old('name') }}" required autocomplete="name">
                    <label for="name" class="sign-text-nam">NAMA PENUH</label>
                </div>

                <!-- Nama Gelaran -->
                <div class="form-floating mb-3">
                    <input type="text" id="nickname" name="nickname" class="form-control" placeholder=" "
                        value="{{ old('nickname') }}" autocomplete="nickname">
                    <label for="nickname">NAMA GELARAN</label>
                </div>

                <!-- No. Telefon -->
                <div class="mb-3">
                    <label class="form-label" style="color: #5a7b70; font-size: 0.875rem; margin-bottom: 0.375rem;">NO.
                        TELEFON</label>
                    <div class="input-group">
                        <select name="phone_country" class="form-select"
                            style="max-width: 120px; border-radius: 12px 0 0 12px !important;" id="phoneCountry">
                            <option value="MY" {{ old('phone_country', 'MY') == 'MY' ? 'selected' : '' }}>🇲🇾 +60
                            </option>
                            <option value="ID" {{ old('phone_country') == 'ID' ? 'selected' : '' }}>🇮🇩 +62</option>
                            <option value="SG" {{ old('phone_country') == 'SG' ? 'selected' : '' }}>🇸🇬 +65</option>
                            <option value="BN" {{ old('phone_country') == 'BN' ? 'selected' : '' }}>🇧🇳 +673</option>
                        </select>
                        <input type="text" name="phone" id="phone" class="form-control" required
                            placeholder="Contoh: 199009014">

                    </div>
                </div>

                <!-- Emel -->
                <div class="form-floating mb-3">
                    <input type="email" id="email" name="email" class="form-control" placeholder=" "
                        value="{{ old('email') }}" required autocomplete="email">
                    <label for="email">E-MAIL</label>
                </div>

                <!-- Kata Laluan -->
                <div class="form-floating mb-3 position-relative">
                    <input type="password" id="password" name="password" class="form-control" placeholder=" ">
                    <label for="password">KATA LALUAN</label>
                    <button type="button" class="btn toggle-eye position-absolute end-0 top-50 translate-middle-y pe-3"
                        onclick="togglePasswordVisibility('password')" style="background: none; border: none;">
                        <i id="password-icon" class="bi bi-eye" style="font-size: 1.2rem;"></i>
                    </button>
                </div>

                <!-- Sahkan Kata Laluan -->
                <div class="form-floating mb-4 position-relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                        placeholder=" ">
                    <label for="password_confirmation">SAHKAN KATA LALUAN</label>
                    <button type="button" class="btn toggle-eye position-absolute end-0 top-50 translate-middle-y pe-3"
                        onclick="togglePasswordVisibility('password_confirmation')" style="background: none; border: none;">
                        <i id="password_confirmation-icon" class="bi bi-eye" style="font-size: 1.2rem;"></i>
                    </button>
                </div>

                <!-- Terms & Privacy Consent -->
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required checked>
                    <label class="form-check-label" for="terms">
                        Saya bersetuju dengan
                        <a href="{{ route('terms') }}" target="_blank">Terma Penggunaan</a>
                        &amp;
                        <a href="{{ route('privacy') }}" target="_blank">Dasar Privasi</a>
                    </label>
                </div>


                <!-- Submit Button -->
                <button type="submit" class="btn w-100 py-2 login-btn">
                    Daftar & Masuk
                </button>

            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePasswordVisibility(fieldId) {
                const input = document.getElementById(fieldId);
                const icon = document.getElementById(fieldId + '-icon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }

            // Phone number validation (no leading 0)
            const phoneInput = document.getElementById('phone');

            phoneInput.addEventListener('input', function() {
                // Allow digits only
                this.value = this.value.replace(/\D/g, '');

                // Remove leading 0
                if (this.value.startsWith('0')) {
                    this.value = this.value.substring(1);
                }
            });

            // Extra protection: block "0" key if input is empty
            phoneInput.addEventListener('keydown', function(e) {
                if (this.value.length === 0 && e.key === '0') {
                    e.preventDefault();
                }
            });

            function togglePasswordVisibility(fieldId) {
                const input = document.getElementById(fieldId);
                const icon = document.getElementById(fieldId + '-icon');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        </script>
    @endpush
@endsection
