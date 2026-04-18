# BookKeeping Platform - Equipment Management System

A comprehensive Laravel-based equipment management system designed to track, loan, maintain, and manage organizational equipment across different users and departments.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Initial Setup](#initial-setup)
- [User Roles and Permissions](#user-roles-and-permissions)
- [Core Modules](#core-modules)
- [Data Models](#data-models)
- [Usage Guide](#usage-guide)
- [API Documentation](#api-documentation)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

---

## Overview

The BookKeeping Platform is an equipment management system built with Laravel 12 that allows organizations to:
- **Track Equipment**: Maintain a comprehensive database of all organizational equipment
- **Manage Loans**: Handle equipment loans with expiration dates and automatic status tracking
- **Monitor Maintenance**: Log and track maintenance records for all equipment
- **Maintain History**: Keep detailed historical records of equipment assignments and loans
- **Role-Based Access**: Enforce different permission levels for Managers and Employees

The system is designed with a modern, responsive user interface using Laravel Breeze authentication and Tailwind CSS styling.

---

## Features

### Equipment Management
- ✅ **Create Equipment**: Add new equipment with detailed specifications (Manager only)
- ✅ **View Equipment**: Browse all equipment with filtering and search capabilities
- ✅ **Edit Equipment**: Update![img.png](img.png) equipment details (Manager only)
- ✅ **Delete Equipment**: Remove equipment from the system (Manager only)
- ✅ **Equipment Status Tracking**: Monitor equipment status (Available, Assigned, Repair, Lost)
- ✅ **Condition Tracking**: Track equipment condition (New, Used, Broken)

### Equipment Loaning
- ✅ **Loan Equipment**: Employees can loan available equipment with due dates
- ✅ **Return Equipment**: Return loaned equipment to available status
- ✅ **Early Return**: Return equipment before the loan expiration date
- ✅ **History Tracking**: Automatic tracking of all loan transactions
- ✅ **Smart Loan Assignment**: Employees can only loan to themselves; Managers can loan to any user

### Equipment Maintenance
- ✅ **Repair Actions**: Log equipment repairs with costs and descriptions
- ✅ **Maintenance Records**: Track all maintenance history
- ✅ **Repair Status**: Equipment status changes to "Repair" when logged for maintenance
- ✅ **Finish Repair**: Complete repair and return equipment to Available/Assigned status

### Equipment History & Audit Trail
- ✅ **Complete History**: Maintain detailed records of all equipment assignments
- ✅ **Loan History**: Track all loans with start and end dates
- ✅ **Employee Assignment**: See which employees have had which equipment
- ✅ **Timeline View**: View complete timeline of equipment movements

### User Management
- ✅ **User Profiles**: Create and manage user profiles with personal information
- ✅ **Role Assignment**: Assign roles (Manager or Employee) to users
- ✅ **Date of Birth**: Track employee date of birth
- ✅ **View Assigned Equipment**: See all equipment currently assigned to a user

### Authentication & Security
- ✅ **User Registration**: New users can create accounts
- ✅ **Email Verification**: Verify user email addresses
- ✅ **Password Hashing**: Secure password storage using bcrypt
- ✅ **Session Management**: Secure session handling
- ✅ **CSRF Protection**: Protection against cross-site request forgery

---

## System Requirements

- **PHP**: 8.2 or higher
- **Laravel**: 12.0 or higher
- **Database**: SQLite (default) or MySQL/PostgreSQL
- **Node.js**: 14.0 or higher (for frontend compilation)
- **Composer**: Latest version
- **npm**: 6.0 or higher

### Required Extensions
- PHP JSON extension
- PHP PDO extension
- PHP Tokenizer extension
- PHP XML extension

---

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd BookKeepingPlatform
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file and configure:
- Database settings
- App name and URL
- Mail configuration (optional)

### 4. Create Database

For SQLite (default):
```bash
touch database/database.sqlite
```

For MySQL, create a database and update `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookkeeping_platform
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Install Frontend Dependencies

```bash
npm install
```

### 6. Run Migrations and Seeders

```bash
php artisan migrate
php artisan db:seed
```

This will:
- Create all required database tables
- Seed initial test data with sample equipment, users, and maintenance records

### 7. Build Frontend Assets

```bash
npm run dev
```

Or for production:
```bash
npm run build
```

### 8. Start Development Server

```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000`

### Quick Setup Command

Alternatively, run the composer setup command:
```bash
composer run setup
```

---

## Initial Setup

### Default Test Users

The database seeder creates the following test users:

**Manager Account:**
- Email: `manager@example.com`
- Password: `password`

**Employee Accounts:**
- Email: `test@example.com`
- Password: `password`

### Create New Users

1. Navigate to Users management (Manager only)
2. Click "Create User"
3. Fill in the required information:
   - First Name
   - Last Name
   - Date of Birth (DD/MM/YYYY format)
   - Email
   - Password
   - Role (Manager or Employee)
4. Click "Create"

### Login

1. Visit `http://127.0.0.1:8000/login`
2. Enter email and password
3. Click "Sign in"

---

## User Roles and Permissions

### Manager Role

Managers have full system access and can:

**Equipment Management:**
- ✅ Create new equipment
- ✅ Edit equipment details
- ✅ Delete equipment
- ✅ View all equipment in the system
- ✅ Repair equipment and mark as "Repair"
- ✅ Finish repairs and return equipment to "Available" or "Assigned" status
- ✅ Loan equipment to any user
- ✅ Log repairs and maintenance

**User Management:**
- ✅ View all users
- ✅ Create new user accounts
- ✅ Edit user information
- ✅ Delete user accounts
- ✅ Assign roles

**Reports & History:**
- ✅ View complete equipment history
- ✅ View all maintenance records
- ✅ Access audit trails
- ✅ View all historical data

**Navigation:**
- Has access to all menu items

### Employee Role

Employees have limited access and can:

**Equipment Management:**
- ✅ View available equipment
- ✅ View equipment assigned to them
- ✅ Loan available equipment (to themselves only)
- ✅ Return equipment they have loaned
- ❌ Create, edit, or delete equipment
- ❌ Repair equipment
- ❌ View equipment history or maintenance records

**Restrictions:**
- Cannot access user management
- Cannot view equipment history
- Cannot access maintenance records
- Cannot modify other users' equipment
- Cannot access manager-only features

**Navigation:**
- Limited menu items (Equipment only)
- History, Maintenance, and Users tabs are hidden

---

## Core Modules

### 1. Equipment Module

**Database Table**: `equipment`

Manages all organizational equipment with comprehensive tracking.

**Key Fields:**
- `id`: Unique identifier
- `brand`: Equipment brand/manufacturer
- `model`: Equipment model number or VIN
- `category`: Type of equipment (Laptop, Computer, Peripherals, Ergonomics)
- `cost`: Purchase price
- `condition`: Current physical condition (New, Used, Broken)
- `status`: Operational status (Available, Assigned, Repair, Lost)
- `acquisition_date`: Date equipment was purchased
- `loan_date`: Current loan start date (if loaned)
- `loan_expire_date`: Current loan expiration date
- `storage_location`: Physical storage location
- `user_id`: Currently assigned user ID (if loaned)

**Status Workflow:**
```
Available (initial) 
    ↓
    ├→ Loan → Assigned
    │          ├→ Return → Available
    │          └→ Early Return → Available
    │
    ├→ Repair (Manager) → Repair
    │                     └→ Finish Repair → Available or Assigned
    │
    └→ Lost (if lost)
```

### 2. Equipment History Module

**Database Table**: `equipment_histories`

Maintains complete audit trail of equipment assignments and loans.

**Key Fields:**
- `id`: Unique identifier
- `equipment_id`: Reference to equipment
- `loan_dates`: Array of loan start dates
- `loan_expire_dates`: Array of corresponding expiration dates
- `user_ids`: Array of user IDs who have borrowed the equipment

**Purpose:**
- Track all past assignments
- Maintain loan history
- Generate equipment usage reports
- Audit trail for compliance

**Data Format:**
Stores arrays of dates and user IDs to track complete history:
```php
[
    'loan_dates' => ['2026-04-10', '2026-04-15', '2026-04-20'],
    'loan_expire_dates' => ['2026-04-20', '2026-04-25', '2026-04-30'],
    'user_ids' => [1, 2, 3]
]
```

### 3. Maintenance Record Module

**Database Table**: `maintenance_records`

Tracks all equipment maintenance, repairs, and service records.

**Key Fields:**
- `id`: Unique identifier
- `equipment_id`: Reference to equipment
- `description`: Maintenance work performed
- `cost`: Maintenance cost
- `maintenance_date`: Date of maintenance

**Purpose:**
- Log all repairs and maintenance
- Track maintenance costs
- Generate service history reports
- Monitor equipment reliability

**Typical Uses:**
- Recording hardware repairs
- Logging software updates
- Tracking professional maintenance services
- Recording preventive maintenance

### 4. User Module

**Database Table**: `users`

Manages user accounts with role-based permissions.

**Key Fields:**
- `id`: Unique identifier
- `name`: First name
- `surname`: Last name
- `dob`: Date of birth (DD/MM/YYYY format)
- `email`: Email address (unique)
- `password`: Hashed password
- `email_verified_at`: Email verification timestamp
- `role`: User role (Manager or Employee)

**Relationships:**
- Has many Equipment records (equipment they have borrowed)
- Can have multiple equipment assignments

---

## Data Models

### Equipment Model

```php
class Equipment {
    - id: integer (primary key)
    - brand: string
    - model: string
    - category: enum (Laptop|Computer|Peripherals|Ergonomics)
    - cost: integer (in currency units)
    - condition: enum (new|used|broken)
    - status: enum (Available|Assigned|Repair|Lost)
    - acquisition_date: date
    - loan_date: nullable date
    - loan_expire_date: nullable date
    - storage_location: string
    - user_id: nullable foreign key (users)
    
    Relationships:
    - belongsTo: User
    - hasMany: MaintenanceRecord
    - hasMany: EquipmentHistory
}
```

### User Model

```php
class User {
    - id: integer (primary key)
    - name: string
    - surname: string
    - dob: string (date of birth)
    - email: string (unique)
    - email_verified_at: nullable timestamp
    - password: string (hashed)
    - role: string (Manager|Employee)
    - created_at: timestamp
    - updated_at: timestamp
    
    Relationships:
    - hasMany: Equipment
    
    Methods:
    - isManager(): bool
    - isEmployee(): bool
}
```

### EquipmentHistory Model

```php
class EquipmentHistory {
    - id: integer (primary key)
    - equipment_id: foreign key (equipment)
    - loan_dates: array (list of loan dates)
    - loan_expire_dates: array (list of expiration dates)
    - user_ids: array (list of user IDs)
    - created_at: timestamp
    - updated_at: timestamp
    
    Relationships:
    - belongsTo: Equipment
}
```

### MaintenanceRecord Model

```php
class MaintenanceRecord {
    - id: integer (primary key)
    - equipment_id: foreign key (equipment)
    - description: text
    - cost: integer
    - maintenance_date: date
    - created_at: timestamp
    - updated_at: timestamp
    
    Relationships:
    - belongsTo: Equipment
}
```

---

## Usage Guide

### For Employees

#### Viewing Available Equipment

1. Login with your employee credentials
2. Click "Equipment" in the navigation menu
3. You will see:
   - Equipment assigned to you
   - Available equipment you can loan
4. Use the search bar to find specific equipment by brand name

#### Loaning Equipment

1. Click on available equipment
2. Click the "Loan" button
3. You will be automatically selected as the recipient
4. Enter the loan expiration date
5. Click "Loan Equipment"
6. The equipment status changes to "Assigned"
7. You will see the equipment in your list with the loan dates

#### Returning Equipment

1. Click on equipment you have loaned
2. Click the "Return" button
3. Enter the return date (defaults to today)
4. Click "Return Equipment"
5. Equipment becomes available again
6. The loan is recorded in the equipment history
7. You can now loan other equipment

#### Viewing Your Profile

1. Click your name in the top-right corner
2. Select "Profile"
3. View your personal information
4. Update your email (if needed)
5. Change your password

### For Managers

#### Equipment Management

##### Creating Equipment

1. Click "Equipment" in the navigation
2. Click "Create Equipment" button
3. Fill in all required fields:
   - Brand: Equipment manufacturer/brand
   - Model: Model number or identifier
   - Category: Select from (Laptop, Computer, Peripherals, Ergonomics)
   - Cost: Purchase price
   - Condition: Select from (New, Used, Broken)
   - Acquisition Date: Date of purchase
   - Storage Location: Where it's stored
4. Click "Create Equipment"

##### Editing Equipment

1. Go to Equipment list
2. Click the "Edit" button for the equipment
3. Update desired fields
4. Click "Save Changes"

##### Deleting Equipment

1. Go to Equipment list
2. Click the "Delete" button
3. Confirm the deletion

##### Equipment Actions

**Loan Equipment:**
1. Click on equipment
2. Click "Loan" button
3. Select user to loan to (not just yourself)
4. Enter loan expiration date
5. Click "Loan Equipment"

**Log Repair:**
1. Only visible if equipment condition is "Broken"
2. Click "Log Repair" button
3. Equipment status changes to "Repair"
4. Wait for fix before pressing "Finish Repair"

**Finish Repair:**
1. Only visible if equipment status is "Repair"
2. Click "Finish Repair" button
3. If equipment is assigned to user, returns to "Assigned"
4. If equipment is not assigned, returns to "Available"

#### User Management

##### Creating Users

1. Click "Users" in the navigation (Manager only)
2. Click "Create User" button
3. Fill in the form:
   - First Name
   - Last Name
   - Date of Birth (DD/MM/YYYY)
   - Email
   - Password
   - Role (Manager or Employee)
4. Click "Create User"

##### Editing Users

1. Go to Users list
2. Click on a user
3. Click "Edit" button
4. Update information
5. Click "Update User"

##### Viewing User Details

1. Go to Users list
2. Click on a user
3. View:
   - Personal information
   - All equipment currently assigned
   - Email and role

##### Deleting Users

1. Go to Users list
2. Click "Delete" button on user card
3. Confirm deletion

#### Maintenance Records

##### Creating Maintenance Records

1. Click "Maintenance" in the navigation
2. Click "Create Maintenance Record" button
3. Fill in:
   - Equipment: Select from dropdown
   - Description: Details of maintenance work
   - Cost: Maintenance cost
   - Maintenance Date: Date performed
4. Click "Create Record"

##### Viewing Maintenance Records

1. Click "Maintenance" in the navigation
2. View all maintenance records
3. Click on a record to see details
4. Equipment brand name is searchable

##### Editing Maintenance Records

1. Go to Maintenance list
2. Click on the record
3. Click "Edit"
4. Update information
5. Click "Save Changes"

#### Equipment History

##### Viewing Equipment History

1. Click "History" in the navigation (Manager only)
2. View all equipment assignments and loans
3. Search by equipment brand name
4. Click on a record to view:
   - Complete loan history with dates
   - All users who have borrowed the equipment
   - Current loan status

##### Equipment History Data

Each history record shows:
- Equipment brand and model
- List of all loan start dates
- Corresponding loan expiration dates
- All user IDs who have borrowed the equipment
- Current status

---

## API Documentation

### Routes

The application provides RESTful routes for all resources:

```
GET    /equipment                 - List all equipment
GET    /equipment/{id}            - View equipment details
POST   /equipment/{id}/loan       - Loan equipment to user
POST   /equipment/{id}/return     - Return equipment
POST   /equipment/{id}/repair     - Log equipment repair (Manager)
POST   /equipment/{id}/finish-repair - Complete repair (Manager)

GET    /users                     - List all users (Manager)
GET    /users/{id}               - View user details (Manager)
POST   /users                    - Create new user (Manager)
PATCH  /users/{id}               - Update user (Manager)
DELETE /users/{id}               - Delete user (Manager)

GET    /equipmentHistory         - List all equipment history (Manager)
GET    /equipmentHistory/{id}    - View history record (Manager)

GET    /maintenanceRecord        - List maintenance records (Manager)
GET    /maintenanceRecord/{id}   - View maintenance record (Manager)
POST   /maintenanceRecord        - Create maintenance record (Manager)
PATCH  /maintenanceRecord/{id}   - Update maintenance record (Manager)
DELETE /maintenanceRecord/{id}   - Delete maintenance record (Manager)

GET    /profile                  - View current user profile
PATCH  /profile                  - Update current user profile
DELETE /profile                  - Delete current user account
```

### Authentication

All routes except login/registration require authentication:

```
POST   /login                    - User login
POST   /register                 - User registration
POST   /forgot-password          - Password reset request
POST   /logout                   - User logout
```

### Response Format

All API responses are in JSON format:

**Success Response:**
```json
{
    "success": true,
    "message": "Action completed successfully",
    "data": { ... }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Error description",
    "errors": { ... }
}
```

---

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/EquipmentControllerTest.php

# Run tests with verbose output
php artisan test --verbose

# Run tests with coverage report
php artisan test --coverage
```

### Test Structure

Tests are organized in two categories:

**Feature Tests** (`tests/Feature/`):
- Controller tests
- End-to-end workflow tests
- Database integration tests

**Unit Tests** (`tests/Unit/`):
- Action tests
- Model tests
- Business logic tests

### Key Test Suites

```
Tests/Feature/
├── EquipmentControllerTest.php
├── UserControllerTest.php
├── EquipmentHistoryControllerTest.php
├── MaintenanceRecordControllerTest.php
├── ProfileTest.php
└── AuthenticationTest.php

Tests/Unit/
├── Actions/
│   ├── LoanEquipmentActionTest.php
│   ├── ReturnEquipmentActionTest.php
│   ├── RepairEquipmentActionTest.php
│   └── FinishRepairActionTest.php
└── Models/
    ├── EquipmentTest.php
    ├── UserTest.php
    └── MaintenanceRecordTest.php
```

### Example Test Cases

**Equipment Loan Test:**
```bash
php artisan test --filter="loan_equipment"
```

**User Creation Test:**
```bash
php artisan test --filter="create_user"
```

**Return Equipment Test:**
```bash
php artisan test --filter="return_equipment"
```

---

## Troubleshooting

### Common Issues and Solutions

#### 1. "No such column" Database Error

**Problem:** Error about missing database columns

**Solution:**
```bash
php artisan migrate:fresh
php artisan db:seed
```

#### 2. Equipment Not Loaning Properly

**Problem:** Equipment status not changing when loaning

**Ensure:**
- Equipment status is "Available"
- Equipment condition is not "Broken"
- You have selected a user to loan to
- Expiration date is in the future

#### 3. Password Hash Error

**Problem:** Cannot decrypt or verify password

**Note:** Passwords are one-way hashed using bcrypt. Cannot be decrypted.

**Solution:** Use password reset:
```bash
php artisan tinker
> $user = User::find(1);
> $user->password = Hash::make('newpassword');
> $user->save();
```

#### 4. Equipment History Not Updating

**Problem:** History not recording loans

**Solution:**
- Check that EquipmentHistory observer is loaded
- Verify database observers are registered in `AppServiceProvider`
- Run migration to ensure table structure is correct

**Debug:**
```bash
php artisan tinker
> Equipment::with('history')->first();
```

#### 5. Role Permissions Not Working

**Problem:** Users accessing pages they shouldn't

**Verify:**
- Middleware is registered in `app/Http/Kernel.php`
- Routes have correct middleware applied
- User role is set correctly (Manager or Employee)

```

#### 8. Email Verification Not Working

**Problem:** Cannot verify email

**Solution:** Check mail configuration in `.env`:
```
MAIL_DRIVER=log  # For testing
```

In production, configure actual mail service (SendGrid, Mailgun, etc.)

---

## Development

### Project Structure

```
BookKeepingPlatform/
├── app/
│   ├── Actions/              # Business logic actions
│   ├── Enums/               # Status, Condition, Category enums
│   ├── Http/
│   │   ├── Controllers/     # Request handlers
│   │   ├── Middleware/      # Auth & permission middleware
│   │   └── Requests/        # Form validation requests
│   ├── Models/              # Database models
│   └── Observers/           # Model event observers
├── database/
│   ├── factories/           # Model factories for testing
│   ├── migrations/          # Database schema
│   └── seeders/             # Database seeders
├── resources/
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   └── views/               # Blade templates
├── routes/                  # Route definitions
└── tests/                   # Test suites
```
```

---

## Security Considerations

- ✅ All passwords are hashed using bcrypt
- ✅ CSRF tokens protect all forms
- ✅ SQL injection prevented through Eloquent ORM
- ✅ XSS protection through Blade templating
- ✅ Role-based access control enforced
- ✅ Email verification for new accounts
- ✅ Secure session management

---

## Support & Documentation

### Laravel Documentation
- [Laravel Official Docs](https://laravel.com/docs)
- [Laravel Database](https://laravel.com/docs/eloquent)
- [Laravel Validation](https://laravel.com/docs/validation)

### Project Contacts
For issues or questions about this project, refer to the project repository or documentation.

---

---

## Changelog

### Version 1.0 (Current)
- Initial release
- Equipment CRUD operations
- Equipment loaning system
- Maintenance record tracking
- Equipment history tracking
- User management with roles
- Authentication with email verification
- Role-based access control
- Comprehensive test suite

---

## Future Enhancements

---

## Contributing

To contribute to this project:

1. Create a feature branch: `git checkout -b feature/new-feature`
2. Commit your changes: `git commit -am 'Add new feature'`
3. Push to the branch: `git push origin feature/new-feature`
4. Submit a pull request

---

**Last Updated:** April 2026
**Version:** 1.0.0

