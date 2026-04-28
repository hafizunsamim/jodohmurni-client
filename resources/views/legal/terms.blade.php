@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/legal.css') }}">
@endpush

@section('content')
<div class="container py-5 mt-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">

                    <h1 class="mb-2 text-center fw-bold">Terms of Use</h1>
                    <p class="text-center text-muted mb-4">
                        <strong>JodohMurni</strong><br>
                        Tarikh Kuat Kuasa: <em>[isi tarikh]</em>
                    </p>

                    <hr class="mb-4">

                    <section class="mb-4">
                        <h5 class="fw-bold">1. Pengenalan & Penerimaan Terma</h5>
                        <p>
                            Selamat datang ke JodohMurni, sebuah platform ta’aruf yang dibangunkan bagi membantu
                            individu Muslim mengenali calon pasangan secara beradab, beretika dan bertanggungjawab.
                        </p>
                        <p>
                            Dengan mendaftar, mengakses atau menggunakan mana-mana perkhidmatan JodohMurni,
                            anda mengesahkan bahawa anda telah membaca, memahami dan bersetuju untuk terikat
                            dengan Terma Penggunaan ini (“Terma”).
                        </p>
                        <p>
                            Jika anda tidak bersetuju dengan mana-mana bahagian Terma ini,
                            anda dinasihatkan untuk tidak menggunakan Platform ini.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">2. Definisi</h5>
                        <ul>
                            <li><strong>Platform</strong>: Laman web, aplikasi dan sistem berkaitan JodohMurni</li>
                            <li><strong>Pengguna</strong>: Individu yang mendaftar atau menggunakan Platform</li>
                            <li><strong>Akaun</strong>: Akaun pengguna berdaftar</li>
                            <li><strong>Perkhidmatan</strong>: Semua ciri dan fungsi yang disediakan</li>
                            <li><strong>Kandungan</strong>: Teks, imej, video atau maklumat yang dimuat naik oleh Pengguna</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">3. Objektif & Prinsip JodohMurni</h5>
                        <ul>
                            <li>Menyediakan ruang ta’aruf yang selamat, beradab dan bermaruah</li>
                            <li>Mengiktiraf perjalanan jodoh monogami dan poligami secara terbimbing</li>
                            <li>Menolak eksploitasi, manipulasi emosi, penipuan dan unsur scam</li>
                            <li>Menggalakkan penilaian calon secara objektif dan beretika</li>
                            <li>Menghormati hak, batas dan keputusan setiap individu</li>
                        </ul>
                        <p>
                            Platform ini bukan agen perkahwinan, bukan wali,
                            dan bukan wakil peribadi mana-mana pengguna.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">4. Kelayakan & Pendaftaran Akaun</h5>
                        <ul>
                            <li>Berumur 18 tahun ke atas</li>
                            <li>Mempunyai keupayaan undang-undang</li>
                            <li>Memberikan maklumat yang benar dan terkini</li>
                        </ul>
                        <p>
                            Setiap individu hanya dibenarkan satu akaun sahaja.
                            Akaun palsu atau penyamaran adalah dilarang sama sekali.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">5. Tanggungjawab Pengguna</h5>
                        <ul>
                            <li>Bertindak dengan adab dan kesopanan</li>
                            <li>Menghormati batas peribadi pengguna lain</li>
                            <li>Tidak memaksa atau memanipulasi emosi</li>
                            <li>Tidak meminta atau menyebarkan maklumat sensitif</li>
                            <li>Tidak menggunakan Platform untuk penipuan atau scam</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">6. Tatacara Interaksi & Larangan</h5>
                        <ul>
                            <li>Bahasa lucah atau menjatuhkan maruah</li>
                            <li>Kandungan palsu atau mengelirukan</li>
                            <li>Permintaan wang atau manfaat kewangan</li>
                            <li>Mengajak keluar dari Platform secara mencurigakan</li>
                        </ul>
                        <p>
                            JodohMurni berhak mengambil tindakan tanpa notis
                            jika keselamatan komuniti terjejas.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">7. Kandungan Pengguna</h5>
                        <p>
                            Pengguna mengekalkan hak ke atas Kandungan sendiri,
                            namun memberikan lesen terhad kepada JodohMurni
                            bagi tujuan operasi Platform.
                        </p>
                        <p>
                            Kandungan yang melanggar Terma boleh dipadam atau disekat.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">8. Pembayaran & Akses Perkhidmatan</h5>
                        <p>
                            Bayaran adalah berdasarkan skop perkhidmatan yang dinyatakan.
                            JodohMurni tidak menjamin hasil jodoh atau keserasian.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">9. Keselamatan & Risiko</h5>
                        <ul>
                            <li>Berhati-hati dalam komunikasi</li>
                            <li>Melaporkan tingkah laku mencurigakan</li>
                            <li>Tidak berkongsi maklumat sensitif secara terburu-buru</li>
                        </ul>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">10. Penamatan & Penggantungan Akaun</h5>
                        <p>
                            Akaun boleh digantung atau ditamatkan jika Terma dilanggar
                            atau keselamatan komuniti terancam.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">11. Had Liabiliti</h5>
                        <p>
                            JodohMurni tidak bertanggungjawab terhadap
                            tindakan pengguna lain atau keputusan peribadi
                            hasil interaksi di Platform.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">12. Indemniti</h5>
                        <p>
                            Pengguna bersetuju menanggung rugi JodohMurni
                            atas sebarang tuntutan akibat pelanggaran Terma ini.
                        </p>
                    </section>

                    <section class="mb-4">
                        <h5 class="fw-bold">13. Undang-Undang Terpakai</h5>
                        <p>
                            Terma ini ditadbir mengikut undang-undang Malaysia.
                        </p>
                    </section>

                    <section>
                        <h5 class="fw-bold">14. Pindaan Terma</h5>
                        <p>
                            Terma boleh dikemas kini dari semasa ke semasa.
                            Penggunaan berterusan Platform bermaksud penerimaan pindaan tersebut.
                        </p>
                    </section>


        </div>
    </div>
</div>
@endsection
