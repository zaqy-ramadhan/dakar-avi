<!DOCTYPE html>
<html>

<head>
    <title>Paklaring</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: white;
            padding: 2rem;
            font-size: 12px;
            text-align: justify;
            text-justify: inter-word;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            margin-top: 75px;
            background-color: white;
            padding: 1rem;
        }

        .page_break {
            page-break-before: always;
        }

        h1 {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        h2 {
            text-align: center;
            font-size: 14px;
            font-style: italic;
            margin-bottom: 1rem;
        }

        p {
            margin-bottom: 1rem;
        }

        p .right {
            text-align: right;
        }

        ol {
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .to {
            margin-bottom: 3px;
            margin-top: 3px;
        }

        .signature-section {
            margin-bottom: 20px;
        }

        .signature {
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

        .italic {
            font-style: italic;
            padding-bottom: 6px;
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
        <div style="text-align: center">
            <h1 style="text-decoration: underline">SURAT KETERANGAN KERJA</h1>
            <h2>CERTIFICATE of EMPLOYMENT</h2>
            <p>No. {{ $kontrak->nomor }}</p>
        </div>
        <br>
        <p>Dengan ini menerangkan bahwa,</p>
        <p style="font-style: italic">This is to certify that,</p>
        <table>
            <tr>
                <td>
                    Nama <br>
                </td>
                <td class="space">:</td>
                <td>{{ $kontrak->user->fullname }}</td>
            </tr>
            <tr>
                <td class="italic">Name</td>
            </tr>
            <tr>
                <td>NPK</td>
                <td class="space">:</td>
                <td>{{ $kontrak->user->npk }}</td>
            </tr>
            <tr>
                <td class="italic">ID Number</td>
            </tr>
            <tr>
                <td>Tempat, tanggal lahir</td>
                <td class="space">:</td>
                @php
                    $birthPlace = $kontrak->user->employeeDetail->birth_place ?? '-';
                    $birthDate =
                        $kontrak->user->employeeDetail && $kontrak->user->employeeDetail->birth_date
                            ? \Carbon\Carbon::parse($kontrak->user->employeeDetail->birth_date)->isoFormat('D MMMM Y')
                            : '-';
                @endphp
                <td>{{ $birthPlace }}, {{ $birthDate }}</td>
            </tr>
            <tr>
                <td class="italic">Place and date of birth</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td class="space">:</td>
                <td>{{ $kontrak->position->position_name }}</td>
            </tr>
            <tr>
                <td class="italic">Position</td>
            </tr>
            <tr>
                <td>Masa kerja</td>
                <td class="space">:</td>
                <td>
                    {{ $kontrak->start_date ? $kontrak->start_date->isoFormat('D MMMM Y') : '-' }}
                    -
                    @php
                        $endDate = $kontrak->resign_date ?? $kontrak->end_date;
                    @endphp
                    {{ $endDate ? $endDate->isoFormat('D MMMM Y') : '' }}
                </td>
                </td>
            </tr>
            <tr>
                <td class="italic">Period of service</td>
            </tr>
            <tr>
                <td>Alasan berhenti</td>
                <td class="space">:</td>
                <td>{{ optional($offboarding)->reason ?? ' - ' }}</td>
            </tr>
            <tr>
                <td class="italic">Reason for leaving</td>
            </tr>
        </table>
        <p>Kami mengucapkan terima kasih atas segala dedikasi dan kontribusi yang telah diberikan kepada PT Astra
            Visteon Indonesia.</p>
        <p style="font-style: italic;">We would like to take this opportunity to thank you for your dedications and
            contributions to PT. Astra Visteon Indonesia.</p>
        <div class="signature-section">
            <div>Citeureup, @php
                $endDate = $kontrak->resign_date ?? $kontrak->end_date;
            @endphp
                {{ $endDate ? $endDate->isoFormat('D MMMM Y') : '' }}</div>
            <div>Hormat kami,</div>
            <div class="signature">
                <img src="{{ public_path('storage/' . $jobDoc?->first_party_signature) }}" alt=" "
                    style="width: auto; height: 60px;">
                <div>{{ $hr->fullname ?? '-' }}</div>
                <div>HRGA & EHS Dept. Head</div>
            </div>
        </div>
    </div>
    <p style="position:absolute; bottom: 20px;">Auto Generated by System</p>

    <div class="page_break"></div>

    <div class="container">
        <p>No.{{ $kontrak->nomor }}</p>
        <p style="float: right">Citeureup, @php
            $endDate = $kontrak->resign_date ?? $kontrak->end_date;
        @endphp
            {{ $endDate ? $endDate->isoFormat('D MMMM Y') : '' }}</p>
        <br style="clear: both; height: 5px;">
        <div>
            <p class="to">Kepada Yth,</p>
            <p class="to">Kepala Dinas Tenaga Kerja</p>
            <p class="to">Kabupaten Bogor</p>
            <p class="to">Jl. Tegar Beriman</p>
            <p class="to">Cibinong - Bogor</p>
        </div>
        <br>
        <p class="to">Dengan hormat,</p>
        <p class="to">Bersama ini kami PT. Astra Visteon Indonesia, menerangkan bahwa :</p>
        {{-- <p>Karyawan kami yang bernama : {{ $kontrak->user->fullname }}</p>
        <p>Nomer KPJ : xxx</p> --}}
        <table>
            <tr>
                <td>
                    Nama <br>
                </td>
                <td class="space">:</td>
                <td>{{ $kontrak->user->fullname }}</td>
            </tr>
            <tr>
                <td>KPJ</td>
                <td class="space">:</td>
                <td>{{ $kontrak->user->npk }}</td>
            </tr>
        </table>
        <p class="to">Per tanggal @php
            $endDate = $kontrak->resign_date ?? $kontrak->end_date;
        @endphp
            {{ $endDate ? $endDate->isoFormat('D MMMM Y') : '' }}, sudah tidak bekerja / keluar
            dari PT. Astra Visteon Indonesia.</p>
        <p class="to">Demikian kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.</p>
        <br>
        <div class="signature-section">
            <div>Citeureup, @php
                $endDate = $kontrak->resign_date ?? $kontrak->end_date;
            @endphp
                {{ $endDate ? $endDate->isoFormat('D MMMM Y') : '' }}</div>
            <div>Hormat kami,</div>
            <div class="signature">
                <img src="{{ public_path('storage/' . $jobDoc?->first_party_signature) }}" alt=" "
                    style="width: auto; height: 60px;">
                <div>{{ $hr->fullname ?? '-' }}</div>
                <div>HRGA & EHS Dept. Head</div>
            </div>
        </div>
        <p>CC : BPJS Ketenagakerjaan</p>
        </br>
        <p style="position:absolute; bottom: 20px;">Auto Generated by System</p>
    </div>
</body>

</html>
