@extends('emails.layout')

@section('content')
<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333;">
    <div style="background-color: #1F4788; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 28px; font-weight: bold;">
            🔔 Pengingat Perbaruan Kontrak Karyawan
        </h1>
        <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">
            {{ now()->locale('id')->format('d F Y') }}
        </p>
    </div>

    <div style="background-color: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px;">
        <div style="max-width: 800px; margin: 0 auto;">
            <!-- Greeting -->
            <p style="font-size: 16px; margin-bottom: 20px;">
                Kepada Yth. <strong>Tim Human Resources</strong>,
            </p>

            <!-- Main Message -->
            <div style="background-color: white; padding: 20px; border-left: 4px solid #1F4788; margin-bottom: 20px; border-radius: 4px;">
                <p style="margin: 0 0 15px 0; font-size: 15px; line-height: 1.6;">
                    Berikut ini adalah daftar karyawan yang <strong style="color: #d9534f;">kontraknya akan berakhir dalam 3 bulan ke depan</strong>. 
                    Mohon segera lakukan koordinasi untuk persiapan perpanjangan kontrak atau proses administrasi lainnya.
                </p>
            </div>

            <!-- Summary Statistics -->
            <div style="background-color: #e8f4f8; padding: 20px; border-radius: 4px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-around; flex-wrap: wrap;">
                    <div style="text-align: center; padding: 10px;">
                        <div style="font-size: 32px; font-weight: bold; color: #1F4788;">{{ $totalEmployees }}</div>
                        <div style="font-size: 13px; color: #666; margin-top: 5px;">Total Karyawan</div>
                    </div>
                    <div style="text-align: center; padding: 10px;">
                        <div style="font-size: 32px; font-weight: bold; color: #5cb85c;">↔️</div>
                        <div style="font-size: 13px; color: #666; margin-top: 5px;">Periode: {{ now()->locale('id')->addMonths(3)->format('F Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Employee Table Summary -->
            <div style="margin-bottom: 20px;">
                <h3 style="color: #1F4788; margin-bottom: 15px; font-size: 16px; border-bottom: 2px solid #1F4788; padding-bottom: 10px;">
                    📋 Ringkasan Data Karyawan
                </h3>
                
                @if($employees->count() > 0)
                    <div style="overflow-x: auto; margin-bottom: 15px;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="background-color: #1F4788; color: white;">
                                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">NPK</th>
                                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Nama Karyawan</th>
                                    <th style="padding: 12px; text-align: left; border: 1px solid #ddd;">Departemen</th>
                                    <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Tanggal Akhir Kontrak</th>
                                    <th style="padding: 12px; text-align: center; border: 1px solid #ddd;">Sisa Hari</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees->take(5) as $employee)
                                    @php
                                        $job = $employee->current_job ?? $employee->employeeJob?->first();
                                    @endphp
                                    <tr style="background-color: {{ $loop->iteration % 2 == 0 ? '#f5f5f5' : 'white' }}; border-bottom: 1px solid #ddd;">
                                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $employee->npk ?? '-' }}</td>
                                        <td style="padding: 10px; border: 1px solid #ddd;"><strong>{{ $employee->name }}</strong></td>
                                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $job?->department?->name ?? '-' }}</td>
                                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd; color: #d9534f; font-weight: bold;">
                                            {{ $job?->end_date?->format('d/m/Y') ?? '-' }}
                                        </td>
                                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">
                                            <span style="background-color: #d9534f; color: white; padding: 4px 8px; border-radius: 3px; font-weight: bold;">
                                                {{ $employee->remaining_days ?? '-' }} hari
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($employees->count() > 5)
                        <div style="background-color: #fff3cd; padding: 12px; border-radius: 4px; color: #856404; font-size: 13px;">
                            ℹ️ Menampilkan 5 karyawan pertama. Silakan lihat file Excel terlampir untuk daftar lengkap ({{ $employees->count() }} karyawan total).
                        </div>
                    @endif
                @else
                    <p style="color: #5cb85c; font-style: italic;">Tidak ada karyawan dengan kontrak yang akan berakhir dalam periode ini.</p>
                @endif
            </div>

            <!-- Important Notes -->
            <div style="background-color: #fef8e7; padding: 15px; border-left: 4px solid #f0ad4e; border-radius: 4px; margin-bottom: 20px;">
                <h4 style="margin-top: 0; color: #8a6d3b;">⚠️ Catatan Penting:</h4>
                <ul style="margin: 10px 0; padding-left: 20px; color: #8a6d3b; font-size: 13px;">
                    <li>Dokumen Excel lengkap terlampir pada email ini</li>
                    <li>Persiapkan dokumen yang diperlukan untuk proses perpanjangan kontrak</li>
                    <li>Pastikan koordinasi dengan pihak terkait dilakukan tepat waktu</li>
                    <li>Hubungi tim HR jika ada pertanyaan atau klarifikasi lebih lanjut</li>
                </ul>
            </div>

            <!-- Call to Action -->
            <div style="background-color: #d4edda; padding: 15px; border-left: 4px solid #28a745; border-radius: 4px; margin-bottom: 20px;">
                <p style="margin: 0; color: #155724; font-size: 14px; line-height: 1.6;">
                    <strong>Tindakan yang diperlukan:</strong> Mohon review data karyawan di atas dan mulai proses persiapan perpanjangan kontrak sesuai dengan kebijakan perusahaan.
                </p>
            </div>

            <!-- Footer -->
            <div style="border-top: 1px solid #ddd; padding-top: 20px; margin-top: 20px;">
                <p style="font-size: 13px; color: #666; margin: 0 0 10px 0;">
                    Email ini dikirim secara otomatis oleh sistem. Jangan membalas email ini. 
                    Jika ada pertanyaan, silakan hubungi <strong>Tim HR</strong>.
                </p>
                <p style="font-size: 12px; color: #999; margin: 0;">
                    Astra Visteon Indonesia • Human Resources Department<br>
                    {{ config('mail.from.address') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
