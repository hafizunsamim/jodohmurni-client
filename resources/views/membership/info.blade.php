@extends('layouts.app')

@section('content')
<div class="keahlian-page">
  <div class="keahlian-wrap">
    <h1 class="keahlian-title">Keahlian</h1>
    <p class="keahlian-subtitle text-muted">Perbandingan pakej LITE, ACTIVE, GRADUATE dan HYPE.</p>

    <div class="keahlian-grid">
      {{-- Lajur kriteria (ikon + label) --}}
      <div class="keahlian-criteria-col">
        <div class="keahlian-criteria-row keahlian-criteria-header">&nbsp;</div>
        <div class="keahlian-criteria-row">
          <!-- <span class="keahlian-criteria-icon">
            {{-- Tag / sijil: segi empat condong, lubang bulat penjuru atas kanan --}}
            <svg class="keahlian-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 8l7-5 9 4v10l-9 4-7-5V8z"/><circle cx="15" cy="6" r="1.5"/></svg>
          </span> -->
          <span class="keahlian-criteria-label">PENERANGAN</span>
        </div>
        <div class="keahlian-criteria-row">
          <!-- <span class="keahlian-criteria-icon">
            {{-- Dua buih chat bertindih, kiri ada tiga titik --}}
            <svg class="keahlian-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18c-2 0-4-1.5-4-4V8c0-2.5 2-4 4-4h6c2 0 4 1.5 4 4v2"/><path d="M15 14v2c0 2.5-2 4-4 4H5l-1 3 3-2h2c2 0 4-1.5 4-4v-2"/><circle cx="8" cy="12" r="0.6" fill="currentColor"/><circle cx="10.5" cy="12" r="0.6" fill="currentColor"/><circle cx="13" cy="12" r="0.6" fill="currentColor"/></svg>
          </span> -->
          <span class="keahlian-criteria-label">CHAT</span>
        </div>
        <div class="keahlian-criteria-row">
          <!-- <span class="keahlian-criteria-icon">
            {{-- Jam dengan anak panah suku atas kiri, buih kecil penjuru bawah kanan --}}
            <svg class="keahlian-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3 2"/><path d="M18 17.5a2 2 0 0 1-1.5.6 2 2 0 0 1-1.5-.6"/></svg>
          </span> -->
          <span class="keahlian-criteria-label">HAD PERBUALAN</span>
        </div>
        <div class="keahlian-criteria-row">
          <!-- <span class="keahlian-criteria-icon">
            {{-- Buku terbuka: tulang kiri, dua muka surat, garisan teks di kanan --}}
            <svg class="keahlian-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h6a2 2 0 0 1 2 2v10H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1z"/><path d="M20 6h-6a2 2 0 0 0-2 2v10h8a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1z"/><path d="M10 6V4"/><path d="M14 10h4"/><path d="M14 13h4"/><path d="M14 16h3"/></svg>
          </span> -->
          <span class="keahlian-criteria-label">E-BOOK</span>
        </div>
        <div class="keahlian-criteria-row">
          <!-- <span class="keahlian-criteria-icon">
            {{-- Tiga orang: tengah lebih besar, dua di sisi --}}
            <svg class="keahlian-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="6" r="3.5"/><path d="M12 21v-2a4 4 0 0 0-4-4H6a3.5 3.5 0 0 0-3.5 3.5V21"/><path d="M22 21v-1.5a3.5 3.5 0 0 0-3.5-3.5H16a4 4 0 0 0-4 4V21"/><path d="M5 10.5a2.5 2.5 0 0 1 2.5-2.5"/><path d="M19 10.5a2.5 2.5 0 0 0-2.5-2.5"/><path d="M12 9.5v1"/></svg>
          </span> -->
          <span class="keahlian-criteria-label">FORUM</span>
        </div>
      </div>

      {{-- LITE --}}
      <div class="keahlian-tier-card keahlian-tier--lite">
        <div class="keahlian-tier-header">LITE</div>
        <div class="keahlian-tier-body">
          <div class="keahlian-tier-row">Akaun asas untuk ahli yang telah mendaftar tetapi belum melanggan pakej keahlian.</div>
          <div class="keahlian-tier-row">Boleh memulakan chat dengan sehingga <strong>10 calon</strong>. Hanya <strong>2 calon pertama yang membalas</strong> boleh meneruskan perbualan.</div>
          <div class="keahlian-tier-row">Maksimum <strong>5 mesej</strong> bagi setiap perbualan.</div>
          <div class="keahlian-tier-row">Tiada akses</div>
          <div class="keahlian-tier-row">Tiada akses</div>
        </div>
      </div>

      {{-- ACTIVE (PILIHAN POPULAR) --}}
      <div class="keahlian-tier-card keahlian-tier--active">
        <div class="keahlian-tier-header">ACTIVE</div>
        <div class="keahlian-tier-body">
          <div class="keahlian-tier-row">Akaun keahlian aktif yang membolehkan anda menggunakan semua fungsi utama platform.</div>
          <div class="keahlian-tier-row">Chat <strong>tanpa had</strong> dengan semua calon sepanjang tempoh langganan.</div>
          <div class="keahlian-tier-row"><strong>Tiada had</strong></div>
          <div class="keahlian-tier-row">Akses diberikan</div>
          <div class="keahlian-tier-row">Akses diberikan</div>
        </div>
      </div>

      {{-- GRADUATE --}}
      <div class="keahlian-tier-card keahlian-tier--graduate">
        <div class="keahlian-tier-header">GRADUATE</div>
        <div class="keahlian-tier-body">
          <div class="keahlian-tier-row">Akaun ahli yang pernah melanggan tetapi tempoh keahlian telah tamat.</div>
          <div class="keahlian-tier-row">Boleh memulakan chat dengan sehingga <strong>10 calon</strong>. Hanya <strong>2 calon pertama yang membalas</strong> boleh meneruskan perbualan.</div>
          <div class="keahlian-tier-row">Maksimum <strong>5 mesej</strong> bagi setiap perbualan.</div>
          <div class="keahlian-tier-row">Tiada akses</div>
          <div class="keahlian-tier-row">Tiada akses</div>
        </div>
      </div>

      {{-- HYPE --}}
      <div class="keahlian-tier-card keahlian-tier--hype">
        <div class="keahlian-tier-header">HYPE</div>
        <div class="keahlian-tier-body">
          <div class="keahlian-tier-row">Akaun premium untuk ahli yang melanggan atau memperbaharui pakej keahlian.</div>
          <div class="keahlian-tier-row">Chat <strong>tanpa had</strong> dengan semua calon.</div>
          <div class="keahlian-tier-row"><strong>Tiada had</strong></div>
          <div class="keahlian-tier-row">Akses diberikan</div>
          <div class="keahlian-tier-row">Akses diberikan</div>
        </div>
      </div>
    </div>

    <p class="keahlian-footer text-muted small mt-4 mb-0">Pilihan anda akan disimpan secara automatik apabila anda melanggan.</p>

    @auth
      <div class="mt-3">
        <a href="{{ route('subscription.index') }}" class="btn btn-success">Lihat Pakej Langganan</a>
      </div>
    @endauth
  </div>
