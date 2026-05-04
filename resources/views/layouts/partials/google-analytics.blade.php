@php($gaId = config('analytics.google_measurement_id'))
@if($gaId)
{{-- Google tag (gtag.js) — Measurement ID dari .env --}}
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', @json($gaId));
</script>
@endif
