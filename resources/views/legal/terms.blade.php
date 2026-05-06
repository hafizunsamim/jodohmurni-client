@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/legal.css') }}">
@endpush

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">

                    <h1 class="mb-2 text-center fw-bold" data-translate="legal_terms_title">Terms of Use</h1>
                    <p class="text-center text-muted mb-4">
                        <strong>JodohMurni</strong><br>
                        <span data-translate="legal_effective_date">Tarikh Kuat Kuasa</span>: <em>[isi tarikh]</em>
                    </p>

                    <hr class="mb-4">

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s1_title">1. Pengenalan & Penerimaan Terma</h5>
                        <p data-translate="legal_terms_s1_p1">
                            Selamat datang ke JodohMurni, sebuah platform ta’aruf yang dibangunkan bagi membantu individu Muslim mengenali calon pasangan secara beradab, beretika dan bertanggungjawab.
                        </p>
                        <p data-translate="legal_terms_s1_p2">
                            Dengan mendaftar, mengakses atau menggunakan mana-mana perkhidmatan JodohMurni, anda mengesahkan bahawa anda telah membaca, memahami dan bersetuju untuk terikat dengan Terma Penggunaan ini (“Terma”).
                        </p>
                        <p data-translate="legal_terms_s1_p3">
                            Jika anda tidak bersetuju dengan mana-mana bahagian Terma ini, anda dinasihatkan untuk tidak menggunakan Platform ini.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s2_title">2. Definisi</h5>
                        <ul>
                            <li data-translate="legal_terms_s2_li1"><strong>Platform</strong>: Laman web, aplikasi dan sistem berkaitan JodohMurni</li>
                            <li data-translate="legal_terms_s2_li2"><strong>Pengguna</strong>: Individu yang mendaftar atau menggunakan Platform</li>
                            <li data-translate="legal_terms_s2_li3"><strong>Akaun</strong>: Akaun pengguna berdaftar</li>
                            <li data-translate="legal_terms_s2_li4"><strong>Perkhidmatan</strong>: Semua ciri dan fungsi yang disediakan</li>
                            <li data-translate="legal_terms_s2_li5"><strong>Kandungan</strong>: Teks, imej, video atau maklumat yang dimuat naik oleh Pengguna</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s3_title">3. Objektif & Prinsip JodohMurni</h5>
                        <ul>
                            <li data-translate="legal_terms_s3_li1">Menyediakan ruang ta’aruf yang selamat, beradab dan bermaruah</li>
                            <li data-translate="legal_terms_s3_li2">Mengiktiraf perjalanan jodoh monogami dan poligami secara terbimbing</li>
                            <li data-translate="legal_terms_s3_li3">Menolak eksploitasi, manipulasi emosi, penipuan dan unsur scam</li>
                            <li data-translate="legal_terms_s3_li4">Menggalakkan penilaian calon secara objektif dan beretika</li>
                            <li data-translate="legal_terms_s3_li5">Menghormati hak, batas dan keputusan setiap individu</li>
                        </ul>
                        <p data-translate="legal_terms_s3_p1">Platform ini bukan agen perkahwinan, bukan wali, dan bukan wakil peribadi mana-mana pengguna.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s4_title">4. Kelayakan & Pendaftaran Akaun</h5>
                        <ul>
                            <li data-translate="legal_terms_s4_li1">Berumur 18 tahun ke atas</li>
                            <li data-translate="legal_terms_s4_li2">Mempunyai keupayaan undang-undang</li>
                            <li data-translate="legal_terms_s4_li3">Memberikan maklumat yang benar dan terkini</li>
                        </ul>
                        <p data-translate="legal_terms_s4_p1">Setiap individu hanya dibenarkan satu akaun sahaja. Akaun palsu atau penyamaran adalah dilarang sama sekali.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s5_title">5. Tanggungjawab Pengguna</h5>
                        <ul>
                            <li data-translate="legal_terms_s5_li1">Bertindak dengan adab dan kesopanan</li>
                            <li data-translate="legal_terms_s5_li2">Menghormati batas peribadi pengguna lain</li>
                            <li data-translate="legal_terms_s5_li3">Tidak memaksa atau memanipulasi emosi</li>
                            <li data-translate="legal_terms_s5_li4">Tidak meminta atau menyebarkan maklumat sensitif</li>
                            <li data-translate="legal_terms_s5_li5">Tidak menggunakan Platform untuk penipuan atau scam</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s6_title">6. Tatacara Interaksi & Larangan</h5>
                        <ul>
                            <li data-translate="legal_terms_s6_li1">Bahasa lucah atau menjatuhkan maruah</li>
                            <li data-translate="legal_terms_s6_li2">Kandungan palsu atau mengelirukan</li>
                            <li data-translate="legal_terms_s6_li3">Permintaan wang atau manfaat kewangan</li>
                            <li data-translate="legal_terms_s6_li4">Mengajak keluar dari Platform secara mencurigakan</li>
                        </ul>
                        <p data-translate="legal_terms_s6_p1">JodohMurni berhak mengambil tindakan tanpa notis jika keselamatan komuniti terjejas.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s7_title">7. Kandungan Pengguna</h5>
                        <p data-translate="legal_terms_s7_p1">Pengguna mengekalkan hak ke atas Kandungan sendiri, namun memberikan lesen terhad kepada JodohMurni bagi tujuan operasi Platform.</p>
                        <p data-translate="legal_terms_s7_p2">Kandungan yang melanggar Terma boleh dipadam atau disekat.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s8_title">8. Pembayaran & Akses Perkhidmatan</h5>
                        <p data-translate="legal_terms_s8_p1">Bayaran adalah berdasarkan skop perkhidmatan yang dinyatakan. JodohMurni tidak menjamin hasil jodoh atau keserasian.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s9_title">9. Keselamatan & Risiko</h5>
                        <ul>
                            <li data-translate="legal_terms_s9_li1">Berhati-hati dalam komunikasi</li>
                            <li data-translate="legal_terms_s9_li2">Melaporkan tingkah laku mencurigakan</li>
                            <li data-translate="legal_terms_s9_li3">Tidak berkongsi maklumat sensitif secara terburu-buru</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s10_title">10. Penamatan & Penggantungan Akaun</h5>
                        <p data-translate="legal_terms_s10_p1">Akaun boleh digantung atau ditamatkan jika Terma dilanggar atau keselamatan komuniti terancam.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s11_title">11. Had Liabiliti</h5>
                        <p data-translate="legal_terms_s11_p1">JodohMurni tidak bertanggungjawab terhadap tindakan pengguna lain atau keputusan peribadi hasil interaksi di Platform.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s12_title">12. Indemniti</h5>
                        <p data-translate="legal_terms_s12_p1">Pengguna bersetuju menanggung rugi JodohMurni atas sebarang tuntutan akibat pelanggaran Terma ini.</p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold" data-translate="legal_terms_s13_title">13. Undang-Undang Terpakai</h5>
                        <p data-translate="legal_terms_s13_p1">Terma ini ditadbir mengikut undang-undang Malaysia.</p>
                    </section>

                    <section>
                        <h5 class="fw-bold" data-translate="legal_terms_s14_title">14. Pindaan Terma</h5>
                        <p data-translate="legal_terms_s14_p1">Terma boleh dikemas kini dari semasa ke semasa. Penggunaan berterusan Platform bermaksud penerimaan pindaan tersebut.</p>
                    </section>


        </div>
    </div>
</div>
@endsection
