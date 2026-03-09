<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Leave Application Submitted</title>
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
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .alert-title {
            color: #0369a1;
            font-weight: 600;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
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
        .action-section {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .action-title {
            color: #16a34a;
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
            margin: 5px;
        }
        .btn-secondary {
            background: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 New Leave Application Submitted</h1>
        </div>
        
        <div class="content">
            <div class="alert">
                <div class="alert-title">
                    <i>📬</i> New Leave Application Requires Review
                </div>
                <p>A new leave application has been submitted and requires your attention.</p>
            </div>

            <div class="info-section">
                <div class="info-title">👤 Applicant Information</div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Name</span>
                        <span class="info-value">{{ $user->full_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Department</span>
                        <span class="info-value">{{ $user->department ? $user->department->name : 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Designation</span>
                        <span class="info-value">{{ $user->designation ? $user->designation->name : 'N/A' }}</span>
                    </div>
                </div>
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
                        <span class="info-label">Submitted On</span>
                        <span class="info-value">{{ $leaveApplication->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
                <div style="margin-top: 15px;">
                    <div class="info-label" style="margin-bottom: 5px;">Reason for Leave:</div>
                    <div style="background: white; padding: 10px; border-radius: 4px; border: 1px solid #e2e8f0;">
                        {{ $leaveApplication->reason }}
                    </div>
                </div>
            </div>

            <div class="action-section">
                <div class="action-title">⚡ Required Actions</div>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Review the leave application details</li>
                    <li>Check employee's leave balance availability</li>
                    <li>Approve or reject the application</li>
                    <li>Add remarks if rejecting the application</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('hr.leave-applications.index') }}" class="btn">
                    Review Application
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    Dashboard
                </a>
            </div>
        </div>

        <div class="footer">
            <div class="footer-text">
                <p>This is an automated message from ISU-Flow Leave Management System.</p>
                <p>Please review the application at your earliest convenience.</p>
            </div>
        </div>
    </div>
</body>
</html>
