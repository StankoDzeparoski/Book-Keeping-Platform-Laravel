# BookKeeping Platform - Complete Documentation

## Project Overview

The BookKeeping Platform is a comprehensive equipment management and tracking system built with Laravel. It provides role-based access control, equipment loaning workflows, maintenance tracking, and repair management for organizations.

**Current Date:** April 17, 2026  
**Framework:** Laravel (PHP)  
**Frontend:** Blade Templates with Tailwind CSS  
**Database:** SQLite

---

## Table of Contents

1. [User Roles & Access Control](#user-roles--access-control)
2. [Equipment Management](#equipment-management)
3. [Equipment Loaning System](#equipment-loaning-system)
4. [Equipment Return System](#equipment-return-system)
5. [Equipment History Tracking](#equipment-history-tracking)
6. [Broken Equipment & Repairs](#broken-equipment--repairs)
7. [Maintenance Records](#maintenance-records)
8. [Database Structure](#database-structure)
9. [Routes & API Endpoints](#routes--api-endpoints)
10. [Technical Implementation](#technical-implementation)
11. [User Workflows](#user-workflows)
12. [File Structure](#file-structure)

---

## User Roles & Access Control

### Role Types

The system supports two primary user roles:

#### 1. **Manager**
- Full administrative access
- Can create, edit, delete equipment
- Can view all equipment including broken items
- Can manage equipment loans and returns
- Can perform repairs and finish repairs
- Can view all users
- Access to maintenance records

#### 2. **Employee**
- Limited access to equipment
- Can only see equipment assigned to them or available equipment
- Cannot see broken equipment
- Can borrow available equipment
- Can return equipment assigned to them
- Cannot perform repairs
- No access to administrative functions

### Access Control Implementation

**File:** `app/Http/Middleware/IsManager.php` (inferred)

Access is controlled via:
- Middleware checks on protected routes
- Authorization checks in controllers
- Conditional rendering in Blade templates

---

## Equipment Management

### Equipment Model

**File:** `app/Models/Equipment.php`

**Attributes:**
| Attribute | Type | Description |
|-----------|------|-------------|
| id | bigint | Primary key |
| brand | string | Equipment brand/manufacturer |
| model | string | Equipment model name |
| category | enum | Category (inferred from project) |
| cost | integer | Purchase cost in dollars |
| condition | enum | Condition: new, used, broken |
| status | enum | Status: Available, Assigned, Repair, Lost |
| acquisition_date | date | Date equipment was acquired |
| loan_date | date | Current loan start date (null if not loaned) |
| loan_expire_date | date | Current loan expiration date |
| storage_location | string | Physical storage location |
| user_id | bigint | Currently assigned user (FK) |
| created_at | timestamp | Record creation timestamp |
| updated_at | timestamp | Record update timestamp |

### Enums

#### Condition Enum
```php
enum Condition: string {
    case NEW = 'new';
    case USED = 'used';
    case BROKEN = 'broken';
}
```

#### Status Enum
```php
enum Status: string {
    case AVAILABLE = 'Available';
    case ASSIGNED = 'Assigned';
    case REPAIR = 'Repair';
    case LOST = 'Lost';
}
```

#### Category Enum
Location: `app/Enums/Category.php`

### Equipment CRUD Operations

**Manager-Only Actions:**
- `POST /equipment` - Create new equipment
- `GET /equipment/{id}/edit` - Edit equipment form
- `PATCH /equipment/{id}` - Update equipment
- `DELETE /equipment/{id}` - Delete equipment

**All Users:**
- `GET /equipment` - View equipment list (filtered by role)
- `GET /equipment/{id}` - View equipment details

### Equipment Visibility Rules

**Managers:** See all equipment

**Employees:** 
- Can see equipment assigned to them
- Can see equipment with status AVAILABLE
- Cannot see equipment with condition BROKEN
- Cannot see REPAIR, LOST status equipment

---

## Equipment Loaning System

### Loan Equipment Action

**File:** `app/Actions/LoanEquipmentAction.php`

**Purpose:** Transfers equipment to a user with loan dates

**Process Flow:**
1. Manager/Employee initiates loan
2. Select user to loan to
3. Enter loan date and expiration date
4. Equipment status changes to ASSIGNED
5. EquipmentHistory is created/updated
6. Equipment is assigned to user

**Key Features:**
- Single save() call triggers observer
- Observer creates/updates EquipmentHistory
- Appends to existing history arrays if record exists
- Tracks all users who have loaned equipment

### Loan Route

**Route:** `POST /equipment/{equipment}/loan`  
**Access:** Manager & Employee  
**Name:** `equipment.loan`

### Loan Validation

```php
$validated = $request->validate([
    'user_id' => 'required|exists:users,id',
    'loan_date' => 'required|date_format:Y-m-d|after_or_equal:today',
    'loan_expire_date' => 'required|date_format:Y-m-d|after:loan_date',
]);
```

**Rules:**
- User must exist in database
- Loan date must be today or future
- Expiration date must be after loan date

### Loan Equipment Modal (Frontend)

**Location:** `resources/views/equipment/index.blade.php`

**Form Fields:**
- User selection dropdown
- Loan date picker
- Loan expiration date picker

**Visibility:** Equipment with status AVAILABLE

---

## Equipment Return System

### Return Equipment Action

**File:** `app/Actions/ReturnEquipmentAction.php`

**Purpose:** Processes equipment returns with conditional status change

**Key Logic:**
```
if return_date = TODAY:
    ├─ Equipment status → AVAILABLE
    ├─ user_id → null
    └─ loan_date → null

if return_date > TODAY (Future Return):
    └─ Equipment status → ASSIGNED (unchanged)
    └─ Only loan_expire_date updated
```

**Process Flow:**
1. Equipment is found and refreshed from database
2. Equipment updated with saveQuietly() to prevent duplicate history
3. EquipmentHistory updated with actual return date
4. Final refresh ensures latest data

### Return Route

**Route:** `POST /equipment/{equipment}/return`  
**Access:** Manager & assigned user  
**Name:** `equipment.return`

### Return Validation

```php
$maxReturnDate = $equipment->loan_expire_date->format('Y-m-d');
$validated = $request->validate([
    'return_date' => 'required|date_format:Y-m-d|before_or_equal:' . $maxReturnDate,
]);
```

**Rules:**
- Return date must be valid date
- Return date cannot exceed original loan expiration date

### Return Equipment Modal (Frontend)

**Location:** `resources/views/equipment/index.blade.php`

**Form Fields:**
- Return date picker

**Visibility:** Equipment with status ASSIGNED (for manager or assigned user)

### Early Return Workflow

**Scenario:** Equipment loaned 4/17-4/30, returned 4/20

**Result:**
- Equipment status: ASSIGNED (stays assigned if user has equipment)
- Equipment loan_expire_date: 4/20 (updated)
- EquipmentHistory loan_expire_date: ['2026-04-20'] (updated)
- Equipment still assigned to user until actual return

---

## Equipment History Tracking

### EquipmentHistory Model

**File:** `app/Models/EquipmentHistory.php`

**Attributes:**
| Attribute | Type | Description |
|-----------|------|-------------|
| id | bigint | Primary key |
| equipment_id | bigint | Foreign key to equipment |
| user_ids | json | Array of user IDs who loaned equipment |
| loan_date | json | Array of loan start dates |
| loan_expire_date | json | Array of actual/scheduled return dates |
| created_at | timestamp | Record creation timestamp |
| updated_at | timestamp | Record update timestamp |

### Storage Format

All date fields are stored as **JSON arrays** to track multiple loans:

```php
{
    equipment_id: 1,
    user_ids: [5, 3, 5],                               // Multiple users, including repeats
    loan_date: ['2026-04-17', '2026-04-20', '2026-04-22'],
    loan_expire_date: ['2026-04-20', '2026-05-01', '2026-05-05']
}
```

### EquipmentObserver

**File:** `app/Observers/EquipmentObserver.php`

**Triggered Events:**
- `created()` - Sets default status to AVAILABLE
- `updated()` - Creates/updates EquipmentHistory when status changes to ASSIGNED

**Logic:**
1. Detects when equipment status changes to ASSIGNED
2. Creates new history record if none exists
3. Appends to existing arrays if record exists
4. Uses forceFill() for JSON attribute updates

### History Creation Flow

**First Loan:**
```php
MaintenanceRecord created with:
- user_ids: [5]
- loan_date: ['2026-04-17']
- loan_expire_date: ['2026-04-30']
```

**Second Loan (same equipment):**
```php
MaintenanceRecord updated:
- user_ids: [5, 3]
- loan_date: ['2026-04-17', '2026-04-20']
- loan_expire_date: ['2026-04-20', '2026-05-10']
```

### History Display

**Location:** Equipment show page and equipment details  
**Shows:** All loan transactions with user IDs, dates, and return dates

---

## Broken Equipment & Repairs

### Broken Equipment Visibility

**Manager:** Can see all equipment including broken items

**Employee:** 
- Cannot see equipment with condition BROKEN
- Broken equipment filtered from equipment list
- Employees see only: Own equipment + Available equipment

**Filter Logic:**
```php
if (employee && !manager) {
    $query->where('condition', '!=', Condition::BROKEN->value);
}
```

### Repair Action Button

**Visibility:** Manager only

**Shows when:**
- Equipment condition = BROKEN
- Equipment status ≠ REPAIR

**Location:** 
- Equipment detail page (header actions)
- Equipment index table (actions column)

**Color:** Orange text (text-orange-600)

### Repair Equipment Action

**File:** `app/Actions/RepairEquipmentAction.php`

**Purpose:** Create or update maintenance record and change status to REPAIR

**Process Flow:**
1. Equipment status changes to REPAIR
2. Creates new MaintenanceRecord if none exists
3. Updates existing MaintenanceRecord if one exists
4. Appends repair description and date to arrays
5. Increments cumulative cost

**Returns:** Updated MaintenanceRecord

### Repair Route & Form

**Route:** `POST /equipment/{equipment}/repair`  
**Access:** Manager only  
**Name:** `equipment.repair`

**Form Fields:**
- Repair Description (textarea, required, min 3 chars)
- Repair Cost (number, required, min 1)
- Maintenance Date (date, required)

**Modal:** 
- Location: Index and Show pages
- Can close via Cancel or clicking outside
- Form clears on each open
- Dark mode support

### Repair Workflow

**Step 1: Log Repair**
```
Equipment state: BROKEN, AVAILABLE
↓
Manager clicks "Repair" button
↓
Modal form opens
```

**Step 2: Submit Repair**
```
Enter: Description, Cost, Date
↓
Click "Log Repair"
↓
Equipment status → REPAIR
MaintenanceRecord created/updated
```

**Step 3: View Status**
```
Equipment status badge: REPAIR
"Repair" button → Hidden
"Finish Repair" button → Visible
```

### Finish Repair Action

**File:** `app/Actions/FinishRepairAction.php`

**Purpose:** Complete repair, update status and condition

**Logic:**
```
if user_id assigned:
    status → ASSIGNED
else:
    status → AVAILABLE
condition → USED
```

**Process Flow:**
1. Check if equipment has assigned user
2. Set new status based on assignment
3. Update condition to USED (repaired equipment)
4. Refresh equipment

**Returns:** Updated Equipment

### Finish Repair Route

**Route:** `POST /equipment/{equipment}/finish-repair`  
**Access:** Manager only  
**Name:** `equipment.finishRepair`

**Action:** Form POST with confirmation dialog

### Finish Repair Workflow

**Step 1: Finish Button**
```
Equipment status: REPAIR
↓
Manager clicks "Finish Repair"
↓
Confirmation dialog appears
```

**Step 2: Confirm**
```
Click "Confirm"
↓
Equipment status updated
Equipment condition → USED
```

**Step 3: Result**
```
If assigned: status = ASSIGNED, condition = USED
If not assigned: status = AVAILABLE, condition = USED
```

---

## Maintenance Records

### MaintenanceRecord Model

**File:** `app/Models/MaintenanceRecord.php`

**Attributes:**
| Attribute | Type | Description |
|-----------|------|-------------|
| id | bigint | Primary key |
| equipment_id | bigint | Foreign key to equipment |
| description | json | Array of repair descriptions |
| maintenance_date | json | Array of maintenance dates |
| cost | integer | Total cumulative cost of all repairs |
| created_at | timestamp | Record creation timestamp |
| updated_at | timestamp | Record update timestamp |

### Storage Format

JSON arrays track all repairs for equipment:

```php
{
    equipment_id: 1,
    description: ["Screen replaced", "Battery replaced"],
    maintenance_date: ["2026-04-10", "2026-04-15"],
    cost: 350  // Total: 150 + 200
}
```

### MaintenanceRecord Display

**Location:** Equipment show page  
**Shows:** 
- Total cost of all repairs
- Dates of all maintenance work
- Descriptions of repairs performed

### Maintenance Observer

**File:** `app/Observers/MaintenanceRecordObserver.php`

Currently empty (ready for future audit logging)

---

## Database Structure

### Tables

#### `equipment` table
```sql
CREATE TABLE equipment (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    brand VARCHAR(255),
    model VARCHAR(255),
    category VARCHAR(255),
    cost INTEGER,
    condition VARCHAR(255),
    status VARCHAR(255),
    acquisition_date DATE,
    loan_date DATE NULL,
    loan_expire_date DATE NULL,
    storage_location VARCHAR(255),
    user_id BIGINT NULL REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### `equipment_histories` table
```sql
CREATE TABLE equipment_histories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    equipment_id BIGINT REFERENCES equipment(id) ON DELETE CASCADE,
    user_ids JSON,
    loan_date JSON,
    loan_expire_date JSON,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### `maintenance_records` table
```sql
CREATE TABLE maintenance_records (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    equipment_id BIGINT REFERENCES equipment(id) ON DELETE CASCADE,
    description JSON,
    maintenance_date JSON,
    cost INTEGER,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### `users` table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    surname VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    role VARCHAR(255),
    password VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## Routes & API Endpoints

### Authentication Routes

**File:** `routes/auth.php`

- `GET /register` - Registration form
- `POST /register` - Register user
- `GET /login` - Login form
- `POST /login` - Login user
- `POST /logout` - Logout user

### Public Routes

- `GET /` - Welcome page
- `GET /dashboard` - Dashboard (authenticated users)

### Equipment Routes

**All Authenticated Users:**
```
GET    /equipment              → equipment.index (view list)
GET    /equipment/{id}         → equipment.show (view details)
POST   /equipment/{id}/loan    → equipment.loan (loan equipment)
POST   /equipment/{id}/return  → equipment.return (return equipment)
```

**Manager Only:**
```
GET    /equipment/create       → equipment.create (create form)
POST   /equipment              → equipment.store (store new)
GET    /equipment/{id}/edit    → equipment.edit (edit form)
PATCH  /equipment/{id}         → equipment.update (update)
DELETE /equipment/{id}         → equipment.destroy (delete)
POST   /equipment/{id}/repair  → equipment.repair (start repair)
POST   /equipment/{id}/finish-repair → equipment.finishRepair (finish repair)
```

### Other Routes

**Manager Only:**
```
GET/POST/PATCH/DELETE /equipmentHistory/* → Equipment history management
GET/POST/PATCH/DELETE /maintenanceRecord/* → Maintenance records management
GET/POST/PATCH/DELETE /users/*             → User management
```

**Profile:**
```
GET   /profile        → profile.edit
PATCH /profile        → profile.update
DELETE /profile       → profile.destroy
```

---

## Technical Implementation

### Actions (Commands)

Actions are service classes that encapsulate business logic:

#### LoanEquipmentAction
- **File:** `app/Actions/LoanEquipmentAction.php`
- **Purpose:** Handle equipment loaning
- **Method:** `execute(Equipment $equipment, User $user, string $loanDate, string $loanExpireDate): Equipment`

#### ReturnEquipmentAction
- **File:** `app/Actions/ReturnEquipmentAction.php`
- **Purpose:** Handle equipment returns with conditional status
- **Method:** `execute(Equipment $equipment, string $returnDate): Equipment`

#### RepairEquipmentAction
- **File:** `app/Actions/RepairEquipmentAction.php`
- **Purpose:** Log equipment repairs
- **Method:** `execute(Equipment $equipment, string $description, int $cost, string $maintenanceDate): MaintenanceRecord`

#### FinishRepairAction
- **File:** `app/Actions/FinishRepairAction.php`
- **Purpose:** Complete equipment repairs
- **Method:** `execute(Equipment $equipment): Equipment`

### Observers (Event Listeners)

Observers react to model events:

#### EquipmentObserver
- `created()` - Sets default status to AVAILABLE
- `updated()` - Creates/updates EquipmentHistory on status changes

#### MaintenanceRecordObserver
- Empty (ready for future audit logging)

#### EquipmentHistoryObserver
- Empty (ready for future event handling)

#### UserObserver
- Empty (ready for future user tracking)

### Request Validation

**Equipment Store Request:**
- `app/Http/Requests/Equipment/EquipmentStoreRequest.php`

**Equipment Update Request:**
- `app/Http/Requests/Equipment/EquipmentUpdateRequest.php`

**Maintenance Record Requests:**
- `app/Http/Requests/MaintenanceRecord/MaintenanceRecordStoreRequest.php`
- `app/Http/Requests/MaintenanceRecord/MaintenanceRecordUpdateRequest.php`

### Controllers

**EquipmentController:**
- Handles all equipment CRUD operations
- Implements loan, return, repair, finishRepair actions
- Filters equipment based on user role
- Manages modals and form submissions

**Other Controllers:**
- EquipmentHistoryController
- MaintenanceRecordController
- UserController
- ProfileController

### Frontend Components

**Blade Templates:**
- `resources/views/equipment/index.blade.php` - Equipment list with modals
- `resources/views/equipment/show.blade.php` - Equipment detail page with modals
- `resources/views/equipment/create.blade.php` - Create equipment form
- `resources/views/equipment/edit.blade.php` - Edit equipment form

**Modals in Index Page:**
- Loan Equipment Modal
- Return Equipment Modal
- Repair Equipment Modal

**Modals in Show Page:**
- Repair Equipment Modal (with full form)

**Styling:**
- Tailwind CSS for responsive design
- Dark mode support throughout
- Consistent color scheme:
  - Blue: View action
  - Green: Edit/Success actions
  - Red: Delete action
  - Orange: Repair/Important actions
  - Yellow: Secondary actions

---

## User Workflows

### Manager Workflow: Create & Loan Equipment

**Step 1: Create Equipment**
```
1. Navigate to Equipment Management
2. Click "+ New Equipment"
3. Fill in equipment details
4. Click "Create Equipment"
```

**Step 2: Loan Equipment**
```
1. View equipment in list or detail page
2. Click "Loan" button
3. Select employee to loan to
4. Enter loan date and expiration date
5. Click "Loan Equipment"
→ Equipment status: ASSIGNED
→ EquipmentHistory: Created/Updated
```

### Manager Workflow: Equipment Return & Repair

**Step 1: Employee Returns Equipment**
```
1. Equipment shows status: ASSIGNED
2. Employee or manager clicks "Return"
3. Enter return date (can be earlier than due date)
4. Click "Return Equipment"
→ If return date = TODAY: status → AVAILABLE
→ If return date > TODAY: status → ASSIGNED, loan_expire_date updated
→ EquipmentHistory: loan_expire_date updated
```

**Step 2: Equipment Breaks (if broken)**
```
1. Manager marks equipment as BROKEN (via edit)
2. Equipment now visible only to managers
3. Employees cannot see broken equipment
```

**Step 3: Manager Repairs Equipment**
```
1. Navigate to broken equipment (manager only sees it)
2. Click "Repair" button (on index or show page)
3. Repair modal opens
4. Fill in:
   - Repair description: "Screen replaced"
   - Cost: 150
   - Maintenance date: 2026-04-15
5. Click "Log Repair"
→ Equipment status: REPAIR
→ MaintenanceRecord: Created/Updated
→ "Repair" button: Hidden
→ "Finish Repair" button: Appears
```

**Step 4: Complete Repair**
```
1. Click "Finish Repair"
2. Confirm action
→ Equipment condition: USED
→ If assigned user: status → ASSIGNED
→ If no user: status → AVAILABLE
→ Equipment back in normal workflow
```

### Employee Workflow: Borrow & Return Equipment

**Step 1: View Available Equipment**
```
1. Login as employee
2. Navigate to Equipment Management
3. See: Own equipment + Available equipment
4. Cannot see: BROKEN, REPAIR, LOST equipment
```

**Step 2: Borrow Equipment**
```
1. Find available equipment
2. Click "Loan" button
3. Note: Only sees themselves in user dropdown
4. Enter loan dates
5. Click "Loan Equipment"
→ Equipment assigned to them
→ Status: ASSIGNED
```

**Step 3: Return Equipment**
```
1. Equipment shows "Return" button
2. Click "Return"
3. Enter return date
4. Click "Return Equipment"
→ If returning today: status → AVAILABLE
→ If future date: status stays ASSIGNED, expiration updated
```

### Manager Workflow: Multiple Repairs Same Equipment

**Repair 1:**
```
MaintenanceRecord:
- description: ["Screen replaced"]
- maintenance_date: ["2026-04-10"]
- cost: 150
```

**Repair 2 (same equipment):**
```
MaintenanceRecord updated:
- description: ["Screen replaced", "Battery replaced"]
- maintenance_date: ["2026-04-10", "2026-04-15"]
- cost: 350  // 150 + 200
```

---

## File Structure

```
BookKeepingPlatform/
├── app/
│   ├── Actions/
│   │   ├── LoanEquipmentAction.php
│   │   ├── ReturnEquipmentAction.php
│   │   ├── RepairEquipmentAction.php
│   │   └── FinishRepairAction.php
│   ├── Enums/
│   │   ├── Category.php
│   │   ├── Condition.php
│   │   └── Status.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── EquipmentController.php
│   │   │   ├── EquipmentHistoryController.php
│   │   │   ├── MaintenanceRecordController.php
│   │   │   ├── UserController.php
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   │   └── IsManager.php
│   │   └── Requests/
│   │       ├── Equipment/
│   │       │   ├── EquipmentStoreRequest.php
│   │       │   └── EquipmentUpdateRequest.php
│   │       └── MaintenanceRecord/
│   │           ├── MaintenanceRecordStoreRequest.php
│   │           └── MaintenanceRecordUpdateRequest.php
│   ├── Models/
│   │   ├── Equipment.php
│   │   ├── EquipmentHistory.php
│   │   ├── MaintenanceRecord.php
│   │   └── User.php
│   ├── Observers/
│   │   ├── EquipmentObserver.php
│   │   ├── EquipmentHistoryObserver.php
│   │   ├── MaintenanceRecordObserver.php
│   │   └── UserObserver.php
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_04_15_092313_create_equipment_table.php
│   │   ├── 2026_04_15_092433_create_maintenance_records_table.php
│   │   └── 2026_04_15_092453_create_equipment_histories_table.php
│   ├── factories/
│   │   ├── EquipmentFactory.php
│   │   ├── EquipmentHistoryFactory.php
│   │   ├── MaintenanceRecordFactory.php
│   │   └── UserFactory.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── views/
│       ├── equipment/
│       │   ├── index.blade.php
│       │   ├── show.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── maintenanceRecord/
│       │   ├── index.blade.php
│       │   ├── show.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── users/
│       │   ├── index.blade.php
│       │   ├── show.blade.php
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── layouts/
│       │   └── app.blade.php
│       └── auth/
│           ├── login.blade.php
│           ├── register.blade.php
│           └── forgot-password.blade.php
├── routes/
│   ├── web.php
│   ├── auth.php
│   └── console.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── ...
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
├── tests/
│   ├── Feature/
│   └── Unit/
├── public/
│   ├── index.php
│   ├── favicon.ico
│   └── robots.txt
├── .env
├── composer.json
├── package.json
├── phpunit.xml
├── tailwind.config.js
├── vite.config.js
└── README.md
```

---

## Key Features Summary

### ✅ Implemented Features

1. **Role-Based Access Control**
   - Manager & Employee roles
   - Equipment visibility filtering
   - Action availability based on role

2. **Equipment Management**
   - Full CRUD operations (managers only)
   - Equipment conditions: New, Used, Broken
   - Equipment statuses: Available, Assigned, Repair, Lost
   - Equipment categorization

3. **Equipment Loaning**
   - Loan equipment to users
   - Track loan dates and expiration dates
   - Conditional status changes
   - Loan history tracking

4. **Equipment Returns**
   - Return equipment with return date
   - Early return support (date adjustment)
   - Conditional status changes based on return date
   - On-time and late return handling

5. **Equipment History**
   - Track all users who borrowed equipment
   - Track loan dates and return dates
   - JSON array storage for multiple loans
   - Complete audit trail

6. **Broken Equipment Management**
   - Hide broken equipment from employees
   - Manager-only view of broken equipment
   - Repair initiation

7. **Equipment Repair Workflow**
   - Log equipment repairs
   - Track repair costs and dates
   - Multiple repairs per equipment
   - Repair completion status
   - Condition update to USED after repair

8. **Maintenance Records**
   - Create maintenance records during repair
   - Append to existing records
   - Track cumulative repair costs
   - Multiple repairs per equipment

9. **Frontend UI**
   - Equipment list with filtration
   - Equipment detail page
   - Modal-based forms (Loan, Return, Repair)
   - Dark mode support
   - Responsive design

10. **Data Persistence**
    - SQLite database
    - Proper relationships and foreign keys
    - JSON fields for array data
    - Cascade deletes

---

## Future Enhancement Ideas

1. **Export Functionality**
   - Export equipment list to CSV/PDF
   - Generate repair reports
   - Export history data

2. **Advanced Reporting**
   - Equipment usage statistics
   - Repair cost analysis
   - Equipment condition trends
   - User borrowing patterns

3. **Notifications**
   - Email alerts for overdue equipment
   - Repair completion notifications
   - Equipment maintenance reminders

4. **Equipment Tracking**
   - QR codes for quick equipment lookup
   - Equipment location tracking
   - Movement history

5. **Approval Workflow**
   - Manager approval for loans
   - Repair approval process
   - Equipment disposal workflow

6. **Integration**
   - Inventory system integration
   - Budget tracking
   - Asset depreciation tracking

---

## Troubleshooting

### Common Issues

**Issue: Equipment not appearing in employee list**
- Check: Equipment condition is not BROKEN
- Check: Equipment status is AVAILABLE or assigned to user
- Check: Search filters are not hiding it

**Issue: Return button not showing**
- Check: Equipment must be assigned to current user or user is manager
- Check: Equipment status must be ASSIGNED

**Issue: Repair button not showing**
- Check: User must be logged in as Manager
- Check: Equipment condition must be BROKEN
- Check: Equipment status must not already be REPAIR

**Issue: EquipmentHistory not updating**
- Check: Equipment observer is enabled
- Check: Using save() not saveQuietly() for initial loan
- Check: Equipment has status change to ASSIGNED

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2026-04-17 | Initial implementation with complete loan/return/repair workflow |

---

## Support & Contact

For issues, feature requests, or contributions, please refer to the project documentation or contact the development team.

---

**Last Updated:** April 17, 2026  
**Project Status:** Active Development  
**Framework:** Laravel 10+  
**PHP Version:** 8.1+

