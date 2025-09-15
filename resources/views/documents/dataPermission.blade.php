<!DOCTYPE html>
<html>

<head>
    <title>Persetujuan Data Pribadi</title>
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
            /* margin-top: 75px; */
            background-color: white;
            padding: 1rem;
            /* border: 1px solid #d1d5db; */
        }

        h1 {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 1px;
        }

        p {
            margin-bottom: 1rem;
            font-size: 12px;
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
            text-align: left;
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
            font-size: 12px;
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
        <h1>FORMULIR PERSETUJUAN DATA PRIBADI </h1>
        <br>
        <p>Saya yang bertanda tangan di bawah ini menyatakan bahwa saya telah membaca dan memahami kebijakan pemrosesan
            data pribadi yang ditetapkan oleh PT Astra Visteon Indonesia (“Perusahaan”), dan dengan ini memberikan
            persetujuan sebagai berikut:</p>
        <br>

        <h2>1. Persetujuan Pemberian Data Pribadi</h2>

        <p>
            Saya memberikan persetujuan kepada Perusahaan untuk mengumpulkan, menggunakan, menyimpan, dan memproses Data
            Pribadi saya sejak tahap melamar kerja hingga apabila saya diterima, dalam masa hubungan kerja dengan
            Perusahaan.
        </p>

        <h2>2. Jenis Data Pribadi yang Dikumpulkan</h2>

        <p>Data Pribadi yang dapat dikumpulkan dan diproses termasuk namun tidak terbatas pada:
        </p>

        <ul>
            <li>Data identitas (nama, tempat/tanggal lahir, NIK, foto, dst.)</li>
            <li>Kontak pribadi (alamat rumah, nomor HP, email pribadi)</li>
            <li>Riwayat pendidikan dan pekerjaan</li>
            <li>Informasi keluarga (status, jumlah tanggungan, dll)</li>
            <li>Informasi rekening bank untuk payroll</li>
            <li>Dokumen pendukung (KTP, KK, NPWP, BPJS, ijazah, dll)</li>
        </ul>

        <h2>3. Tujuan Pemrosesan Data Pribadi</h2>

        <p>Data Pribadi saya akan digunakan oleh Perusahaan untuk tujuan yang sah, termasuk namun tidak terbatas pada:
        </p>

        <ul>
            <li>
                Proses seleksi dan rekrutmen
            </li>
            <li>
                Penyusunan dan pelaksanaan perjanjian kerja
            </li>
            <li>
                Penggajian, tunjangan, dan pemotongan pajak
            </li>
            <li>
                Pendaftaran program BPJS, asuransi, dan fasilitas lainnya
            </li>
            <li>
                Pengelolaan absensi, cuti, dan lembur
            </li>
            <li>
                Pengelolaan absensi, cuti, dan lembur
            </li>
            <li>
                Komunikasi internal dan akses ke sistem Perusahaan
            </li>
            <li>
                Audit internal/eksternal dan pelaporan kepada instansi berwenang
            </li>
            <li>
                Keamanan, kontrol akses area kerja dan sistem informasi
            </li>
            <li>
                Dokumentasi organisasi dan pengelolaan struktur SDM
            </li>
            <li>
                Pengelolaan hubungan industrial
            </li>
            <li>
                Pengarsipan data kerja, termasuk bila karyawan telah menjadi tetap atau mengakhiri hubungan kerja
            </li>
        </ul>

        <h2>4. Hak Akses Perubahan Data Pribadi dalam Sistem HR</h2>

        <p>Saya berhak melakukan perubahan langsung pada data pribadi yang bersifat administratif melalui sistem HR
            (ESS), tanpa perlu persetujuan tambahan, antara lain: </p>

        <ul>
            <li>
                Alamat tempat tinggal </li>
            <li>
                Nomor telepon/handphone </li>
            <li>
                Nomor rekening bank pribadi
            </li>
            <li>
                Data keluarga (istri/suami, anak, tanggungan) </li>
            <li>
                Foto pribadi </li>
        </ul>

        <p>
            Perubahan langsung yang saya lakukan merupakan bentuk tanggung jawab saya dalam menjaga keakuratan data
            pribadi di sistem HR.
        </p>

        <p>
            Untuk data yang bersifat ketenagakerjaan atau berdampak pada hak dan kewajiban perusahaan, saya memahami
            bahwa perubahan hanya dapat dilakukan oleh HR setelah melalui proses verifikasi, antara lain:
        </p>

        <ul>
            <li>
                Jabatan atau posisi kerja </li>
            <li>
                Status kepegawaian (PKWT, PKWTT, Outsourcing) </li>
            <li>
                Data terkait penggajian, tunjangan, dan BPJS
            </li>
            <li>
                Data perpajakan (NPWP, status PTKP)
            </li>
        </ul>

        <p>
            Saya juga menyetujui bahwa segala bentuk perubahan data akan dicatat dalam log sistem sebagai bukti audit.
        </p>

        <h2>5. Penghapusan Data Pribadi</h2>

        <ul>
            <li>
                Bahwa Perusahaan wajib mengirimkan notifikasi kepada Saya atas penghapusan Data Pribadi Saya melalui email
            </li>
            <li>
                Perusahaan akan menghapus Data Pribadi Saya 2 tahun setelah masa hubungan kerja dengan Perusahaan berakhir.
            </li>
        </ul>

        <h2>6. Pengkinian Data Pribadi</h2>

        <ul>
            <li>
                Perusahaan akan memberitahukan dan meminta persetujuan apabila Perusahaan memproses Data Pribadi Saya selain daripada yang disebutkan di dalam Formulir Persetujuan ini.
            </li>
        </ul>

        <h2>7. Data Pribadi adalah Data yang Benar</h2>

        <p>
            Saya menyatakan bahwa Data Pribadi Saya dan dokumen yang Saya berikan adalah sah dan mengandung informasi terkini dan benar.
        </p>

        <br>
        <div class="signature" style="margin-right: 240px">
            <div style="margin-bottom: 5px" >Bogor, {{ Carbon\Carbon::parse($user->created_at)->isoFormat('D MMMM Y') ?? now()->isoFormat('D MMMM Y') }}</div>
            <div>Hormat saya,</div>
            <img src="{{ public_path('storage/' . optional($user)->permission_signature) }}" alt=" "
                style="width: 60px; height: 30px; object-fit:cover; margin-bottom: 50px; margin-top: 30px">
            <div>{{ $user->fullname ?? 'Employee' }}</div>
        </div>
        

    </div>
    <p style="position:absolute; bottom: 20px;">Auto Generated by System</p>
</body>

</html>
