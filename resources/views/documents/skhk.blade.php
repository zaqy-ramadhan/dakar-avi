<!DOCTYPE html>
<html>

<head>
    <title>Surat Keterangan Selesai Masa Kerja</title>
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
            /* text-decoration: underline; */
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

        tr {
            vertical-align: top;
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
        <h1>SURAT KETERANGAN SELESAI MASA KERJA</h1><br>
        <p>Yang Bertanda tangan di bawah ini :</p>
        <br>
        <table>
            <tr>
                <td>
                    1.
                </td>
                <td style="width:10rem">
                    Pihak Perusahaan
                </td>
                <td>
                    PT. Astra Visteon Indonesia beralamat di Jl. Lanbau Karang Asem Barat diwakili oleh Sdri. {{$hr->fullname}} (HRGA & EHS Department Head), bertindak untuk dan atas nama Perusahaan, selanjutnya disebut Pihak I.
            </tr>

            <tr>
                <td>
                    2.
                </td>
                <td>
                    Pihak Karyawan
                </td>
                <td>
                    Sdr. {{ $kontrak->user->fullname }} (No. KTP : {{ $kontrak->user->employeeDetail->no_ktp ?? '-' }}) selaku karyawan PT. Astra Visteon Indonesia yang beralamat di
                    {{ $kontrak->user->employeeDetail->current_address ?? '-' }}
                </td>
            </tr>
        </table>
        <p>Bahwa pada hari ini, {{ $kontrak->end_date?->isoFormat('D MMMM Y') }} berdasarkan ketentuan Undang-Undang Nomor 2 Tahun 2004, Undang-Undang No.
            13 Tahun 2003, dan Undang-Undang No. 11 Tahun 2020, kedua belah pihak mencari penyelesaian permasalahan
            sebagai berikut :
        </p><br>

        <h2>PENGAKHIRAN HUBUNGAN KERJA</h2>

        <p style="margin-top: 4px">Bahwa berdasarkan musyawarah untuk mencapai mufakat kedua belah pihak sepakat sebagai
            berikut :
        </p>
        <ol>
            <li>Bahwa Pihak I dan Pihak II sepakat untuk mengadakan pengakhiran hubungan kerja terhitung tanggal
                {{ $kontrak->end_date?->isoFormat('D MMMM Y') }} Bahwa Pihak I akan membayarkan gaji dan hak lainnya hingga hari terakhir
                bekerja sesuai dengan tanggal pembayaran yang berlaku di Perusahaan.
            </li>
            <li>Bahwa dengan adanya pengakhiran hubungan kerja ini, Pihak I memberikan uang kompensasi sesuai dengan
                ketentuan dalam peraturan perundang-undangan.
            </li>
            <li>Bahwa Pihak II bersedia menerima hak-hak seperti tersebut pada point (2) di atas dari Pihak I dengan
                sepenuhnya dan mengembalikan seluruh fasilitas dan peralatan kerja milik Pihak I.
            </li>
            <li>
                Bahwa, dengan ditandatanganinya surat ini maka Pihak Kedua menyatakan melepaskan hak atas
                tuntutan dalam bentuk apapun terkait dengan hak-hak lainnya yang timbul dalam hubungan kerja serta
                menyatakan surat yang sebelumnya tidak berlaku lagi.
            </li>
            <li>
                Bahwa dengan dipenuhinya Hak dan Kewajiban masing-masing pihak menurut surat ini, maka
                segala permasalahan yang menyangkut hubungan kerja dinyatakan selesai dan kedua belah pihak saling
                mengamankan isi surat keterangan selesai masa kerja ini dan tidak akan mengajukan tuntutan dalam bentuk apapun
                dikemudian hari.
            </li>
        </ol>

        <p>
            Demikian surat ini dibuat oleh kedua belah pihak dalam keadaan sehat jasmani dan rohani tanpa
            adanya paksaan dari pihak lain, untuk dilaksanakan dengan penuh rasa tanggung jawab
        </p>
        <br>
        <p>Citeureup, {{ $kontrak->end_date?->isoFormat('D MMMM Y') }}</p>
        <div class="signature" style="margin-right: 240px">
            <div class="fw-3" >Pihak I</div>
            <img src="{{ public_path('storage/' . optional($jobDoc)->first_party_signature) }}" alt=" "
                style="width: auto; height: 60px;">
            <div>{{ $hr->fullname }}</div>
            <div>HRGA & EHS Dept. Head</div>
        </div>
        <div class="signature">
            <div class="fw-3">Pihak II</div>
            <img src="{{ public_path('storage/' . optional($jobDoc)->second_party_signature) }}" alt=" "
                style="width: auto; height: 60px;">
            <div>{{ $kontrak->user->fullname }}</div>
        </div>
    </div>
        <p style="position:absolute; bottom: 20px;">Auto Generated by System</p>

</body>

</html>
