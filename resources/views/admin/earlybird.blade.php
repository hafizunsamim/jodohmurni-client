@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 560px;">
    <h3 class="fw-bold mb-3" data-translate="admin_earlybird_title">Early Bird (Pakej HYPE)</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted" data-translate="admin_earlybird_status">Status Early Bird</td>
                    <td><strong>{{ $isActive ? \App\Support\JmI18n::t('admin_earlybird_active') : \App\Support\JmI18n::t('admin_earlybird_inactive') }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted" data-translate="admin_earlybird_user_count">Bilangan pengguna early bird</td>
                    <td><strong>{{ $subscriberCount ?? 0 }} / {{ $maxUsers ?? 300 }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted" data-translate="admin_earlybird_running">Early Bird berjalan</td>
                    <td>{{ $isRunning ? \App\Support\JmI18n::t('admin_earlybird_running_yes') : \App\Support\JmI18n::t('admin_earlybird_running_no') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h6 class="card-title" data-translate="admin_earlybird_toggle_title">Tukar status</h6>
            <form method="POST" action="{{ route('admin.earlybird.toggle') }}" class="d-inline">
                @csrf
                <input type="hidden" name="active" value="{{ $isActive ? '0' : '1' }}">
                <button type="submit" class="btn {{ $isActive ? 'btn-outline-danger' : 'btn-success' }}">
                    {{ $isActive ? \App\Support\JmI18n::t('admin_earlybird_turn_off') : \App\Support\JmI18n::t('admin_earlybird_turn_on') }}
                </button>
            </form>
        </div>
    </div>

    <p class="small text-muted mt-3">
        <span data-translate="admin_earlybird_note_1">Early Bird berjalan apabila</span> <strong>is_early_bird_active</strong> <span data-translate="admin_earlybird_note_2">ON dan bilangan pengguna yang subscribe pakej HYPE</span> &lt; {{ $maxUsers ?? 300 }}.
        <span data-translate="admin_earlybird_note_3">Bila cap</span> {{ $maxUsers ?? 300 }} <span data-translate="admin_earlybird_note_4">user dicapai, Early Bird automatik dimatikan. Anda juga boleh matikan manual dengan butang di atas.</span>
    </p>
    <p class="small text-muted mt-2">
        <span data-translate="admin_earlybird_command">Command</span>: <code>php82 artisan earlybird:toggle --on</code>, <code>php82 artisan earlybird:toggle --off</code>, <code>php82 artisan earlybird:status</code>.
    </p>
</div>
@endsection
