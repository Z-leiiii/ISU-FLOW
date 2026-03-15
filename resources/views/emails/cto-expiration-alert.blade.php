<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTO Expiration Alert</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .alert {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .alert-title {
            color: #d97706;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-card {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-title {
            color: #0369a1;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .cto-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .cto-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .cto-label {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 5px;
        }
        .cto-value {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }
        .warning-section {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .warning-title {
            color: #dc2626;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer-text {
            color: #64748b;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 15px;
        }
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        .days-urgent {
            color: #dc2626;
            font-weight: 700;
        }
        .days-warning {
            color: #d97706;
            font-weight: 600;
        }
        .reason-box {
            background: white;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }
        .reason-label {
            margin-bottom: 5px;
        }
        .action-list {
            margin: 0;
            padding-left: 20px;
        }
        .policy-list {
            margin: 0;
            padding-left: 20px;
            font-size: 14px;
        }
        .center-text {
            text-align: center;
        }
        .btn-margin {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ CTO Expiration Alert</h1>
        </div>
        
        <div class="content">
            <div class="alert">
                <div class="alert-title">
                    <i>⚠️</i> Your Compensatory Time Off is Expiring Soon
                </div>
                <p>Hello {{ $user->full_name }},</p>
                <p>This is an important reminder that you have Compensatory Time Off (CTO) hours that will expire soon. Please use them before they expire to avoid losing them.</p>
            </div>

            <div class="info-card">
                <div class="info-title">📊 Your CTO Details</div>
                <div class="cto-details">
                    <div class="cto-item">
                        <div class="cto-label">Remaining Hours</div>
                        <div class="cto-value">{{ $cto->remaining_hours }} hours</div>
                    </div>
                    <div class="cto-item">
                        <div class="cto-label">Expires On</div>
                        <div class="cto-value">{{ $cto->expires_at->format('M d, Y') }}</div>
                    </div>
                    <div class="cto-item">
                        <div class="cto-label">Days Until Expiration</div>
                        <div class="cto-value {{ $cto->days_until_expiration <= 7 ? 'days-urgent' : 'days-warning' }}">
                            {{ $cto->days_until_expiration }} days
                        </div>
                    </div>
                    <div class="cto-item">
                        <div class="cto-label">Original Earned</div>
                        <div class="cto-value">{{ $cto->hours_earned }} hours</div>
                    </div>
                </div>
                <div style="margin-top: 15px;">
                    <div class="reason-label">Reason for CTO:</div>
                    <div class="reason-box">
                        {{ $cto->reason }}
                    </div>
                </div>
            </div>

            @if($cto->days_until_expiration <= 7)
                <div class="warning-section">
                    <div class="warning-title">🚨 Urgent Action Required</div>
                    <p>Your CTO will expire in <strong>{{ $cto->days_until_expiration }} days</strong>. If you don't use these hours, they will be permanently lost.</p>
                    <p>Please contact your supervisor immediately to schedule time off or discuss alternative arrangements.</p>
                </div>
            @endif

            <div class="info-card">
                <div class="info-title">💡 What You Can Do</div>
                <ul class="action-list">
                    <li>Submit a leave application using your CTO hours</li>
                    <li>Coordinate with your supervisor to schedule time off</li>
                    <li>Plan your work schedule to utilize the remaining hours</li>
                    <li>Contact HR if you need assistance with the process</li>
                </ul>
            </div>

            <div class="info-card">
                <div class="info-title">📋 CTO Policy Information</div>
                <ul class="policy-list">
                    <li>CTO hours expire 6 months from the date earned</li>
                    <li>Unused CTO hours are automatically forfeited upon expiration</li>
                    <li>CTO can be used for any type of leave with proper approval</li>
                    <li>Expiration alerts are sent 30 days and 7 days before expiration</li>
                </ul>
            </div>

            <div class="center-text">
                <a href="{{ route('dashboard.cto') }}" class="btn">
                    View My CTO Balance
                </a>
                <a href="{{ route('leave-applications.create') }}" class="btn btn-warning btn-margin">
                    Use CTO Hours
                </a>
            </div>
        </div>

        <div class="footer">
            <div class="footer-text">
                <p>This is an automated message from ISU-Flow Leave Management System.</p>
                <p>If you have questions about CTO usage, please contact the HR Department.</p>
            </div>
        </div>
    </div>
</body>
</html>
