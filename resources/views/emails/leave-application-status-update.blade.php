<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Application Status Update</title>
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
        .status-card {
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: center;
        }
        .status-approved {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }
        .status-rejected {
            background: #fef2f2;
            border: 1px solid #fecaca;
        }
        .status-pending {
            background: #fefce8;
            border: 1px solid #fde047;
        }
        .status-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        .status-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .status-approved .status-title {
            color: #16a34a;
        }
        .status-rejected .status-title {
            color: #dc2626;
        }
        .status-pending .status-title {
            color: #ca8a04;
        }
        .info-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: #374151;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-label {
            color: #64748b;
            font-size: 14px;
        }
        .info-value {
            font-weight: 500;
            color: #333;
        }
        .remarks-section {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .remarks-title {
            color: #0369a1;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .processor-info {
            margin-top: 10px;
            font-size: 14px;
            color: #64748b;
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
        .btn-secondary {
            background: #64748b;
        }
        .steps-list {
            margin: 0;
            padding-left: 20px;
        }
        .center-text {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Leave Application Status Update</h1>
        </div>
        
        <div class="content">
            <div class="status-card status-{{ $status }}">
                <div class="status-icon">
                    @if($status == 'approved')
                        ✅
                    @elseif($status == 'rejected')
                        ❌
                    @else
                        ⏳
                    @endif
                </div>
                <div class="status-title">
                    Your Leave Application Has Been {{ ucfirst($status) }}
                </div>
                <p>Hello {{ $user->full_name }},</p>
                <p>Your leave application has been reviewed and {{ $status }}.</p>
            </div>

            <div class="info-section">
                <div class="info-title">📄 Application Details</div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Leave Type</span>
                        <span class="info-value">{{ $leaveApplication->leaveType->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Duration</span>
                        <span class="info-value">{{ $leaveApplication->start_date->format('M d, Y') }} - {{ $leaveApplication->end_date->format('M d, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Days Requested</span>
                        <span class="info-value">{{ $leaveApplication->days_requested }} days</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Application Date</span>
                        <span class="info-value">{{ $leaveApplication->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            @if($remarks)
                <div class="remarks-section">
                    <div class="remarks-title">📝 Remarks</div>
                    <p>{{ $remarks }}</p>
                    @if($processedBy)
                        <p class="processor-info">
                            Processed by: {{ $processedBy->full_name }}
                        </p>
                    @endif
                </div>
            @endif

            @if($status == 'approved')
                <div class="info-section">
                    <div class="info-title">✅ Next Steps</div>
                    <ul class="steps-list">
                        <li>Your leave has been approved and recorded</li>
                        <li>Please ensure your work responsibilities are covered</li>
                        <li>Submit any required documentation before your leave</li>
                        <li>Contact your supervisor for any handover arrangements</li>
                    </ul>
                </div>
            @elseif($status == 'rejected')
                <div class="info-section">
                    <div class="info-title">❌ What to Do Next</div>
                    <ul class="steps-list">
                        <li>Review the remarks for rejection reasons</li>
                        <li>Address any issues mentioned in the remarks</li>
                        <li>Submit a new application if needed</li>
                        <li>Contact HR for clarification or assistance</li>
                    </ul>
                </div>
            @endif

            <div class="center-text">
                <a href="{{ route('leave-applications.index') }}" class="btn">
                    View My Applications
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
