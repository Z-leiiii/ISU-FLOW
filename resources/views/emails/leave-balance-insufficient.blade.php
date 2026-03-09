<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insufficient Leave Balance</title>
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
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
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
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .alert-title {
            color: #dc2626;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-card {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-title {
            color: #16a34a;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .balance-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .balance-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .balance-label {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 5px;
        }
        .balance-value {
            font-size: 18px;
            font-weight: 600;
            color: #333;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Insufficient Leave Balance</h1>
        </div>
        
        <div class="content">
            <div class="alert">
                <div class="alert-title">
                    <i>⚠️</i> Leave Balance Alert
                </div>
                <p>Hello {{ $user->full_name }},</p>
                <p>We detected an issue with your recent leave application. You requested <strong>{{ $requestedDays }} days</strong> of {{ $leaveType->name }}, but you only have <strong>{{ $availableBalance }} days</strong> available.</p>
            </div>

            <div class="info-card">
                <div class="info-title">📊 Your Leave Balance Details</div>
                <div class="balance-info">
                    <div class="balance-item">
                        <div class="balance-label">Leave Type</div>
                        <div class="balance-value">{{ $leaveType->name }}</div>
                    </div>
                    <div class="balance-item">
                        <div class="balance-label">Requested Days</div>
                        <div class="balance-value">{{ $requestedDays }} days</div>
                    </div>
                    <div class="balance-item">
                        <div class="balance-label">Available Balance</div>
                        <div class="balance-value">{{ $availableBalance }} days</div>
                    </div>
                    <div class="balance-item">
                        <div class="balance-label">Shortfall</div>
                        <div class="balance-value" style="color: #dc2626;">{{ $requestedDays - $availableBalance }} days</div>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <div class="info-title">📋 Computation Information</div>
                <p><strong>Computation Type:</strong> {{ ucfirst(str_replace('_', ' ', $computationDetails['computation_type'])) }}</p>
                @if($computationDetails['has_designation'])
                    <p><strong>Designation:</strong> {{ $computationDetails['designation_name'] }}</p>
                    <p><strong>Monthly Rate:</strong> {{ $computationDetails['monthly_rate'] }} days/month</p>
                @else
                    <p><strong>Annual Rate:</strong> {{ $computationDetails['annual_rate'] }} days/year</p>
                    <p><strong>Conversion Formula:</strong> {{ $computationDetails['conversion_formula'] }}</p>
                @endif
                <p><strong>Service Period:</strong> {{ $computationDetails['months_worked'] }} months worked</p>
            </div>

            <div class="info-card">
                <div class="info-title">💡 What You Can Do</div>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Apply for a shorter duration within your available balance</li>
                    <li>Wait for your leave credits to accumulate (monthly/annual accrual)</li>
                    <li>Submit designation documents for enhanced leave credits (if applicable)</li>
                    <li>Contact HR for special consideration or leave without pay options</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('dashboard.leave-balances') }}" class="btn">
                    View Leave Balances
                </a>
            </div>
        </div>

        <div class="footer">
            <div class="footer-text">
                <p>This is an automated message from ISU-Flow Leave Management System.</p>
                <p>If you have questions, please contact the HR Department.</p>
            </div>
        </div>
    </div>
</body>
</html>
