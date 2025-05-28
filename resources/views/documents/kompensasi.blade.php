<!DOCTYPE html>
<html>

<head>
    <title>Surat Kompensasi</title>
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

        .space{
            min-width: 2rem;
            text-align: right;
            margin-right: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        @php
            $no = 0;
        @endphp
        <h1 style="text-decoration: underline" >DATA KOMPENSASI KARYAWAN BARU - INDIVIDUAL</h1>

        <table>
            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Nama</td>
                <td class="space">:</td>
                <td>{{ $kontrak->user->fullname }}</td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Tanggal Lahir</td>
                <td class="space">:</td>
                <td>{{ $kontrak->user->employeeDetail?->birth_date ? \Carbon\Carbon::parse($kontrak->user->employeeDetail->birth_date)->isoFormat('D MMMM Y') : '-' }}</td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Perusahaan</td>
                <td class="space">:</td>
                <td>PT Astra Visteon Indonesia</td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Divisi</td>
                <td class="space">:</td>
                <td>{{ $kontrak->division->division_name ?? '-' }}</td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Departemen</td>
                <td class="space">:</td>
                <td>{{ $kontrak->department->department_name ?? '-' }}</td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Seksi</td>
                <td class="space">:</td>
                <td>{{ $kontrak->section->section_name ?? '-' }}</td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Lini</td>
                <td class="space">:</td>
                <td>{{ $kontrak->line->line_name ?? '-' }}</td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Posisi</td>
                <td class="space">:</td>
                <td>{{ $kontrak->position->position_name ?? '-' }}</td>
            </tr>

             <tr>
                <td>{{ $no +=1 }}</td>
                <td>Alamat Lokasi Kerja</td>
                <td class="space">:</td>
                <td>Jl. Lanbau RT 05/10 Karang Asem Barat Kec. Citeureup </br> Kab. Bogor Jawa Barat - Indonesia 16810</td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>NPK</td>
                <td class="space">:</td>
                <td>{{ $kontrak->user->npk }}</td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Gol./Sub Gol.</td>
                <td class="space">:</td>
                <td>{{ $kontrak->subGolongan->sub_golongan_name ?? '-' }}</td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Tipe Tenaga Kerja / Work Contract</td>
                <td class="space">:</td>
                <td>{{ $kontrak->jobType->job_type_name }}
                </td>
            </tr>

            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Cost Center</td>
                <td class="space">:</td>
                <td>{{ $kontrak->costCenter->cost_center_name ?? '-' }}</td>
            </tr>
    
            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Status Karyawan</td>
                <td class="space">:</td>
                <td>{{ Str::ucfirst($kontrak->job_status) . " " . $kontrak->monthDuration()  }}</td>
            </tr>
    
            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Tanggal Masuk</td>
                <td class="space">:</td>
                <td>{{ $kontrak->user->join_date ? \Carbon\Carbon::parse($kontrak->user->join_date)->isoFormat('D MMMM Y') : '-' }}</td>
            </tr>
    
            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Periode Percobaan/ Kontrak</td>
                <td class="space">:</td>
                <td>Tanggal {{ $kontrak->start_date->isoFormat('D MMMM Y') ?? '-' }} s/d tanggal {{ $kontrak->end_date ? $kontrak->end_date->isoFormat('D MMMM Y') : '-' }}</td>
            </tr>

            @foreach ($wages as $wage)
                <tr>
                    <td>{{ $no +=1 }}</td>
                    <td>{{ $wage->type }}</td>
                    <td class="space">:</td>
                    <td>{{ 'Rp'.$wage->amount.' '.$wage->calculation.' '.$wage->status }}</td>
                </tr>
                
            @endforeach
    
            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Pakaian Kerja</td>
                <td class="space">:</td>
                <td>Disediakan 2 stel/ Tidak Disediakan *)</td>
            </tr>
    
            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Lain lain</td>
                <td class="space">:</td>
                <td></td>
            </tr>
    
            <tr>
                <td>{{ $no +=1 }}</td>
                <td>Kode Jam Kerja</td>
                <td class="space">:</td>
                <td>{{ $kontrak->workHour->work_hour ?? '-' }}</td>
            </tr>

            <tr>
                <td>*) </td>
                <td>Coret yang tidak perlu</td>
            </tr>
        </table>
        
    </div>

    <div style="margin-left: 400px;">
        <div>Citeureup, {{ $kontrak->start_date->isoFormat('D MMMM Y') }}</div>
        <img src="{{ public_path('storage/'.optional($jobDoc)->first_party_signature) }}" alt=" " style="width: auto; height: 60px;">
        <div>{{ $hr->fullname }}</div>
        <div>HR Bisnis Unit</div>
    </div>
    <p style="position:absolute; bottom: 20px;">Auto Generated by System</p>

    <div class="page-break"></div>
</body>

</html>
