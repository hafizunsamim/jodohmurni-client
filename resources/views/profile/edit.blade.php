  <link rel="stylesheet" href="{{ asset('css/profile.css') }}">

@extends('layouts.app')

@section('content')
<section class="section-main mb-4">
@php
    $photos = [
        1 => $user->photo_1,
        2 => $user->photo_2,
        3 => $user->photo_3,
        4 => $user->photo_4,
    ];
@endphp

    @if ($errors->any())
      <div class="alert alert-danger">
        <div class="fw-bold mb-1" data-translate="profile_errors_title">Ada error:</div>
        <ul class="mb-0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="single-profile-nam mb-0" data-translate="profile_edit">Edit Profile</h2>
        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary btn-sm" data-translate="profile_back">Back</a>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="about-box">
            <p class="issAbout-me" data-translate="profile_basic_info">Maklumat Asas</p>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" data-translate="profile_name">Nama</label>
                    <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label" data-translate="profile_nickname">Nickname</label>
                    <input class="form-control" name="nickname" value="{{ old('nickname', $user->nickname) }}">
                </div>

                <div class="col-12">
                    <label class="form-label" data-translate="profile_email">Email</label>
                    <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label" data-translate="profile_phone">Telefon</label>
                    <input class="form-control" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>

                <div class="col-6">
                    <label class="form-label" data-translate="profile_gender">Jantina</label>
                    <select class="form-select" name="gender">
                        @php $g = old('gender', $user->gender); @endphp
                        <option value="">—</option>
                        <option value="male" {{ $g==='male' ? 'selected' : '' }} data-translate="profile_gender_male">Lelaki</option>
                        <option value="female" {{ $g==='female' ? 'selected' : '' }} data-translate="profile_gender_female">Perempuan</option>
                    </select>
                </div>

                <div class="col-6">
                    <label class="form-label" data-translate="profile_status">Status</label>
                    @php $m = old('marital_status', $user->marital_status); @endphp
                    <input class="form-control" name="marital_status" value="{{ $m }}">
                </div>

                <div class="col-12">
                    <label class="form-label" data-translate="profile_dob">Tarikh Lahir</label>
                    <input class="form-control" type="date" name="date_of_birth"
                           value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d')) }}">
                </div>

                <div class="col-12">
                    <label class="form-label" data-translate="profile_job">Pekerjaan</label>
                    <input class="form-control" name="occupation_type" value="{{ old('occupation_type', $user->occupation_type) }}">
                </div>

                <div class="col-12">
                    <label class="form-label" data-translate="profile_education">Tahap Pendidikan</label>
                    <input class="form-control" name="education_level" value="{{ old('education_level', $user->education_level) }}">
                </div>
            </div>
        </div>

        <div class="about-box">
            <p class="issAbout-me" data-translate="profile_location">Lokasi</p>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" data-translate="profile_country">Negara</label>
                    <input class="form-control" name="country" value="{{ old('country', $user->country) }}">
                </div>
                <div class="col-6">
                    <label class="form-label" data-translate="profile_state">State/Region</label>
                    <input class="form-control" name="state" value="{{ old('state', $user->state) }}">
                </div>
                <div class="col-6">
                    <label class="form-label" data-translate="profile_district">District</label>
                    <input class="form-control" name="district" value="{{ old('district', $user->district) }}">
                </div>
            </div>
        </div>

        <div class="about-box">
            <p class="issAbout-me" data-translate="profile_interests">Minat</p>
            <div class="text-muted small mb-2">
                <span data-translate="profile_interests_help_prefix">Isi dalam format comma. Contoh:</span> <b data-translate="profile_interests_help_example">Memasak, Hiking, Membaca</b>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label" data-translate="profile_hobbies">Hobbies</label>
                    <textarea class="form-control" name="hobbies" rows="2">{{ old('hobbies', $user->hobbies) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label" data-translate="profile_social_activities">Social Activities</label>
                    <textarea class="form-control" name="social_activities" rows="2">{{ old('social_activities', $user->social_activities) }}</textarea>
                </div>
            </div>
        </div>

        <div class="about-box">
            <p class="issAbout-me" data-translate="profile_profile_photos">Gambar Profile</p>
            <div class="text-muted small mb-3">
                <span data-translate="profile_photos_help">Max 4MB setiap gambar. Kalau nak buang gambar lama, tick “Remove”.</span>
            </div>

            <div class="row g-3">
                @foreach([1,2,3,4] as $i)
                    <div class="col-12">
                        <div class="p-3 border rounded-3 bg-white">
                            <div class="fw-bold mb-2"><span data-translate="profile_photo">Photo</span> {{ $i }}</div>

                            <div class="d-flex gap-3 align-items-center flex-wrap">
                                <div style="width:120px;height:120px;border-radius:12px;overflow:hidden;background:#f2f2f2;">
                                    @if(!empty($photos[$i]))
                                        <img src="{{ asset($photos[$i]) }}" alt="photo{{ $i }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <img src="{{ asset('images/default.jpg') }}" alt="default" style="width:100%;height:100%;object-fit:cover;">
                                    @endif
                                </div>

                                <div class="flex-grow-1" style="min-width:240px;">
                                    <input class="form-control" type="file" name="photo_{{ $i }}" accept="image/*">

                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="remove_photo_{{ $i }}" name="remove_photo_{{ $i }}" value="1">
                                        <label class="form-check-label" for="remove_photo_{{ $i }}">
                                            <span data-translate="profile_remove_photo">Remove photo</span> {{ $i }}
                                        </label>
                                    </div>

                                    @if(!empty($photos[$i]))
                                        <div class="text-muted small mt-2">
                                            <span data-translate="profile_current">Current:</span> {{ $photos[$i] }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="about-box">
            <p class="issAbout-me" data-translate="profile_prefs_optional">Preferences (Optional)</p>

            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label" data-translate="profile_min_age">Min Age</label>
                    <input type="number" class="form-control" name="pref_min_age"
                           value="{{ old('pref_min_age', $prefs->min_age ?? '') }}" min="18" max="99">
                </div>
                <div class="col-6">
                    <label class="form-label" data-translate="profile_max_age">Max Age</label>
                    <input type="number" class="form-control" name="pref_max_age"
                           value="{{ old('pref_max_age', $prefs->max_age ?? '') }}" min="18" max="99">
                </div>
            </div>

            <div class="text-muted small mt-2">
            </div>
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-warning w-100" type="submit">
                <span data-translate="profile_save_changes">Simpan Perubahan</span>
            </button>
        </div>
    </form>
</section>

@include('partials.footer-nav', ['active' => 'profile'])
@endsection
