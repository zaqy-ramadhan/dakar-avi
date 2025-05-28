<!DOCTYPE html>
<html>

<head>
    <title>Perjanjian Kontrak</title>
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
            margin-bottom: 1px;
        }

        p {
            margin-bottom: 1rem;
        }

        ol {
            /* list-style-type: decimal; */
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        ul {
            padding-left: 1rem;
        }

        h2 {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-decoration: underline;
        }

        /* .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        } */

        .signature div {
            text-align: left;
        }

        .page_break {
            page-break-before: always;
        }

        li {
            margin-bottom: 2px;
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

        p {
            margin: 0px;
        }

        ol {
            margin: 0px;
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
        <h1 style="text-decoration: underline;">PERJANJIAN KERJA WAKTU TERTENTU</h1>
        <p style="text-align: center;">No. {{ $kontrak->nomor }}</p><br>
        <p>Yang Bertanda tangan di bawah ini :</p>
        <br>
        <table style="border-collapse: collapse;">
            <tr>
                <td style="width: 100px;">
                    Nama
                </td>
                <td>
                    :
                </td>
                <td>
                    {{ $hr->fullname }}
                </td>
            </tr>

            <tr>
                <td>
                    Jabatan
                </td>
                <td>
                    :
                </td>
                <td>
                    HRGA & EHS Dept. Head
                </td>
            </tr>

            <tr>
                <td>
                    Alamat
                </td>
                <td>
                    :
                </td>
                <td>
                    Jl. Lanbau
                    RT 05/10 Kelurahan Karang Asem Barat Kecamatan Citeureup, Kabupaten Bogor
                </td>
            </tr>
        </table>
        <p>bertindak selaku jabatannya, untuk dan atas nama PT Astra Visteon Indonesia,
            selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>
        </p><br>
        <table style="border-collapse: collapse;">
            <tr>
                <td style="width: 100px;">
                    Nama
                </td>
                <td>
                    :
                </td>
                <td>
                    {{ $kontrak->user->fullname }}
                </td>
            </tr>

            <tr>
                <td>
                    Alamat
                </td>
                <td>
                    :
                </td>
                <td>
                    {{ optional($kontrak->user->employeeDetail)->current_address }}
                </td>
            </tr>

            <tr>
                <td>
                    Tanggal Lahir
                </td>
                <td>
                    :
                </td>
                <td>
                    {{ optional($kontrak->user->employeeDetail)->birth_date }}
                </td>
            </tr>

            <tr>
                <td>
                    Jenis Kelamin
                </td>
                <td>
                    :
                </td>
                <td>
                    {{ optional($kontrak->user->employeeDetail)->genders }}
                </td>
            </tr>
        </table>
        <p>bertindak untuk dan atas nama pribadi, selanjutnya disebut sebagai <strong>PIHAK
                KEDUA</strong>
        </p><br>

        <p style="margin-top: 4px">Para Pihak terlebih dahulu menerangkan hal-hal sebagai berikut :
        </p>
        <ol>
            <li>Bahwa Pihak Pertama adalah perusahaan yang bergerak dalam bidang manufaktur industri suku cadang dan
                aksesoris kendaraan bermotor roda dua dan roda empat beserta komponen dan perlengkapannya, untuk itu
                Pihak Pertama membutuhkan pekerja yang penempatannya disesuaikan dengan kebutuhan dan kebijakan Pihak
                Pertama.
            </li>
            <li>Bahwa Pihak Pertama memproduksi barang-barang yang disupply ke manufaktur perakitan kendaraan bermotor
                roda dua dan roda empat selaku pemakai/konsumen Pihak Pertama yang pemesanannya tergantung dari
                permintaan.
            </li>
            <li>Bahwa Pihak Kedua dalam hal ini selaku pekerja telah bersedia menerima pekerjaan dari Pihak Pertama
                untuk jabatan dan macam pekerjaan yang akan disebutkan dibawah.
            </li>
        </ol>

        <p>Berdasarkan keterangan di atas, kedua belah pihak mengadakan <strong>Perjanjian Kerja Waktu
                Tertentu</strong>, dalam mana Pihak kedua terikat dalam hubungan kerja pada Pihak Pertama dengan
            syarat-syarat sebagai berikut :</p>

        <br>
        <h2>Pasal 1. Jabatan dan Macam Pekerjaan</h2>

        <p>Pihak Kedua menerima dan bersedia mengemban dengan baik jabatan dan macam pekerjaannya sebagai <strong
                style="font-style: italic">{{ $kontrak->position->position_name }}</strong>.
        </p>

        <h2>Pasal 2. Lokasi Bekerja</h2>

        <p>Pihak Kedua menerima dan bersedia ditempatkan pada Kantor PT Astra Visteon Indonesia beralamat di Jl. Lanbau
            RT 05/10 Kelurahan Karang Asem Barat Kecamatan Citeureup, Kabupaten Bogor, Indonesia, 16810 selanjutnya
            disebut sebagai tempat awal penerimaan dan bersedia dimutasikan ke bagian/departement lain.</p>

        <h2>Pasal 3. Upah dan Cara Pembayaran</h2>
        <p>Selama terkait dalam hubungan kerja ini, Pihak Kedua menerima dan sepakat untuk menerima imbalan balas jasa,
            selanjutnya disebut upah yang dibayar tunai pada akhir bulan, yaitu berupa :</p>
        <ol type="a">
            @foreach ($wages as $wage)
                @if ($wage->type === 'Tunjangan Makan')
                    <li>{{ $wage->type . ' : ' . number_format($wage->amount, 0, ',', '.') . ' /' . Str::lower($wage->status) . ' ' . $wage->calculation . ' hadir kerja yang disediakan dalam bentuk uang saku/makan' }}
                    </li>
                @elseif($wage->type === 'Tunjangan Transport')
                    <li>{{ $wage->type . ' : ' . number_format($wage->amount, 0, ',', '.') . ' /' . Str::lower($wage->status) . ' ' . $wage->calculation . ' hadir kerja' }}
                    </li>
                @elseif($wage->type === 'Gaji Pokok')
                    <li>{{ $wage->type . ' : ' . number_format($wage->amount, 0, ',', '.') . ' /' . Str::lower($wage->status) . ' ' . $wage->calculation }}
                    </li>
                @else
                    <li>{{ $wage->type . ' : ' . number_format($wage->amount, 0, ',', '.') . ' /' . Str::lower($wage->status) . ' ' . $wage->calculation }}
                    </li>
                @endif
            @endforeach
            <li>Tunjangan pengobatan/perawatan kesehatan kepada Pihak Kedua diberikan dalam bentuk fasilitas BPJS
                Kesehatan sesuai dengan peraturan pemerintah yang berlaku.</li>
            <li>Mengikutsertakan Pihak Kedua dalam program BPJS Ketenagakerjaan dan Kesehatan.</li>
            <li>Pembayaran Gaji dan tunjangan setiap bulannya di transfer melalui Bank.</li>
        </ol>

        <div style="text-align: right"><span>1</span></div>
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
    <div style="height: 2rem"></div>
    <div class="container">

        <h2>Pasal 4. Waktu Berlangsungnya Perjanjian Kerja</h2>

        <ol>
            <li>Perjanjian Kerja Waktu Tertentu ini berlaku mulai dari tanggal
                {{ $kontrak->start_date?->isoFormat('D MMMM Y') }} dan berakhir tanggal
                {{ $kontrak->end_date?->isoFormat('D MMMM Y') }}.
            </li>
            <li>Selama masa berlangsungnya Perjanjian Kerja ini, Pihak Pertama menerima Pihak Kedua dalam status
                single/bujangan (tidak menikah), namun tidak terbatas pada Status Wajib Pajak Pihak Kedua.
            </li>
        </ol>

        <h2>Pasal 5. Jam Kerja</h2>

        <ol>
            <li>Pihak Kedua bersedia melaksanakan pekerjaan diluar jam kerja normal (lembur) atas permintaan Pihak
                Pertama dan untuk itu Pihak Pertama akan memberikan uang lembur.
            </li>
            <li>Pihak Kedua bersedia untuk bekerja sesuai dengan hari kerja dan jam kerja yang diatur dan ditetapkan
                Perusahaan sebagai berikut :
                <ul>
                    <li>Senin sampai dengan Jum’at terbagi dalam Shift, pengaturan kerja shift mengikuti aturan dan
                        ketentuan yang diatur secara tersendiri.</li>
                    <li>Hari Sabtu dan Minggu libur.</li>
                </ul>
            </li>
        </ol>

        <h2>Pasal 6. Hak dan Kewajiban</h2>

        <p>Kedua belah Pihak menerima dan sepakat melakukan dengan baik hak dan kewajibannya atas Pihak lainnya,
            sebagaimana yang terinci dalam Peraturan Perusahaan dan Tata Tertib Perusahaan lainnya serta Peraturan
            Ketenagakerjaan yang berlaku.
        </p>

        <ol>
            <li>Pihak Kedua bersedia mentaati semua peraturan / tata tertib yang sedang berlaku untuk semua karyawan
                dalam Perusahaan.</li>
            <li>Pihak Kedua bersedia mentaati perintah-perintah yang layak dari atasannya dan wajib menjalankan tugas
                dengan sebaik-baiknya dan bertanggung jawab.
            </li>
            <li>Pihak Pertama dapat mengakhiri hubungan kerja tanpa adanya ganti rugi ataupun pesangon apabila ternyata
                Pihak Kedua telah melakukan tindakan-tindakan yang melanggar Peraturan Perusahaan, Norma, Tata Tertib
                dan atau Peraturan-Peraturan serta Undang-Undang yang berlaku.</li>
            <li>Pihak Pertama akan membayar upah / gaji sisa kontrak yang belum berakhir, bila diputuskan hubungan kerja
                itu dengan kemauan Pihak Pertama.</li>
            <li>Pihak Kedua wajib menggunakan pakaian kerja yang telah ditetapkan dan disediakan oleh perusahaan.
            </li>
            <li>Pihak Kedua wajib mentaati semua ketentuan mengenai keselamatan dan kesehatan kerja dan tidak melakukan
                perbuatan yang mengabaikan keselamatan bagi pekerja sendiri dan atau pekerja lainnya</li>
        </ol>

        <h2 class="text-center">Pasal 7. Kerahasiaan</h2>

        <ol>
            <li>Pihak Kedua wajib untuk menjaga kerahasiaan dari seluruh dokumen, data, dan/atau standar operasional
                yang dikategorikan sebagai informasi perusahaan yang bersifat rahasia.
            </li>
            <li>Pihak Kedua dilarang untuk memperbanyak, mengedarkan dan/atau memberikan informasi perusahaan yang
                bersifat rahasia kepada pihak ketiga tanpa izin tertulis dari Pihak Pertama.
            </li>
            <li>Pihak Kedua wajib memenuhi ketentuan dalam ayat 1 dan 2 Pasal ini untuk jangka waktu yang tidak
                terbatas.
            </li>
        </ol>

        <h2 class="text-center">Pasal 8. Berakhirnya Perjanjian</h2>

        <ol>
            <li>
                Perjanjian ini berakhir dalam hal:
                <ol type="a">
                    <li>Tercapainya tanggal berakhirnya Perjanjian sebagaimana diatur dalam Pasal 4 Kesepakatan ini.
                    </li>
                    <li>Pihak Kedua meninggal dunia.</li>
                    <li>Pihak Kedua terlibat tindak pidana.</li>
                    <li>Adanya putusan pengadilan dan/atau putusan atau penetapan lembaga penyelesaian perselisihan
                        hubungan industrial yang telah mempunyai kekuatan hukum tetap.</li>
                    <li>Pihak Kedua mengundurkan diri.</li>
                    <li>Adanya keadaan atau kejadian tertentu yang dicantumkan dalam Peraturan Perusahaan yang dapat
                        menyebabkan berakhirnya hubungan kerja.</li>
                </ol>
            </li>
        </ol>

        <div style="text-align: right"><span>2</span></div>
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
            <li>Apabila karena sesuatu hal Pihak Kedua secara sepihak mengundurkan diri (Memutuskan Hubungan Kerja)
                sebelum masa perjanjian berakhir, maka Pihak Kedua berkewajiban untuk memberitahukan tentang niatnya itu
                secara tertulis 2 (Dua) bulan dimuka.
            </li>
            <li>Apabila Pihak Kedua mengundurkan diri seperti di atas (ayat 1), maka Pihak Pertama tidak memberikan
                ganti rugi / pesangon dan tidak berkewajiban untuk memberikan upah sisa masa kerja yang belum dijalani
                oleh Pihak Kedua.
            </li>
            <li>Pada saat Perjanjian ini berakhir, pemberian uang kompensasi diberikan sesuai dengan ketentuan dalam
                peraturan perundang-undangan.
            </li>
        </ol>

        <h2 class="text-center">Pasal 9. Ketentuan Tambahan</h2>

        <ol>
            <li>Hal-hal lain yang belum tertuang dalam perjanjian ini akan ditambahkan kemudian hari dalam bentuk
                addendum dan atas perjanjian kedua belah pihak, dan penambahan tersebut merupakan satu kesatuan yang
                tidak terpisahkan dengan perjanjian ini kecuali ditentukan lain.</li>
            <li>Perjanjian Kerja Waktu Tertentu ini dinyatakan tidak berlaku lagi terhitung sejak tanggal berakhirnya
                masa kerja yang telah disepakati diatas, kecuali bila ternyata salah satu Pihak tidak dapat memenuhi
                kewajibannya atau alasan-alasan lainnya yang mengakibatkan terputusnya perjanjian kerja ini sebelum
                waktunya, maka segala sesuatunya akan diselesaikan dalam suasana kekeluargaan dengan tetap berlandaskan
                pada Peraturan Ketenagakerjaan yang berlaku.</li>
        </ol>
        <br>
        <p>Demikian Perjanjian Kerja Waktu tertentu ini dibuat atas dasar kesepakatan bersama dan tanpa paksaan dari
            pihak manapun, dibuat rangkap 2 (dua) dan untuk segala akibatnya mereka memilih kedudukan hukum dan tempat
            tinggal tidak berubah di Cibinong, Kabupaten Bogor.
        </p>

        <br>
        <table>
            <tr>
                <td>Ditetapkan di</td>
                <td>:</td>
                <td>Cibinong</td>
            </tr>
            <tr>
                <td>Pada tanggal</td>
                <td>:</td>
                <td>{{ ($jobDoc->created_at ?? \Carbon\Carbon::now())->isoFormat('D MMMM Y') }}</td>
            </tr>
        </table>
        <br>
        <div class="signature" style="margin-right: 240px">
            <div>Pihak Pertama,</div>
            <img src="{{ public_path('storage/' . optional($jobDoc)->first_party_signature) }}" alt=" "
                style="width: auto; height: 60px;">
            <div>{{ $hr->fullname }}</div>
            <div>HRGA & EHS Dept. Head</div>
        </div>
        <div class="signature">
            <div>Pihak Kedua,</div>
            <img src="{{ public_path('storage/' . optional($jobDoc)->second_party_signature) }}" alt=" "
                style="width: auto; height: 60px;">
            <div>{{ $kontrak->user->fullname }}</div>
        </div>

        <div style="text-align: right"><span>3</span></div>

    </div>
    <p style="position:absolute; bottom: 20px;">Auto Generated by System</p>

</body>

</html>
