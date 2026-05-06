<link rel="stylesheet" href="{{ asset('css/onboarding.css') }}">

@extends('layouts.app')

@section('content')
    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <h3 class="mb-2 fw-bold" style="color:#2e7d32;" data-translate="onb_photos_title">Muat Naik Gambar Profil</h3>

                <ul class="text-muted small mb-2 ps-3 onboarding-photo-guide">
                    <li><strong data-translate="onb_photo_1_label">Gambar 1:</strong> <span data-translate="onb_photo_1_rule">wajib selfie (kamera depan).</span></li>
                    <li><strong data-translate="onb_photo_2_label">Gambar 2:</strong> <span data-translate="onb_photo_2_rule">(jika upload) full body.</span></li>
                    <li><strong data-translate="onb_photo_3_label">Gambar 3:</strong> <span data-translate="onb_photo_3_rule">(jika upload) berkaitan hobby.</span></li>
                    <li><strong data-translate="onb_photo_4_label">Gambar 4:</strong> <span data-translate="onb_photo_4_rule">bebas.</span></li>
                </ul>

                <div id="desktopBlock" class="alert alert-warning d-none">
                    <span data-translate="onb_photos_desktop_block_prefix">Onboarding ini</span>
                    <strong data-translate="onb_photos_desktop_block_strong">hanya boleh dibuat melalui telefon</strong>.
                    <span data-translate="onb_photos_desktop_block_tail">Sila buka link ini di phone.</span>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="photoForm" method="POST" action="{{ route('onboarding.photos.store') }}"
                    enctype="multipart/form-data">
                    @csrf

                    {{-- hidden: selfie base64 --}}
                    <input type="hidden" name="photo_1_data" id="photo_1_data" value="{{ old('photo_1_data', '') }}">

                    <div class="row g-3 mt-3">

                        {{-- SLOT 1: SELFIE --}}
                        <div class="col-6">
                            <div class="photo-card" role="button" tabindex="0" onclick="openSelfieModal()"
                                onkeydown="if(event.key==='Enter' || event.key===' ') { event.preventDefault(); openSelfieModal(); }">

                                <div class="photo-box" id="photoBox1">
                                    @if (!empty(old('photo_1_data')))
                                        <img src="{{ old('photo_1_data') }}" class="photo-preview" id="preview1">
                                    @elseif(!empty($onb['photo_1']))
                                        <img src="{{ asset($onb['photo_1']) }}" class="photo-preview" id="preview1">
                                    @else
                                        <div class="photo-placeholder">
                                            <span class="plus">📷</span>
                                            <small data-translate="onb_selfie">Selfie</small>
                                        </div>
                                    @endif
                                </div>
                                <ul class="small text-muted mt-2">
                                    <li><span data-translate="onb_photo_selfie_tip_1">Tap untuk selfie (wajib)</span></li>
                                    <li><span data-translate="onb_photo_selfie_tip_2">Jika ingin ulang selfie tab digambar sahaja.</span></li>
                                </ul>

                                {{-- ❌ buang error bawah card (kita guna modal popup) --}}
                                {{-- @error('photo_1_data')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror --}}
                            </div>
                        </div>

                        {{-- SLOT 2 --}}
                        <div class="col-6">
                            <label class="photo-card">
                                <input type="file" name="photo_2" accept="image/*" hidden
                                    onchange="previewFile(this, 2)">
                                <div class="photo-box" id="photoBox2">
                                    @if (!empty($onb['photo_2']))
                                        <img src="{{ asset($onb['photo_2']) }}" class="photo-preview" id="preview2">
                                    @else
                                        <div class="photo-placeholder">
                                            <span class="plus">＋</span>
                                            <small data-translate="onb_photo_2_placeholder">Full body/Bahagian Pinggang ke Atas</small>
                                        </div>
                                    @endif
                                </div>

                                {{-- <div class="small text-muted mt-2">Sangat disyorkan untuk menaikkan rating anda</div> --}}
                                <ul class="small text-muted mt-2">
                                    <li><span data-translate="onb_photo_recommend_rating">Sangat disyorkan untuk menaikkan rating anda</span></li>
                                </ul>

                                @error('photo_2')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </label>
                        </div>

                        {{-- SLOT 3 --}}
                        <div class="col-6">
                            <label class="photo-card">
                                <input type="file" name="photo_3" accept="image/*" hidden
                                    onchange="previewFile(this, 3)">
                                <div class="photo-box" id="photoBox3">
                                    @if (!empty($onb['photo_3']))
                                        <img src="{{ asset($onb['photo_3']) }}" class="photo-preview" id="preview3">
                                    @else
                                        <div class="photo-placeholder">
                                            <span class="plus">＋</span>
                                            <small data-translate="onb_photo_3_placeholder">Hobby</small>
                                        </div>
                                    @endif
                                </div>

                                <ul class="small text-muted mt-2">
                                    <li><span data-translate="onb_photo_3_tip">Gambar apa sahaja yang melambangkan hobi/minat anda</span></li>
                                </ul>

                        

                                @error('photo_3')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </label>
                        </div>

                        {{-- SLOT 4 --}}
                        <div class="col-6">
                            <label class="photo-card">
                                <input type="file" name="photo_4" accept="image/*" hidden
                                    onchange="previewFile(this, 4)">
                                <div class="photo-box" id="photoBox4">
                                    @if (!empty($onb['photo_4']))
                                        <img src="{{ asset($onb['photo_4']) }}" class="photo-preview" id="preview4">
                                    @else
                                        <div class="photo-placeholder">
                                            <span class="plus">＋</span>
                                            <small data-translate="onb_free">Bebas</small>
                                        </div>
                                    @endif
                                </div>

                                <div class="small text-muted mt-2"></div>
                                    <ul class="small text-muted mt-2">
                                    <li><span data-translate="onb_photo_4_tip">Upload gambar terbaik anda</span></li>
                                </ul>


                                @error('photo_4')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </label>
                        </div>

                    </div>

                    <button id="submitBtn" class="btn mt-4 w-100 text-white" style="background:#2e7d32;">
                        <span data-translate="onb_photos_next_personal">Seterusnya: Isi Maklumat Pendaftaran</span>
                    </button>
                </form>

            </div>
        </div>
    </div>

    {{-- =========================
   Selfie Modal (custom)
   ========================= --}}
    <style>
        .onboarding-photo-guide {
            padding-left: 1.1rem !important;
        }
        .onboarding-photo-guide li {
            list-style: disc !important;
            margin-bottom: 2px;
        }

        .cam-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 16px;
        }

        .cam-card {
            width: min(520px, 100%);
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
        }

        .cam-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 14px;
            border-bottom: 1px solid #eee;
        }

        .cam-close {
            border: 0;
            background: transparent;
            font-size: 18px;
        }

        .cam-body {
            padding: 14px;
        }

        #selfieVideo {
            width: 100%;
            border-radius: 12px;
            background: #000;
        }

        .cam-footer {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            padding: 12px 14px;
            border-top: 1px solid #eee;
        }

        .d-none {
            display: none !important;
        }

        .photo-card[role="button"] {
            cursor: pointer;
        }
    </style>

    <div id="selfieModal" class="cam-modal d-none" aria-hidden="true">
        <div class="cam-card">
            <div class="cam-header">
                <strong data-translate="onb_take_selfie_title">Ambil Selfie (kamera depan)</strong>
                <button type="button" class="cam-close" onclick="closeSelfieModal()">✕</button>
            </div>

            <div class="cam-body">
                <video id="selfieVideo" playsinline autoplay muted></video>
                <canvas id="selfieCanvas" class="d-none"></canvas>
                <div id="camError" class="alert alert-danger small d-none mt-2"></div>
            </div>

            <div class="cam-footer">
                <button type="button" class="btn btn-secondary btn-sm" onclick="retakeSelfie()"><span data-translate="onb_retake">Retake</span></button>
                <button type="button" class="btn btn-success btn-sm" onclick="snapAndUseSelfie()"><span data-translate="onb_snap">Snap</span></button>
            </div>
        </div>
    </div>

    {{-- =========================
   ✅ Selfie required popup (Bootstrap Modal)
   ========================= --}}
    <div class="modal fade" id="selfieRequiredModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold" data-translate="onb_selfie_required_title">Selfie diperlukan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">
                        <span data-translate="onb_selfie_required_prefix">Sila ambil</span>
                        <strong data-translate="onb_selfie_required_strong">Selfie (Gambar 1)</strong>
                        <span data-translate="onb_selfie_required_tail">dahulu sebelum meneruskan pendaftaran.</span>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal"><span data-translate="popup_button">Faham</span></button>
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal"
                        onclick="openSelfieModal()"><span data-translate="onb_take_selfie_cta">Ambil Selfie</span></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let stream = null;
        let snappedDataUrl = null;

        function isMobileDevice() {
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }

        (function mobileGuard(){
          if (!isMobileDevice()) {
            document.getElementById('desktopBlock').classList.remove('d-none');
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('photoForm').style.opacity = '0.6';
          }
        })();

        function previewFile(input, index) {
            const box = document.getElementById('photoBox' + index);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    box.innerHTML = `<img src="${e.target.result}" class="photo-preview" id="preview${index}">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function startFrontCamera() {
            const video = document.getElementById('selfieVideo');
            const errBox = document.getElementById('camError');

            try {
                if (stream) {
                    stream.getTracks().forEach(t => t.stop());
                    stream = null;
                }

                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "user"
                    },
                    audio: false
                });

                video.srcObject = stream;
                await video.play();
            } catch (err) {
                errBox.setAttribute("data-translate", "onb_camera_access_error");
                errBox.textContent = "Tak dapat akses kamera. Pastikan guna HTTPS atau buka melalui localhost.";
                errBox.classList.remove('d-none');
            }
        }

        async function openSelfieModal() {
            if (!isMobileDevice()) return;

            snappedDataUrl = null;
            document.getElementById('camError').classList.add('d-none');
            document.getElementById('selfieModal').classList.remove('d-none');

            await startFrontCamera();
        }

        function closeSelfieModal() {
            const video = document.getElementById('selfieVideo');
            if (stream) {
                stream.getTracks().forEach(t => t.stop());
                stream = null;
            }
            video.srcObject = null;
            document.getElementById('selfieModal').classList.add('d-none');
        }

        async function retakeSelfie() {
            snappedDataUrl = null;
            document.getElementById('photo_1_data').value = "";

            const box = document.getElementById('photoBox1');
            box.innerHTML = `
    <div class="photo-placeholder">
      <span class="plus">📷</span>
      <small data-translate="onb_selfie">Selfie</small>
    </div>
  `;

            const modal = document.getElementById('selfieModal');
            if (!modal.classList.contains('d-none')) {
                document.getElementById('camError').classList.add('d-none');
                await startFrontCamera();
            }
        }

        function snapAndUseSelfie() {
            const video = document.getElementById('selfieVideo');
            const canvas = document.getElementById('selfieCanvas');
            const box = document.getElementById('photoBox1');

            if (!video.videoWidth) return;

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            snappedDataUrl = canvas.toDataURL('image/jpeg', 0.92);

            box.innerHTML = `<img src="${snappedDataUrl}" class="photo-preview" id="preview1">`;
            document.getElementById('photo_1_data').value = snappedDataUrl;

            closeSelfieModal();
        }

        // ✅ intercept submit: kalau selfie kosong, show modal popup
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.getElementById("photoForm");
            const selfieInput = document.getElementById("photo_1_data");

            form.addEventListener("submit", (e) => {
                const val = (selfieInput.value || "").trim();
                if (!val) {
                    e.preventDefault();

                    // show bootstrap modal
                    const mEl = document.getElementById("selfieRequiredModal");
                    if (mEl && window.bootstrap?.Modal) {
                        new bootstrap.Modal(mEl, {
                            backdrop: true,
                            keyboard: true
                        }).show();
                    } else {
                        // fallback kalau bootstrap tak ada
                        alert(jmT("onb_selfie_required_alert", "Please take a selfie (Photo 1) before continuing."));
                    }
                    return false;
                }
            });

            // ✅ optional: kalau server-side validation balik dengan error selfie,
            // terus pop modal bila page reload
            @if ($errors->has('photo_1_data'))
                const mEl = document.getElementById("selfieRequiredModal");
                if (mEl && window.bootstrap?.Modal) {
                    new bootstrap.Modal(mEl).show();
                }
            @endif
        });
    </script>
@endsection
