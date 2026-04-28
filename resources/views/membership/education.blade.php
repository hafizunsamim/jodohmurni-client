@extends('layouts.app')

@section('content')
<section class="jm-lite-education-page section-main px-3 px-sm-4 py-4">
    <div class="jm-lite-education-inner mx-auto w-100">
        {{-- Tahniah / Status AHLI LITE --}}
        <div class="text-center mb-4 p-3 p-md-4 rounded-3" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border: 1px solid #81c784;">
            <h1 class="h3 mb-2" style="color: #2e7d32;">Tahniah!</h1>
            <p class="mb-0 fw-semibold" style="color: #1b5e20;">Anda kini <strong>AHLI LITE</strong> — JodohMurni</p>
        </div>

        {{-- Jangan guna class global `.card` (style.css tetapkan height 220px untuk upload foto) --}}
        <div class="jm-lite-limit-panel shadow-sm mb-3 mb-md-4 rounded-3">
            <div class="p-3 p-md-4">
                <h2 class="h5 mb-2 mb-md-3" style="color: #37474f;">Limitasi Ahli LITE</h2>
                <p class="text-muted small mb-2 mb-md-3">Sebagai ahli percuma, anda boleh menggunakan platform dengan had berikut:</p>
                <ul class="jm-lite-limit-list mb-0 ps-3 small">
                    <li><strong>Profil:</strong> Profil anda dipaparkan dalam senarai calon dengan label &quot;Ahli Belum Berbayar&quot;.</li>
                    <li><strong>Chat:</strong> Maksimum <strong>10 sapaan</strong> sahaja. Hanya <strong>2 calon</strong> yang membalas sapaan dibenarkan untuk terus berbual; 8 sapaan selebihnya akan dinyahaktifkan secara automatik.</li>
                    <li><strong>Mesej:</strong> Had <strong>5 mesej</strong> sahaja bagi setiap calon yang dibenarkan.</li>
                    <li><strong>Gambar:</strong> Gambar anda hanya jelas kepada <strong>10 calon pertama</strong> yang anda sapa. Bagi calon LITE lain, gambar mereka akan dikaburkan.</li>
                </ul>
            </div>
        </div>

        {{-- Dua butang tindakan --}}
        <div class="d-grid gap-2 gap-md-3 jm-lite-education-actions">
            <a href="{{ route('membership.acknowledge-lite') }}" class="btn btn-lg" style="background: #78909c; color: #fff;">
                Saya faham & kekal <br/> &quot;<strong>AHLI LITE</strong>&quot; sahaja
            </a>
            <a href="{{ route('membership.acknowledge-upgrade') }}" class="btn btn-lg text-white" style="background: #2e7d32;">
                Saya faham & naik taraf <br/> ke &quot;<strong>AHLI ACTIVE</strong>&quot;
            </a>
        </div>
    </div>
</section>

<style>
/* Skrin pendidikan LITE: skrol penuh, teks panjang tidak terpotong, ruang bawah untuk safe area */
.jm-lite-education-page {
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
    margin-top: 20px;
    padding-bottom: calc(2.5rem + env(safe-area-inset-bottom, 0px));
    box-sizing: border-box;
    min-height: calc(100vh - 80px);
    min-height: calc(100dvh - 80px);
}
.jm-lite-education-inner {
    max-width: 560px;
}
.jm-lite-limit-panel {
    width: 100%;
    height: auto;
    min-height: 0;
    overflow: visible;
    box-sizing: border-box;
    /* Sama tema seperti .card upload foto (style.css) — tanpa class .card */
    background: #fae8f9;
    border: 2px dashed #8000ff;
    border-radius: 16px;
}
.jm-lite-limit-list {
    line-height: 1.65;
    overflow-wrap: anywhere;
    word-wrap: break-word;
    padding-left: 0 !important;
}
.jm-lite-limit-list li {
    position: relative;
    padding-left: 1.35rem;
}
.jm-lite-limit-list li::before {
    content: "✅";
    position: absolute;
    left: 0;
    top: 0.05rem;
}
.jm-lite-limit-list li + li {
    margin-top: 0.65rem;
}
.jm-lite-education-actions {
    padding-bottom: 0.5rem;
}
</style>
@endsection
