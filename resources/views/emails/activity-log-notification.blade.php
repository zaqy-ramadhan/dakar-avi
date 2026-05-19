<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
        <h2 style="color: #333; margin-top: 0;">
            {{ $actor ? $actor->fullname . ' (' . $actor->npk . ') ' : 'System ' }}{{ $activityLog->note }}
        </h2>
        
        <div style="background-color: white; padding: 20px; border-radius: 8px; margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 12px; font-weight: bold; color: #666; width: 30%;">Activity:</td>
                    <td style="padding: 12px; color: #333;">{{ $activityLog->note }}</td>
                </tr>
                
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 12px; font-weight: bold; color: #666;">By (Actor):</td>
                    <td style="padding: 12px; color: #333;">
                        {{ $actor ? $actor->fullname . ' (' . $actor->npk . ')' : 'System' }}
                    </td>
                </tr>
                
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 12px; font-weight: bold; color: #666;">Employee:</td>
                    <td style="padding: 12px; color: #333;">
                        {{ $employee ? $employee->fullname . ' (' . $employee->npk . ')' : 'N/A' }}
                    </td>
                </tr>
                
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 12px; font-weight: bold; color: #666;">Table:</td>
                    <td style="padding: 12px; color: #333;">{{ $activityLog->table_name }}</td>
                </tr>
                
                <tr style="border-bottom: 1px solid #e9ecef;">
                    <td style="padding: 12px; font-weight: bold; color: #666;">Record ID:</td>
                    <td style="padding: 12px; color: #333;">{{ $activityLog->table_id }}</td>
                </tr>
                
                <tr>
                    <td style="padding: 12px; font-weight: bold; color: #666;">Timestamp:</td>
                    <td style="padding: 12px; color: #333;">{{ $activityLog->created_at->format('d M Y H:i:s') }}</td>
                </tr>
            </table>
        </div>

        <div style="background-color: #e7f3ff; padding: 15px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #2196F3;">
            <small style="color: #666;">
                <strong>Log ID:</strong> {{ $activityLog->id }}<br>
                <strong>System:</strong> AVI Workforce & Onboarding Record Keeper
            </small>
        </div>
    </div>
</div>
