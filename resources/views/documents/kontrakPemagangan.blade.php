<!DOCTYPE html>
<html>

<head>
    <title>Perjanjian Pemagangan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: white;
            padding: 2rem;
            font-size: 12px;
            text-align: justify;
            /* Mengatur teks rata kiri dan kanan */
            text-justify: inter-word;
            /* Mengatur jarak antar kata untuk perataan */
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            margin-top: 75px;
            background-color: white;
            padding: 1rem;
            /* border: 1px solid #d1d5db; */
        }

        h1 {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        p {
            margin-bottom: 1rem;
        }

        ol {
            /* list-style-type: decimal; */
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        h2 {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        /* .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        } */

        .signature div {
            text-align: center;
        }

        .page_break {
            page-break-before: always;
        }

        li {
            margin-bottom: 8px;
        }

        .signature-section {
            /* width: 100%; */
            margin-bottom: 20px;
        }

        .signature {
            /* width: 40%; */
            display: inline-block;
            text-align: center;
            vertical-align: top;
        }

        .signature img {
            display: block;
            margin: 0 auto 10px;
        }

        .approval-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .approval-section img {
            display: block;
            margin: 0 auto 10px;
        }

         .watermark-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .watermark-text {
            position: absolute;
            font-size: 20px;
            font-weight: bolder;
            color: rgba(0, 0, 0, 0.15);
            transform: rotate(-30deg);
            white-space: nowrap;
            text-align: center;
        }
    </style>
</head>

<body>
    {{-- <table>
        <tr>
            <td>
                <div style="margin-right: 188px">
                    <img style="width: 200px" src="{{ public_path('assets/images/logos/logo-avi.svg') }}" alt=" ">
                </div>
            </td>
            <td>
                <div style="text-align: right; width: 100%;"><span>Jl. Lanbau RT 05/10 Kel Karang Asem Barat</span></div>
                <div style="text-align: right;"><span>Kec. Citeureup, Bogot, Indonesia, 16810</span></div>
                <div style="text-align: right;"><span>Telp : +62 21 87919130</span></div>
                <div style="text-align: right;"><span>Telp : +62 21 87919119</span></div>
            </td>
        </tr>
    </table>
    <hr style="border: 0.5px solid black"> --}}

    @if (!in_array(Auth::user()->getRole(), ['admin', 'admin 2', 'admin 3']))
        <div class="watermark-background">
            @for ($i = 0; $i < 15; $i++)
                @for ($j = 0; $j < 5; $j++)
                    <div class="watermark-text" style="top: {{ $i * 150 }}px; left: {{ $j * 300 }}px;">
                        DOKUMEN RAHASIA<br>
                        Diakses oleh: {{ Auth::user()->fullname ?? 'Tidak diketahui' }}<br>
                        Pada: {{ now()->isoFormat('D MMMM Y HH:mm') }}
                    </div>
                @endfor
            @endfor
        </div>
    @endif

    <div class="container">
        <h1 style="margin-bottom: 0px">PERJANJIAN PEMAGANGAN</h1>
        <p style="text-align: center; margin-top: 0px;">No. {{ $kontrak->nomor }}</p>
        <p>Pada hari ini, {{ ($kontrak->start_date ?? \Carbon\Carbon::now())->isoFormat('dddd') }}, tanggal
            {{ ($kontrak->start_date ?? \Carbon\Carbon::now())->isoFormat('D MMMM Y') }} di Bogor, telah dibuat dan
            ditandatangani
            Perjanjian Pelatihan Kerja (selanjutnya akan disebut “Perjanjian”), oleh dan antara:</p>
        <ol>
            <li><strong>{{ $hr->fullname }}</strong>, selaku HR Legal Sect.Head dalam hal ini bertindak untuk dan atas
                nama PT
                Astra Visteon Indonesia, perusahaan yang bergerak di bidang manufaktur perakitan kendaraan bermotor roda
                dua dan roda empat yang berkedudukan hukum di Bogor (selanjutnya disebut <strong>“Pihak
                    Pertama”</strong>), dan</li>
            <li><strong>{{ $kontrak->user->fullname }}</strong> pribadi, yang berdasarkan Kartu Tanda Penduduk (KTP)
                nomor: {{ optional($kontrak->user->employeeDetail)->no_ktp }}
                dengan alamat sesuai KTP di {{ optional($kontrak->user->employeeDetail)->ktp_address }} (selanjutnya
                disebut
                <strong>“Pihak Kedua”</strong>).
            </li>
        </ol>
        <p>Pihak Pertama dan Pihak Kedua (untuk selanjutnya secara bersama-sama disebut juga “Para Pihak atau Kedua
            Belah Pihak” dan masing-masing sebagai “Pihak”), terlebih dahulu menerangkan hal-hal sebagai berikut :</p>
        <ol>
            <li>Bahwa Pihak Pertama telah setuju untuk menerima Pihak Kedua untuk turut serta dalam Program Pemagangan
                di bidang Operator (selanjutnya disebut “Program Magang”) yang diselenggarakan oleh Pihak Pertama.</li>
            <li>Bahwa Pihak Kedua menyetujui untuk bergabung sebagai Peserta Program Magang dalam program yang
                diselenggarakan oleh Pihak Pertama.</li>
        </ol>
        <p>Maka, berdasarkan hal-hal sebagaimana diterangkan di atas Para Pihak sepakat dan setuju untuk membuat dan
            menandatangani perjanjian ini dengan syarat-syarat dan ketentuan-ketentuan sebagai berikut :</p>
        <h2>Pasal 1</h2>
        <ol>
            <li>Program Magang adalah program pelatihan kerja yang diselenggarakan oleh Pihak Pertama yang diperuntukkan
                bagi calon karyawan Pihak Pertama dalam kurun waktu tertentu.</li>
            <li>Pihak Pertama setuju untuk menerima Pihak Kedua sebagai Peserta dalam Program Magang yang
                diselenggarakan oleh Pihak Pertama, dan Pihak Kedua setuju untuk menjadi Peserta dalam Program Magang
                yang diselenggarakan oleh Pihak Pertama tersebut.</li>
            <li>Program Magang yang diselenggarakan oleh Pihak Pertama berlangsung sejak
                tanggal {{ $kontrak->start_date->isoFormat('D MMMM Y') }}, dan akan berakhir pada
                tanggal {{ $kontrak->end_date->isoFormat('D MMMM Y') }}
                (<strong>“Jangka Waktu Program Magang”</strong>).</li>
        </ol>
        <h2>Pasal 2</h2>
        <ol>
            <li>Selama jangka waktu Program Magang, Pihak Pertama wajib membimbing dan menyediakan kurikulum dan
                fasilitas program pelatihan kerja yang menunjang seluruh kegiatan pelatihan kerja bagi Pihak Kedua.</li>
            <li>Selama jangka waktu Program Magang, Pihak Kedua wajib mentaati peraturan dan tata tertib yang berlaku
                dilingkungan Pihak Pertama baik yang tertulis maupun yang tidak tertulis.</li>
            <li>Pihak Kedua wajib mengikuti keseluruhan proses belajar sebaik-baiknya sampai selesai, melaksanakan
                tugas-tugas dan instruksi-instruksi yang diberikan oleh Pihak Pertama dan berprestasi maksimal dalam
                Program Magang yang diselenggarakan oleh Pihak Pertama, dan bersedia untuk melakukan perjalanan dinas
                keseluruh kantor perwakilan Pihak Pertama, bila diperlukan.</li>
        </ol>

        <div style="text-align: right"><span>1</span></div>
        <table>
            <tr>
                <td>
                    <p style="margin-right: 16px">Paraf Pihak Pertama <img
                            src="{{ public_path('storage/' . optional($jobDoc)->first_party_signature) }}"
                            alt="__________" style="Width:auto; height:30px;"></p>
                </td>
                <td>
                    <p>Paraf Pihak Kedua <img
                            src="{{ public_path('storage/' . optional($jobDoc)->second_party_signature) }}"
                            alt="__________" style="Width:auto; height:30px;"></p>
                </td>
            </tr>
        </table>
    </div>

    <div class="page_break"></div>

    {{-- <table>
        <tr>
            <td>
                <div style="margin-right: 188px">
                    <img style="width: 200px" src="{{ public_path('assets/images/logos/logo-avi.svg') }}"
                        alt=" ">
                </div>
            </td>
            <td>
                <div style="text-align: right; width: 100%;"><span>Jl. Lanbau RT 05/10 Kel Karang Asem Barat</span>
                </div>
                <div style="text-align: right;"><span>Kec. Citeureup, Bogot, Indonesia, 16810</span></div>
                <div style="text-align: right;"><span>Telp : +62 21 87919130</span></div>
                <div style="text-align: right;"><span>Telp : +62 21 87919119</span></div>
            </td>
        </tr>
    </table>
    <hr style="border: 0.5px solid black"> --}}
    <div class="container">
        <ol start="4">
            <li>Pihak kedua wajib untuk mempersiapkan diri dan mengikuti kegiatan evaluasi yang diadakan oleh Pihak
                Pertama selama masa Program Magang.</li>
            <li>Pihak Kedua wajib menjaga segala bentuk kerahasiaan dan nama baik perusahaan Pihak Pertama.</li>
            <li>Selama mengikuti Program Magang, Pihak Kedua tidak diperkenankan bekerja pada perusahaan lain, dan/atau
                mengikuti kegiatan lain yang dapat mengganggu proses belajar dan pelaksanaan tugasnya dalam Program
                Magang. Pelanggaran terhadap ketentuan ini berakibat pada terhentinya Program Magang bagi Pihak Kedua
                tanpa syarat apapun.</li>
            <li>Apabila Pihak Kedua telah menyelesaikan seluruh proses pelatihan dalam Program Magang yang
                diselenggarakan oleh Pihak Pertama dan dinyatakan lulus oleh Pihak Pertama, maka Pihak Pertama wajib
                memberikan sertifikat kepada Pihak Kedua.</li>
        </ol>

        <h2>Pasal 3</h2>

        <ol>
            <li>Selama jangka waktu Program Magang, Pihak Pertama akan memberikan kepada Pihak Kedua
                {{ Str::lower(optional($wage)->type ?? 'Uang Saku') }} sebesar
                Rp {{ number_format(optional($wage)->amount ?? 2500000, 0, ',', '.') }}
                {{ optional($wage)->calculation ?? 'per bulan' }} ("{{ optional($wage)->type ?? 'Uang Saku' }}") dan
                akan diperhitungkan secara proporsional apabila Pihak Kedua
                tidak masuk kerja.</li>
            <li>Selain uang saku sebagaimana tersebut dalam ayat (1) pasal ini, selama jangka waktu Program Magang Pihak
                Pertama juga akan memberikan bantuan sebagai berikut :</li>
            <ol style="padding-left: 1rem;" type="a">
                <li>Penyediaan 1 (satu) kali makan siang untuk tiap-tiap hari kehadiran Pihak Kedua pada Program Magang
                    atau dalam bentuk catering; atau</li>
                <li>Penggantian biaya makan di luar kantor diberikan kepada Pihak Kedua apabila tidak ada catering atau
                    pada waktu istirahat siang karena tugas belajar tidak dapat kembali ke tempat Program Magang untuk
                    makan siang sesuai aturan yang berlaku di perusahaan Pihak Pertama; atau</li>
                <li>c. BPJS Ketenagakerjaan (Jaminan Kecelakaan Kerja, Jaminan Kematian dan Jaminan Pemeliharaan
                    Kesehatan) diberikan kepada Pihak Kedua dan akan dibebankan kepada Perusahaan dan penyetoran kepada
                    BPJS Ketenagakerjaan akan dilakukan oleh Pihak Pertama.</li>
            </ol>
            <li>Pihak Pertama wajib untuk mendaftarkan Pihak Kedua dalam program BPJS Ketenagakerjaan dan membayarkan
                iuran BPJS Ketenagakerjaan yang meliputi jaminan kecelakaan kerja, jaminan pemeliharaan kesehatan dan
                kematian, yang akan secara langsung dipotong oleh Pihak Pertama dari Uang Saku yang diterima Pihak
                Kedua.</li>
            <li>Uang Saku yang diterima oleh Pihak Kedua dari Pihak Pertama sebagaimana pasal ini akan dipungut pajak
                berdasarkan ketentuan perpajakan yang berlaku di Indonesia.</li>
            <li>Uang Saku yang diterima oleh Pihak Kedua dari Pihak Pertama berdasarkan Perjanjian ini akan diberikan
                dengan cara mentransfer ke rekening bank Pihak Kedua yang telah didaftarkan kepada Pihak Pertama
                selambat-lambatnya pada akhir bulan. Dalam hal terjadi suatu keadaan mendesak, uang saku dapat
                dibayarkan secara tunai dan wajib diambil sendiri oleh Pihak Kedua di bagian HRD Pihak Pertama.</li>
        </ol>

        <h2 class="text-center">Pasal 4</h2>

        <p>Pihak Pertama berhak menghentikan Program Magang berikut pemberian uang saku dan bantuan terhadap Pihak
            Kedua, tanpa syarat apapun, apabila :</p>
        <ol>
            <li>Pihak Kedua melakukan tindakan pelanggaran:</li>
        </ol>

        <div style="text-align: right"><span>2</span></div>
        <table>
            <tr>
                <td>
                    <p style="margin-right: 16px">Paraf Pihak Pertama <img
                            src="{{ public_path('storage/' . optional($jobDoc)->first_party_signature) }}"
                            alt="__________" style="Width:auto; height:30px;"></p>
                </td>
                <td>
                    <p>Paraf Pihak Kedua <img
                            src="{{ public_path('storage/' . optional($jobDoc)->second_party_signature) }}"
                            alt="__________" style="Width:auto; height:30px;"></p>
                </td>
            </tr>
        </table>
    </div>
    <p style="position:absolute; bottom: 20px;">Auto Generated by System</p>
    <div class="page_break"></div>

    {{-- <table>
        <tr>
            <td>
                <div style="margin-right: 188px">
                    <img style="width: 200px" src="{{ public_path('assets/images/logos/logo-avi.svg') }}"
                        alt=" ">
                </div>
            </td>
            <td>
                <div style="text-align: right; width: 100%;"><span>Jl. Lanbau RT 05/10 Kel Karang Asem Barat</span>
                </div>
                <div style="text-align: right;"><span>Kec. Citeureup, Bogot, Indonesia, 16810</span></div>
                <div style="text-align: right;"><span>Telp : +62 21 87919130</span></div>
                <div style="text-align: right;"><span>Telp : +62 21 87919119</span></div>
            </td>
        </tr>
    </table>
    <hr style="border: 0.5px solid black"> --}}

    <div class="container">

        <ol start="2">
            <ol type="a">
                <li>Terlambat datang ke tempat Program Magang atau tidak absen dalam kurun waktu 5 (lima) hari kerja
                    berturut-turut atau 7 (tujuh) hari kerja tidak berturut-turut dalam satu bulan, atau
                </li>
                <li>Peserta tidak masuk (mangkir) dalam jangka waktu Program Magang tanpa keterangan dan bukti-bukti
                    yang sah selama 3 (tiga) hari kerja berturut-turut atau 5 (lima) hari kerja tidak berturut-turut
                    dalam 1 bulan dan kemudian dianggap telah mengundurkan diri dari Program Magang, atau
                </li>
                <li>Bentuk pelanggaran tingkat V dan VI sebagaimana diatur dalam Perjanjian Kerja Bersama PT Astra
                    Visteon Indonesia yang berlaku.
                </li>
            </ol>
            <li>Pihak Kedua gagal dalam evaluasi penilaian Program Magang yang berlangsung baik di tengah berjalannya
                program ini maupun saat evaluasi akhir.
            </li>
            <li>Pihak Kedua melakukan tindakan melanggar hukum yang diatur menurut hukum yang berlaku di Indonesia.
            </li>
            <li>Dalam hal terjadi keadaan/peristiwa sebagaimana tersebut dalam Pasal ini, Pihak Pertama tidak
                berkewajiban untuk memberikan Sertifikat, Uang Saku dan Bantuan/Tunjangan lainnya untuk sisa waktu yang
                tidak/belum dijalani oleh Pihak Kedua.
            </li>
        </ol>

        <h2 class="text-center">Pasal 5</h2>

        <ol>
            <li>Apabila hasil evaluasi akhir oleh Pihak Pertama atas hasil pendidikan Pihak Kedua selama Jangka Waktu
                Program Magang menunjukan bahwa Pihak Kedua memenuhi kualifikasi yang berlaku di lingkungan Pihak
                Pertama dalam hal penerimaan karyawan, maka Pihak Pertama dapat mengangkat Pihak kedua untuk menjadi
                karyawan Pihak Pertama.</li>
            <li>Dalam hal Pihak Kedua diangkat sebagai karyawan Pihak Pertama, maka penempatan tugas Pihak Kedua
                sepenuhnya menjadi hak Pihak Pertama dan untuk itu Pihak Kedua wajib bersedia ditempatkan baik di PT
                Astra Visteon Indonesia atau anak perusahaannya. </li>
            <li>Pihak Kedua yang diangkat menjadi karyawan bagi Pihak Pertama akan dinyatakan dalam Surat Pengangkatan
                Karyawan. Bagi Pihak Kedua tetap berlaku ketentuan-ketentuan yang ada dalam perjanjian ini dan Peraturan
                Perusahaan/Perjanjian Kerja Bersama yang berlaku.</li>
            <li>Setelah penempatan ditetapkan, maka hal-hal yang berkaitan dengan remunerasi dan benefit yang akan
                diterima oleh Pihak Kedua mengikuti ketentuan yang berlaku di lingkungan Pihak Pertama khususnya di
                tempat penempatan. </li>
            <li>Dalam hal berakhirnya Perjanjian ini, maka Para Pihak sepakat untuk mengesampingkan ketentuan Pasal 1266
                Kitab Undang-Undang Hukum Perdata yang berlaku di Indonesia.</li>
        </ol>

        <h2 class="text-center">Pasal 6</h2>

        <ol>
            <li>Salah satu Pihak dapat menghentikan/memutuskan Perjanjian ini dengan adanya pemberitahuan dalam bentuk
                tertulis 30 (tiga puluh) hari sebelum tanggal pemutusan perjanjian ini.</li>
            <li>Pengakhiran secara sepihak baru berlaku apabila Para Pihak telah menemukan dan sepakat menempuh cara
                terbaik mengenai penyelesaian dan pengakhiran segala hal yang berkaitan dengan Perjanjian ini.</li>
        </ol>

        <h2 class="text-center">Pasal 7</h2>

        <p>Perjanjian ini mulai berlaku sejak ditandatanganinya oleh Para Pihak sampai dengan dipenuhinya seluruh
            kewajiban Para Pihak berdasarkan Perjanjian ini.
        </p>

        <div style="text-align: right"><span>3</span></div>
        <table>
            <tr>
                <td>
                    <p style="margin-right: 16px">Paraf Pihak Pertama <img
                            src="{{ public_path('storage/' . optional($jobDoc)->first_party_signature) }}"
                            alt="__________" style="Width:auto; height:30px;"></p>
                </td>
                <td>
                    <p>Paraf Pihak Kedua <img
                            src="{{ public_path('storage/' . optional($jobDoc)->second_party_signature) }}"
                            alt="__________" style="Width:auto; height:30px;"></p>
                </td>
            </tr>
        </table>
    </div>

    <p style="position:absolute; bottom: 20px;">Auto Generated by System</p>
    <div class="page-break"></div>
    {{-- <table>
        <tr>
            <td>
                <div style="margin-right: 188px">
                    <img style="width: 200px" src="{{ public_path('assets/images/logos/logo-avi.svg') }}"
                        alt=" ">
                </div>
            </td>
            <td>
                <div style="text-align: right; width: 100%;"><span>Jl. Lanbau RT 05/10 Kel Karang Asem Barat</span>
                </div>
                <div style="text-align: right;"><span>Kec. Citeureup, Bogot, Indonesia, 16810</span></div>
                <div style="text-align: right;"><span>Telp : +62 21 87919130</span></div>
                <div style="text-align: right;"><span>Telp : +62 21 87919119</span></div>
            </td>
        </tr>
    </table>
    <hr style="border: 0.5px solid black"> --}}

    <div class="container">
        <h2 class="text-center">Pasal 8</h2>
        <ol>
            <li>Pihak Pertama tidak bertanggung jawab atas semua janji-janji lisan maupun tertulis yang dikeluarkan oleh
                pihak lain/ketiga yang isinya bertentangan dengan ketentuan-ketentuan yang terdapat dalam perjanjian
                ini.</li>
            <li>Hal-hal lain yang belum jelas diatur dalam perjanjian ini akan diputuskan berdasarkan kebijakan Pihak
                Pertama dengan tetap merujuk kepada ketentuan ketenagakerjaan yang berlaku serta peraturan perusahaan
                yang berlaku di lingkungan Pihak Pertama.</li>
            <li>Apabila dalam rangka pelaksanaan Perjanjian ini terdapat perselisihan antara Pihak Pertama dan Pihak
                Kedua, maka Para Pihak sepakat untuk menyelesaikan masalah yang timbul dengan cara musyawarah untuk
                mufakat. Apabila tidak tercapai mufakat dalam musyawarah tersebut maka Para Pihak sepakat untuk
                menyelesaikan masalah tersebut di Pengadilan Negeri wilayah Bogor.</li>
        </ol>
        <p>Demikianlah Perjanjian ini dibuat dan ditandatangani oleh Para Pihak dalam rangkap 2 (dua) dan masing-masing
            mempunyai kekuatan hukum yang sama. Satu untuk Pihak Pertama dan satu lainnya untuk Pihak Kedua.</p>

        <div class="signature" style="margin-right: 240px">
            <div>Pihak Pertama,</div>
            <img src="{{ public_path('storage/' . optional($jobDoc)->first_party_signature) }}"
                alt="Tanda Tangan Pihak Pertama" style="width: auto; height: 60px;">
            <div>{{ $hr->fullname }}</div>
            <div>HR Legal Sect. Head</div>
        </div>
        <div class="signature">
            <div>Pihak Kedua,</div>
            <img src="{{ public_path('storage/' . optional($jobDoc)->second_party_signature) }}"
                alt="Tanda Tangan Pihak Kedua" style="width: auto; height: 60px;">
            <div>{{ $kontrak->user->fullname }}</div>
        </div>

        {{-- <div style="margin-top: 32px" class="approval-section">
            <div>Mengetahui dan Mengesahkan,</div>
            <div>Kepala Dinas Tenaga Kerja</div>
            <div>Kabupaten Bogor</div>
            <div style="height: 60px"></div>
            <div>{{ $disnaker->nama }}</div>
            <div>NIP. {{ $disnaker->nip }}</div>
        </div> --}}

        <div style="text-align: right; margin-top: 240px;"><span>4</span></div>
        <table>
            <tr>
                <td>
                    <p style="margin-right: 16px">Paraf Pihak Pertama <img
                            src="{{ public_path('storage/' . optional($jobDoc)->first_party_signature) }}"
                            alt="__________" style="Width:auto; height:30px;"></p>
                </td>
                <td>
                    <p>Paraf Pihak Kedua <img
                            src="{{ public_path('storage/' . optional($jobDoc)->second_party_signature) }}"
                            alt="__________" style="Width:auto; height:30px;"></p>
                </td>
            </tr>
        </table>
    </div>
    <p style="position:absolute; bottom: 20px;">Auto Generated by System</p>
</body>

</html>
