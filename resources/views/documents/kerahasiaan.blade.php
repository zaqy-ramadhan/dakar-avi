<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pernyataan Kerahasiaan</title>
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
            text-align: left;
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
            text-align: left;
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

        .space {
            min-width: 2rem;
            text-align: right;
            margin-right: 1rem;
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

        <h1 style="text-decoration: underline">SURAT PERNYATAAN</h1>


        <div class="content">
            <p>Yang bertanda tangan dibawah ini :</p>

            {{-- <p>Nama: <span class="underline"></span></p>
        <p>NPK: <span class="underline"></span></p>
        <p>Jabatan: <span class="underline"></span></p>
        <p>Tempat / tgl lahir: <span class="underline"></span></p>
        <p>Jenis Kelamin: <span class="underline"></span></p>
        <p>Alamat: <span class="underline"></span></p> --}}
            <table style="border-collapse: collapse;">
                <tr>
                    <td style="width: 150px;">
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
                        NPK
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        {{ $kontrak->user->npk }}
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
                        {{ $kontrak->user->firstEmployeeJob->position->position_name ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <td>
                        Tempat / Tanggal Lahir
                    </td>
                    <td>
                        :
                    </td>
                    <td>
                        {{ optional($kontrak->user->employeeDetail)->birth_place . ', ' . \Carbon\Carbon::parse(optional($kontrak->user->employeeDetail)->birth_date)->translatedFormat('d F Y') }}
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
            </table>

            <p>Dengan ini menyatakan sebagai berikut :</p>

            <ol>
                <li>Bahwa saya adalah benar karyawan dari <strong> PT Astra Visteon Indonesia </strong> sejak tanggal
                    <span class="underline"></span>.</li>
                <li>Bahwa saya telah mengetahui dan menyetujui seluruh peraturan yang dibuat oleh perusahaan, baik yang
                    tertuang dalam Peraturan perusahaan maupun peraturan pelaksanaannya.</li>
                <li>Bahwa saya setuju dan dengan ini mengikatkan diri pada perusahaan untuk menjaga kerahasiaan semua
                    <strong style="text-decoration: underline">data/informasi, metode dan dokumen termasuk tapi tidak
                        terbatas pada bidang keuangan,
                        pembukuan/accounting, kepersonaliaan (HRD), teknologi, bahan/data pekerjaan, metode produksi,
                        metode
                        pengolahan dan metode penjualan, metode penyimpanan/warehouse serta kebijakan-kebijakan yang
                        dibuat oleh
                        perusahaan</strong> baik sekarang maupun nanti, pada saat saya tidak lagi menjadi karyawan
                    perusahaan, dan oleh
                    karena itu saya berjanji untuk tidak memberikan, menyertakan atau menyebarluaskan kepada siapapun
                    dengan
                    dalih apapun juga <strong style="text-decoration: underline">dan untuk kepentingan siapapun, tanpa
                        persetujuan tertulis</strong> dari perusahaan. Kecuali
                    karena tugas dan tanggungjawab serta wewenang saya, informasi tersebut saya sampaikan dan berikan
                    untuk
                    kepentingan perusahaan.
                </li>
                <li>Saya bersedia dikenakan denda <strong style="text-decoration: underline"> apabila menghilangkan
                        barang inventaris kantor yang diberikan sesuai
                        dengan SK No. SK.01/BOD-AVI/I/23 tentang Denda Inventaris Perusahaan.</strong></li>
                <li>Saya bersedia untuk <strong style="text-decoration: underline">tidak menggunakan alat komunikasi
                        apapun dalam hal ini Handphone saat bekerja di
                        dalam Ruangan Produksi / Clean Room.</strong></li>
                <li>Dalam hal ini saya melanggar pernyataan diatas maka saya sanggup dituntut secara hukum dimuka
                    Pengadilan
                    baik Perdata maupun Pidana.</li>
            </ol>
        </div>

        <div class="footer">
            <p>Demikian pernyataan ini saya buat dengan sebenar-benarnya tanpa paksaan dari siapapun.</p>

            {{-- <div class="signature-form">
            <p>Hormat saya,</p>
            <br><br><br>
            <p class="underline"></p>
            <p>(Nama Terang)</p>
        </div> --}}
            <div class="signature">
                <p>Cibinong, {{ ($jobDoc->created_at ?? \Carbon\Carbon::now())->isoFormat('D MMMM Y') }}</p>
                <img src="{{ public_path('storage/' . optional($jobDoc)->second_party_signature) }}" alt=" "
                    style="width: auto; height: 60px;">
                <div>{{ $kontrak->user->fullname }}</div>
            </div>
        </div>
    </div>
    <p style="position:absolute; bottom: 20px;">Auto Generated by System</p>

</body>

</html>
