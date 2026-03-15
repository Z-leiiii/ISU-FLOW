<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ISU-Flow Leave Management System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Modern Clean Interface */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #ffffff;
            min-height: 100vh;
            color: #333;
        }
        
        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 2rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-name {
            font-weight: 600;
            color: #333;
        }
        
        .user-role {
            font-size: 0.85rem;
            color: #666;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 80px;
            bottom: 0;
            width: 280px;
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem 1rem;
            overflow-y: auto;
            z-index: 999;
        }
        
        .nav-section {
            margin-bottom: 2rem;
        }
        
        .nav-title {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.5rem;
            letter-spacing: 0.05em;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(4px);
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 80px;
            padding: 2rem;
            background: #f8fafc;
            min-height: calc(100vh - 80px);
        }
        
        /* Cards */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
        
        /* Buttons */
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(4, 120, 87, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary:hover {
            background: #475569;
        }
        
        /* Session Messages */
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .alert-info {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        
        /* Form Helpers */
        .inline-form {
            display: inline;
        }
        
        .btn-logout {
            padding: 0.5rem 1rem;
        }
        
        /* Leave Balances Page */
        .page-container {
            margin-bottom: 2rem;
        }
        
        .computation-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .card-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }
        
        .computation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .computation-item {
            background: rgba(4, 120, 87, 0.05);
            border: 1px solid rgba(4, 120, 87, 0.1);
            border-radius: 0.75rem;
            padding: 1rem;
        }
        
        .computation-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #666;
            margin-bottom: 0.5rem;
        }
        
        .computation-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: #333;
        }
        
        .computation-subtext {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        .back-link {
            color: #047857;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .icon-margin {
            margin-right: 0.5rem;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .leave-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .leave-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            background: rgba(4, 120, 87, 0.1);
        }
        
        .leave-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .leave-type-name {
            font-weight: 600;
            color: #333;
        }
        
        .badge-paid {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-unpaid {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .balance-amount {
            font-size: 1.125rem;
            font-weight: 600;
            color: #333;
        }
        
        .balance-positive {
            color: #10b981;
        }
        
        .balance-negative {
            color: #ef4444;
        }
        
        .days-text {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        .computation-badge {
            background: rgba(4, 120, 87, 0.1);
            color: #047857;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-good {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .status-low {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .status-critical {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }
        
        .rules-list {
            list-style: none;
            padding: 0;
        }
        
        .rules-list li {
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .rules-list li:last-child {
            border-bottom: none;
        }
        
        .rule-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .rule-description {
            color: #666;
            font-size: 0.875rem;
        }
        
        /* Empty State */
        .empty-state {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .empty-state-content {
            color: #666;
        }
        
        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .empty-description {
            margin-bottom: 1.5rem;
        }
        
        .empty-list {
            text-align: left;
            max-width: 400px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .empty-list li {
            margin-bottom: 0.5rem;
        }
        
        /* Common Components */
        .content-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .flex-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Text Truncation */
        .text-truncate {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .mb-1 {
            margin-bottom: 0.5rem;
        }
        
        .mb-2 {
            margin-bottom: 1rem;
        }
        
        .mb-3 {
            margin-bottom: 1.5rem;
        }
        
        .mt-1 {
            margin-top: 0.5rem;
        }
        
        .mt-2 {
            margin-top: 1rem;
        }
        
        .mt-3 {
            margin-top: 1.5rem;
        }
        
        /* Employee Management */
        .employee-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .employee-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }
        
        .employee-card:hover {
            transform: translateY(-2px);
        }
        
        .employee-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0 auto 1rem;
        }
        
        .employee-name {
            font-size: 1.125rem;
            font-weight: 600;
            color: #333;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        
        .employee-info {
            color: #666;
            font-size: 0.875rem;
            text-align: center;
        }
        
        /* Leave Applications */
        .application-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .application-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .application-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #333;
        }
        
        .application-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.875rem;
        }
        
        .application-reason {
            background: rgba(4, 120, 87, 0.05);
            border: 1px solid rgba(4, 120, 87, 0.1);
            border-radius: 0.5rem;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .application-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }
        
        /* Status Badges */
        .status-pending {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-approved {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-rejected {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            font-size: 0.875rem;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #047857;
            box-shadow: 0 0 0 3px rgba(4, 120, 87, 0.1);
        }
        
        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            font-size: 0.875rem;
            resize: vertical;
            min-height: 100px;
        }
        
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            font-size: 0.875rem;
        }
        
        /* File Upload */
        .file-upload {
            border: 2px dashed rgba(4, 120, 87, 0.3);
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            background: rgba(4, 120, 87, 0.05);
            transition: all 0.2s ease;
        }
        
        .file-upload:hover {
            border-color: #047857;
            background: rgba(4, 120, 87, 0.1);
        }
        
        .file-upload-icon {
            font-size: 3rem;
            color: #047857;
            margin-bottom: 1rem;
        }
        
        .file-upload-text {
            color: #666;
            margin-bottom: 1rem;
        }
        
        /* Buttons */
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #047857;
            color: #047857;
        }
        
        .btn-outline:hover {
            background: #047857;
            color: white;
        }
        
        /* Utility Classes */
        .w-full {
            width: 100%;
        }
        
        .h-full {
            height: 100%;
        }
        
        .flex {
            display: flex;
        }
        
        .inline-flex {
            display: inline-flex;
        }
        
        .items-center {
            align-items: center;
        }
        
        .justify-center {
            justify-content: center;
        }
        
        .justify-between {
            justify-content: space-between;
        }
        
        .gap-1 {
            gap: 0.25rem;
        }
        
        .gap-2 {
            gap: 0.5rem;
        }
        
        .gap-3 {
            gap: 0.75rem;
        }
        
        .gap-4 {
            gap: 1rem;
        }
        
        .rounded {
            border-radius: 0.25rem;
        }
        
        .rounded-lg {
            border-radius: 0.5rem;
        }
        
        .rounded-xl {
            border-radius: 0.75rem;
        }
        
        .rounded-2xl {
            border-radius: 1rem;
        }
        
        .shadow {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .shadow-lg {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .shadow-xl {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Employee Specific */
        .font-weight-600 {
            font-weight: 600;
        }
        
        .employee-avatar-small {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .employee-avatar-placeholder {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .status-active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-inactive {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-icon {
            padding: 0.5rem;
            border-radius: 0.25rem;
            border: none;
            background: rgba(4, 120, 87, 0.1);
            color: #047857;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .btn-icon:hover {
            background: #047857;
            color: white;
        }
        
        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        
        .btn-danger:hover {
            background: #dc2626;
            color: white;
        }
        
        /* Text Utilities */
        .text-sm {
            font-size: 0.875rem;
        }
        
        .text-gray-600 {
            color: #666;
        }
        
        .employee-id-badge {
            font-family: monospace;
            background: rgba(0,0,0,0.05);
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        
        .department-badge {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.85rem;
        }
        
        /* Padding Utilities */
        .p-8 {
            padding: 2rem;
        }
        
        .text-center {
            text-align: center;
        }
        
        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .table th {
            background: rgba(102, 126, 234, 0.1);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
        }
        
        .table td {
            padding: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .table tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }
        
        /* Badges */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .badge-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .badge-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .badge-info {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @auth
        <!-- Header -->
        <header class="header">
            <div class="logo">ISU-Flow</div>
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->full_name }}</div>
                    <div class="user-role">{{ Auth::user()->roles->first()->name ?? 'User' }}</div>
                </div>
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->first_name, 0, 1)) }}{{ strtoupper(substr(Auth::user()->last_name, 0, 1)) }}
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Sidebar -->
        <nav class="sidebar">
            @if(Auth::user()->hasRole('admin'))
                <div class="nav-section">
                    <div class="nav-title">Admin Menu</div>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('hr.designation-documents.index') }}" class="nav-link {{ request()->routeIs('hr.designation-documents.*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract nav-icon"></i>
                        Designation Documents
                    </a>
                    <a href="{{ route('hr.leave-applications.index') }}" class="nav-link {{ request()->routeIs('hr.leave-applications.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt nav-icon"></i>
                        Leave Applications
                    </a>
                    <a href="{{ route('hr.employees.index') }}" class="nav-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        Employees
                    </a>
                    <a href="{{ route('leave-credits.index') }}" class="nav-link {{ request()->routeIs('leave-credits.*') ? 'active' : '' }}">
                        <i class="fas fa-coins nav-icon"></i>
                        Leave Credits
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        Reports
                    </a>
                </div>
            @elseif(Auth::user()->hasRole('hr'))
                <div class="nav-section">
                    <div class="nav-title">HR Menu</div>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('hr.designation-documents.index') }}" class="nav-link {{ request()->routeIs('hr.designation-documents.*') ? 'active' : '' }}">
                        <i class="fas fa-file-contract nav-icon"></i>
                        Designation Documents
                    </a>
                    <a href="{{ route('hr.leave-applications.index') }}" class="nav-link {{ request()->routeIs('hr.leave-applications.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt nav-icon"></i>
                        Leave Applications
                    </a>
                    <a href="{{ route('leave-credits.index') }}" class="nav-link {{ request()->routeIs('leave-credits.*') ? 'active' : '' }}">
                        <i class="fas fa-coins nav-icon"></i>
                        Leave Credits
                    </a>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        Reports
                    </a>
                </div>
            @else
                <div class="nav-section">
                    <div class="nav-title">Employee Menu</div>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('dashboard.leave-balances') }}" class="nav-link {{ request()->routeIs('dashboard.leave-balances') ? 'active' : '' }}">
                        <i class="fas fa-balance-scale nav-icon"></i>
                        Leave Balances
                    </a>
                    <a href="{{ route('dashboard.leave-history') }}" class="nav-link {{ request()->routeIs('dashboard.leave-history') ? 'active' : '' }}">
                        <i class="fas fa-history nav-icon"></i>
                        Leave History
                    </a>
                    <a href="{{ route('leave-applications.create') }}" class="nav-link {{ request()->routeIs('leave-applications.create') ? 'active' : '' }}">
                        <i class="fas fa-plus nav-icon"></i>
                        Apply for Leave
                    </a>
                    <a href="{{ route('designation-documents.create') }}" class="nav-link {{ request()->routeIs('designation-documents.create') ? 'active' : '' }}">
                        <i class="fas fa-upload nav-icon"></i>
                        Upload Designation
                    </a>
                    <a href="{{ route('leave-applications.index') }}" class="nav-link {{ request()->routeIs('leave-applications.index') ? 'active' : '' }}">
                        <i class="fas fa-file-alt nav-icon"></i>
                        My Applications
                    </a>
                </div>
            @endif
        </nav>
    @endauth

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert-error">
                {{ session('error') }}
            </div>
        @endif
        
        @if(session('info'))
            <div class="alert-info">
                {{ session('info') }}
            </div>
        @endif
        
        @yield('content')
    </main>
</body>
</html>
