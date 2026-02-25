# ISU-Flow Leave Management System

A comprehensive web-based leave management system developed for Isabela State University to automate and streamline leave credit management, leave applications, approvals, and attendance monitoring.

## Features

### Core Features
- **Leave Credit Management** – Automatic computation and updating of leave credits
- **Online Leave Application and Approval** – Filing, review, and approval of leave requests
- **Leave Monitoring and Tracking** – Real-time tracking of leave status and history
- **Employee Leave Information** – Viewing available leave balances and usage
- **Attendance and DTR Monitoring** – Integration of daily time records
- **Late and Tardiness Monitoring** – Recording and tracking attendance issues
- **Monetization and Leave Conversion** – Support for leave monetization and related records
- **Reports and Records Management** – Generation of summaries and official leave reports

### System Capabilities
- Role-based access control (Admin, HR, Department Heads, Employees)
- Automated notifications for leave applications and approvals
- Audit logging for all system activities
- Responsive web interface for desktop and mobile devices
- Real-time dashboard with key metrics
- File attachment support for leave documentation

## Technology Stack

- **Backend**: Laravel 12.0 (PHP 8.2+)
- **Frontend**: Blade Templates with Tailwind CSS
- **Database**: MySQL
- **Authentication**: Laravel's built-in authentication with Spatie Laravel Permission
- **File Storage**: Local storage with public disk

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 5.7 or higher
- Node.js and NPM (for frontend assets)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd isu-flow
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit your `.env` file and set up your database connection:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_database=isu_flow
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| System Administrator | admin@isu.edu.ph | password |
| HR Manager | hr@isu.edu.ph | password |
| Department Head (IT) | it.head@isu.edu.ph | password |
| Employee | pedro.g@isu.edu.ph | password |

## System Architecture

### Database Schema
The system uses the following main tables:
- `users` – Employee information and authentication
- `departments` – Organizational structure
- `leave_types` – Types of leave available
- `leave_credits` – Employee leave credit balances
- `leave_applications` – Leave requests and applications
- `leave_approvals` – Approval workflow tracking
- `attendance_records` – Daily time records
- `tardiness_records` – Late arrival tracking
- `leave_monetizations` – Leave conversion requests
- `notifications` – System notifications
- `audit_logs` – Activity logging

### User Roles and Permissions
- **Admin**: Full system access and configuration
- **HR**: Manage leave credits, approve applications, generate reports
- **Department Head**: Review and approve department leave applications
- **Employee**: Apply for leave, view credits, track applications

## Usage Guide

### For Employees
1. **Login** with your assigned credentials
2. **View Dashboard** for overview of leave balance and recent activities
3. **Apply for Leave** by filling out the leave application form
4. **Track Application Status** through the leave applications page
5. **View Leave Credits** to check available balances
6. **Monitor Attendance** records and tardiness

### For HR Staff
1. **Manage Leave Credits** – Assign and adjust employee leave credits
2. **Review Applications** – Process leave requests requiring HR approval
3. **Generate Reports** – Create leave summaries and attendance reports
4. **Monitor System** – Track overall leave usage and trends

### For Department Heads
1. **Review Department Applications** – Approve/reject leave requests
2. **Monitor Team Attendance** – Track department attendance patterns
3. **Manage Department Resources** – Ensure adequate staffing

## Leave Types Supported

The system supports the following leave types as per Philippine government regulations:
- Vacation Leave (VL) – 15 days per year
- Sick Leave (SL) – 15 days per year
- Maternity Leave (ML) – 105 days
- Paternity Leave (PL) – 7 days
- Special Leave Benefits for Women (SLBW) – 2 days
- Solo Parent Leave (SPL) – 7 days
- Emergency Leave (EL) – 3 days
- Compensatory Time Off (CTO) – Variable

## Security Features

- Password hashing using bcrypt
- Session-based authentication
- Role-based access control
- Audit logging for all user actions
- Input validation and sanitization
- CSRF protection

## Support and Maintenance

### Regular Tasks
- Database backups
- System monitoring
- User account management
- Leave credit annual updates
- Report generation

### Troubleshooting
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database connections
- Ensure file permissions are correct
- Clear caches: `php artisan optimize:clear`

## Contributing

When contributing to this project:
1. Follow Laravel coding standards
2. Write clear, documented code
3. Test all changes thoroughly
4. Update documentation as needed

## License

This project is proprietary to Isabela State University. All rights reserved.

## Contact

For support and inquiries:
- ISU IT Department
- Email: it.support@isu.edu.ph
- Phone: (078) 322-1234

---

**Note**: This system is designed specifically for Isabela State University's leave management needs and complies with Philippine government regulations on employee leave benefits.