</div>

<style>
.keahlian-page { min-height: 100vh; padding: 1.5rem 1rem 3rem; box-sizing: border-box; }
.keahlian-wrap {
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
}
.keahlian-title { font-size: 1.85rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.35rem; }
.keahlian-subtitle { font-size: 0.95rem; margin-bottom: 1.75rem; }

.keahlian-grid {
  display: grid;
  grid-template-columns: 180px repeat(4, 1fr);
  gap: 0;
  align-items: stretch;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0,0,0,0.08), 0 0 0 1px rgba(10,126,62,0.08);
  overflow: hidden;
  border: 1px solid rgba(10,126,62,0.1);
}

.keahlian-criteria-col {
  display: flex;
  flex-direction: column;
  background: #f8faf8;
  border-right: 1px solid #e8ece8;
}
.keahlian-criteria-header { min-height: 52px; }
.keahlian-criteria-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 1rem 14px;
  border-bottom: 1px solid #e8e8e8;
  font-size: 0.88rem;
  font-weight: 600;
  color: #2d3748;
  min-height: 72px;
}
.keahlian-criteria-row:last-child { border-bottom: none; }
.keahlian-criteria-icon {
  color: #0a7e3e;
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.keahlian-icon-svg {
  width: 22px;
  height: 22px;
}
.keahlian-criteria-label { line-height: 1.3; }

.keahlian-tier-card {
  display: flex;
  flex-direction: column;
  background: #fff;
  border-right: 1px solid #eee;
  position: relative;
}
.keahlian-tier-card:last-child { border-right: none; }

.keahlian-tier-badge {
  background: #0a7e3e;
  color: #fff;
  font-size: 0.7rem;
  font-weight: 700;
  text-align: center;
  padding: 6px 8px;
  letter-spacing: 0.02em;
}
.keahlian-tier-header {
  font-size: 1rem;
  font-weight: 700;
  padding: 14px 1rem;
  text-align: center;
  border-bottom: 1px solid #eee;
}
.keahlian-tier--lite .keahlian-tier-header {
  background: #fff;
  color: #1a1a1a;
  border-top: 3px solid #94d3a2;
}
.keahlian-tier--active .keahlian-tier-header {
  background: #0a7e3e;
  color: #fff;
  border-top: none;
}
.keahlian-tier-card:hover {
  box-shadow: 0 12px 32px rgba(10,126,62,0.18);
  z-index: 1;
  transform: scale(1.02);
}
.keahlian-tier-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.keahlian-tier--graduate .keahlian-tier-header {
  background: #fff;
  color: #1a1a1a;
  border-top: 3px solid #94d3a2;
}
.keahlian-tier--hype .keahlian-tier-header {
  background: #1a5f4a;
  color: #fff;
  border-top: 3px solid #1a5f4a;
}

.keahlian-tier-body { display: flex; flex-direction: column; flex: 1; }
.keahlian-tier-row {
  padding: 1rem;
  font-size: 0.85rem;
  line-height: 1.5;
  color: #333;
  border-bottom: 1px solid #eee;
  min-height: 72px;
}
.keahlian-tier-row:last-child { border-bottom: none; }

.keahlian-footer { border-top: 1px solid #eee; padding-top: 1.25rem; margin-top: 1.5rem; }

@media (max-width: 1199px) {
  .keahlian-grid { grid-template-columns: 160px repeat(4, minmax(140px, 1fr)); }
  .keahlian-criteria-row,
  .keahlian-tier-row { padding: 0.85rem 10px; font-size: 0.8rem; min-height: 68px; }
}
@media (max-width: 991px) {
  .keahlian-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  .keahlian-criteria-col {
    flex-direction: row;
    flex-wrap: wrap;
    gap: 0;
    border-right: none;
    border-bottom: 1px solid #e8e8e8;
    padding: 0.75rem 1rem;
  }
  .keahlian-criteria-header { display: none; }
  .keahlian-criteria-row {
    flex: 1 1 auto;
    min-width: 120px;
    min-height: 0;
    padding: 0.5rem 8px;
    border-bottom: none;
    border-right: 1px solid #e0e0e0;
  }
  .keahlian-criteria-row:nth-child(5) { border-right: none; }
  .keahlian-tier-card {
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }
}
@media (max-width: 575px) {
  .keahlian-page { padding: 1rem 0.75rem 2rem; }
  .keahlian-title { font-size: 1.5rem; }
  .keahlian-subtitle { font-size: 0.875rem; }
  .keahlian-criteria-row { font-size: 0.75rem; }
  .keahlian-tier-row { font-size: 0.8rem; padding: 0.75rem; min-height: 0; }
}
</style>
@endsection
