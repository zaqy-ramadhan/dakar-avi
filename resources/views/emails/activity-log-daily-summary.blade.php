@extends('emails.layout')

@section('content')
<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #2c3e50;">📊 Rekap Aktivitas Harian</h2>
        <p style="margin: 10px 0 0 0; font-size: 14px; color: #7f8c8d;">
            <strong>Tanggal:</strong> {{ $summaryDate->format('d F Y') }}
        </p>
    </div>

    <!-- Statistik Ringkas -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">📈 Statistik</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; background-color: #ecf0f1; border: 1px solid #bdc3c7;">
                    <strong>Total Aktivitas</strong>
                </td>
                <td style="padding: 10px; background-color: #e8f8f5; border: 1px solid #bdc3c7; color: #27ae60; font-weight: bold; text-align: center;">
                    {{ $stats['total_activities'] }}
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; background-color: #ecf0f1; border: 1px solid #bdc3c7;">
                    <strong>User yang Aktif</strong>
                </td>
                <td style="padding: 10px; background-color: #e8f8f5; border: 1px solid #bdc3c7; color: #27ae60; font-weight: bold; text-align: center;">
                    {{ $stats['total_users_acted'] }}
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; background-color: #ecf0f1; border: 1px solid #bdc3c7;">
                    <strong>Kategori Aktivitas</strong>
                </td>
                <td style="padding: 10px; background-color: #e8f8f5; border: 1px solid #bdc3c7; color: #27ae60; font-weight: bold; text-align: center;">
                    {{ $stats['categories_count'] }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Detail Aktivitas per Kategori -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px;">📋 Detail Aktivitas</h3>

        @foreach($activities as $category => $logs)
            <div style="margin-bottom: 20px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #3498db; border-radius: 4px;">
                <h4 style="margin-top: 0; color: #2980b9;">
                    {{ $categoryLabels[$category] ?? $category }}
                    <span style="background-color: #3498db; color: white; padding: 2px 8px; border-radius: 3px; font-size: 12px; margin-left: 10px;">
                        {{ $logs->count() }} aktivitas
                    </span>
                </h4>

                <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr style="background-color: #ecf0f1; border-bottom: 2px solid #bdc3c7;">
                            <th style="padding: 8px; text-align: left; font-size: 12px;">Waktu</th>
                            <th style="padding: 8px; text-align: left; font-size: 12px;">User</th>
                            <th style="padding: 8px; text-align: left; font-size: 12px;">Aktivitas</th>
                            <th style="padding: 8px; text-align: left; font-size: 12px;">Karyawan Terkait</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr style="border-bottom: 1px solid #ecf0f1;">
                                <td style="padding: 8px; font-size: 12px;">
                                    {{ $log->created_at->format('H:i:s') }}
                                </td>
                                <td style="padding: 8px; font-size: 12px;">
                                    {{ $log->actor?->fullname ?? 'System' }}
                                    @if($log->actor?->npk)
                                        <br><span style="color: #7f8c8d; font-size: 11px;">({{ $log->actor->npk }})</span>
                                    @endif
                                </td>
                                <td style="padding: 8px; font-size: 12px;">
                                    {{ $log->note }}
                                </td>
                                <td style="padding: 8px; font-size: 12px;">
                                    {{ $log->employee?->fullname ?? '-' }}
                                    @if($log->employee?->npk)
                                        <br><span style="color: #7f8c8d; font-size: 11px;">({{ $log->employee->npk }})</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        {{-- @if($logs->count() > 10)
                            <tr style="background-color: #ecf0f1; border-top: 2px solid #bdc3c7;">
                                <td colspan="4" style="padding: 8px; text-align: center; font-size: 12px; color: #7f8c8d;">
                                    ... dan {{ $logs->count() - 10 }} aktivitas lainnya
                                </td>
                            </tr>
                        @endif --}}
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    <!-- Footer -->
    <div style="border-top: 2px solid #ecf0f1; padding-top: 15px; font-size: 12px; color: #7f8c8d;">
        <p style="margin: 0;">
            📧 Email ini dikirim otomatis setiap hari.<br>
            ⚙️ Jika ingin mengubah kategori yang ditampilkan, hubungi administrator sistem.
        </p>
    </div>
</div>
@endsection
