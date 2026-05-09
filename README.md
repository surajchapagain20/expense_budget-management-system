# Expense and Budget Management System

## Overview
This is a comprehensive web-based application designed to manage corporate expenses, budgeting, and vendor information. Built on a standard LAMP/WAMP stack (PHP, MySQL), it provides role-based access control, detailed analytics, and robust vendor management (KYV - Know Your Vendor) functionalities. The system aims to streamline financial operations and improve organizational transparency.

![Login Screen](assets/images/login.png)

## Key Features

### 1. Dashboard & Analytics
Get real-time visual representations of budget utilization, vendor trends, and expenditure breakdowns. The dashboard provides an at-a-glance overview of the financial health of the organization, helping decision-makers track ongoing expenses against allocated budgets.

![Admin Dashboard](assets/images/dashboard.png)

### 2. Detailed Analysis & Reporting
The system offers in-depth analysis tools. Departmental users can view data restricted to their own units, while administrators have full organizational visibility.
- Export vendor/expense data to CSV/Excel for auditing.
- Dynamic charts and metrics.

![Analysis Page](assets/images/analysis.png)

### 3. Database Backup & Restore
A comprehensive built-in backup management tool available to administrators.
- 1-click database SQL dump.
- Automatic compression into `.zip` archives.
- Graceful fallbacks for different server environments (PowerShell zip, raw `.sql`).
- Download and delete backups directly from the administrative interface.

![Database Backup](assets/images/backup.png)

### 4. Additional Capabilities
- **Budget & Expense Management**: Set, track, and manage departmental budgets along with detailed expenditure tracking.
- **Role-Based Access Control (RBAC)**: Distinct access levels for administrators and standard users.
- **Category Management**: Efficiently organize and search product/expense categories with robust validation.
- **Secure Account Recovery**: Reliable password reset flow with email notification capabilities.

## Tech Stack
- **Backend**: PHP (v8.x recommended)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (Bootstrap 4, Chart.js for analytics, jQuery)
- **Environment**: Compatible with XAMPP, WAMP, or standard Apache/PHP servers

## Project Structure
- `/admin`: Administrative interface and core application modules (Analysis, Dashboard, Vendor Management, etc.)
- `/assets`: Static frontend assets (CSS, JS, images, including UI screenshots)
- `/classes`: PHP classes for application logic and database interactions
- `/database`: Contains database SQL dumps or related structures
- `/plugins`: Third-party plugins and libraries
- `/uploads`: Directory for file uploads
- `config.php`: Database and application configuration settings

## Setup and Installation
1. Place the project files into your web server's document root (e.g., `C:\xampp\htdocs\expense_budget`).
2. Create a MySQL database for the project (e.g., `expense_budget_db`).
3. Import the required SQL schema (`database/expense_budget_db.sql`) into the database.
4. Update the database credentials in `config.php`.
5. Access the application via your web browser (e.g., `http://localhost/expense_budget/`).
6. Default Admin Credentials: Check the database for the default admin user.
