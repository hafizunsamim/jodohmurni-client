@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h4>Debug Push Support</h4>
    <p class="text-muted">
        Checking Browser support
    </p>
    <pre id="debug-output" style="background:#f8f9fa;padding:10px;border-radius:6px;white-space:pre-wrap;"></pre>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const out = document.getElementById('debug-output');

    function logLine(label, value) {
        out.textContent += label + ': ' + value + '\n';
    }

    logLine('UserAgent', navigator.userAgent);
    logLine('Has serviceWorker', ('serviceWorker' in navigator));
    logLine('Has PushManager', ('PushManager' in window));
    logLine('Has Notification', ('Notification' in window));

    if ('Notification' in window) {
        logLine('Notification.permission', Notification.permission);
    }
});
</script>
@endpush
