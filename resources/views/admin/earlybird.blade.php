@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 560px;">
    <h3 class="fw-bold mb-3">Early Bird (Pakej HYPE)</h3>

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
                    <td class="text-muted">Status Early Bird</td>
                    <td><strong>{{ $isActive ? 'Aktif' : 'Tidak aktif' }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Bilangan pengguna early bird</td>
                    <td><strong>{{ $subscriberCount ?? 0 }} / {{ $maxUsers ?? 300 }}</strong></td>
                </tr>
                <tr>
                    <td class="text-muted">Early Bird berjalan</td>
                    <td>{{ $isRunning ? 'Ya (pakej HYPE sahaja dipaparkan)' : 'Tidak' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h6 class="card-title">Tukar status</h6>
            <form method="POST" action="{{ route('admin.earlybird.toggle') }}" class="d-inline">
                @csrf
                <input type="hidden" name="active" value="{{ $isActive ? '0' : '1' }}">
                <button type="submit" class="btn {{ $isActive ? 'btn-outline-danger' : 'btn-success' }}">
                    {{ $isActive ? 'Matikan Early Bird' : 'Aktifkan Early Bird' }}
                </button>
            </form>
        </div>
    </div>

    <p class="small text-muted mt-3">
        Early Bird berjalan apabila <strong>is_early_bird_active</strong> ON dan bilangan pengguna yang subscribe pakej HYPE &lt; {{ $maxUsers ?? 300 }}.
        Bila cap {{ $maxUsers ?? 300 }} user dicapai, Early Bird automatik dimatikan. Anda juga boleh matikan manual dengan butang di atas.
    </p>
    <p class="small text-muted mt-2">
        Command: <code>php82 artisan earlybird:toggle --on</code>, <code>php82 artisan earlybird:toggle --off</code>, <code>php82 artisan earlybird:status</code>.
    </p>
</div>
@endsection
