# BuildSync ERP — Standalone Demo Database Package

This directory contains a complete, portable, standalone demo dataset for **BuildSync ERP (Laravel 12)**. It provides pre-seeded operational data across all core construction and financial modules without requiring manual recreation.

---

## 1. Package Contents

| File | Description | Size |
|---|---|---|
| `buildsync_demo.sql` | Complete ANSI / SQLite-compatible SQL dump with full schema, indexes, and demo datasets. | ~175 KB |
| `buildsync_demo.sqlite` | Ready-to-use SQLite database binary. Can be copied directly to `database/database.sqlite`. | ~320 KB |
| `README.md` | Comprehensive setup, configuration, and verification instructions. | Documentation |

---

## 2. System Requirements

- **PHP**: >= 8.2 with `pdo_sqlite` and `sqlite3` extensions enabled.
- **Database Engine**: SQLite 3 (version 3.35+), which is the default database engine in Laravel 12.
- **Composer**: 2.x
- **Node.js & npm**: Node 18+ (for frontend assets).

---

## 3. Demo User Accounts

The demo database includes exactly one user account per application role. All passwords are set to `password`.

| Role | Name | Email | Password | Linked Worker |
|---|---|---|---|---|
| `superadmin` | **AHMAD SAUQI ZAMZAMI** | `admin@buildsync.com` | `password` | None (Full Access) |
| `project_manager` | **MUHAMAD AKBAR FEBIANSYAH** | `akbar.febiansyah@buildsync.com` | `password` | None |
| `logistics` | **MUHAMMAD FADHILLAH AKBAR** | `fadhillah.akbar@buildsync.com` | `password` | None |
| `finance` | **SITI NURHALIZA** | `siti.nurhaliza@buildsync.com` | `password` | None |
| `field_staff` | **JOKO PRASETYO** | `joko.prasetyo@buildsync.com` | `password` | `WKR-001` (Mandor Lapangan) |

---

## 4. Operational Modules & Dataset Scope

All operational modules contain realistic, consistent Indonesian construction data (minimum 30 records per module):

| Module | Table | Records | Details |
|---|---|---|---|
| **Projects** | `projects` | 30 | Major public & commercial infrastructure projects across Indonesia (ongoing, completed, planning, on_hold). Total contract value: Rp 608,45 Miliar. |
| **Schedules (WBS)** | `project_schedules` | 34 | Realistic construction stages (Bored Pile, Struktur, ME, Fasad, dll.). Cumulative stage weights are strictly <= 100% per project. |
| **Progress Updates** | `progress_updates` | 34 | Chronological milestone and physical progress tracking percentages and field notes. |
| **Materials / Logistics** | `materials` | 30 | Cement, rebar (D10, D13, D16), sand, split, ready-mix, AAC blocks, PVC pipes, paint, electrical, and diesel fuel. |
| **Suppliers / Vendors** | `suppliers` | 30 | Verified Indonesian construction material manufacturers and distributors. |
| **Procurement (PO)** | `purchase_orders` | 30 | Purchase orders linking projects, suppliers, and items with `ordered`, `received`, and `partial` statuses. |
| **PO Items** | `purchase_order_items` | 30 | Itemized line quantities, unit rates, and totals. |
| **Inventory Mutations** | `stock_transactions` | 30 | Inbound vendor receipts, site project dispatches, and opening balance audit logs. |
| **Workforce / Labor** | `workers` | 30 | Field foremen, certified welders, crane operators, carpenters, plumbers, and electricians. |
| **Worker Assignments** | `worker_assignments` | 30 | Site assignments linked to projects and WBS stages. |
| **Worker Attendances** | `worker_attendances` | 30 | Verified daily attendance records with hours (8h), daily rates, and payroll statuses (`paid` / `pending`). |
| **Budgets & Expenses** | `budgets` | 30 | Project expenses across 6 categories (`material`, `labor`, `equipment`, `subcontractor`, `operational`, `overhead`) with unique vouchers (`EXP-00001` to `EXP-00030`). |
| **Cash Flows** | `cash_flows` | 30 | Financial inflows (client termijn, project advances) and outflows (vendor payments, payroll distributions). |
| **Users** | `users` | 5 | One demo user per application role. |

---

## 5. Installation Instructions

Choose one of the three setup methods below depending on your preference.

### Method A: Instant Setup via SQLite Binary (Fastest)

1. Ensure the active database connection in `.env` is set to SQLite:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/absolute/path/to/project/database/database.sqlite
   ```
   *(Or simply omit `DB_DATABASE` to default to `database/database.sqlite`)*.

2. Copy the pre-built demo SQLite database:
   ```bash
   cp database/demo/buildsync_demo.sqlite database/database.sqlite
   ```

3. Ensure write permissions on the SQLite database:
   ```bash
   chmod 664 database/database.sqlite
   ```

---

### Method B: Import via SQL Dump

If creating a fresh database from the SQL dump (`buildsync_demo.sql`):

1. **Using SQLite CLI**:
   ```bash
   # Remove existing database if needed
   rm -f database/database.sqlite

   # Import SQL dump
   sqlite3 database/database.sqlite < database/demo/buildsync_demo.sql
   ```

2. **Using PHP PDO (No sqlite3 CLI required)**:
   ```bash
   php -r '
   $pdo = new PDO("sqlite:database/database.sqlite");
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   $pdo->exec(file_get_contents("database/demo/buildsync_demo.sql"));
   echo "Import successfully completed!\n";
   '
   ```

---

### Method C: Fresh Migration & Seeder Execution

To generate the dataset dynamically from source code:

1. Run fresh migrations and seed with `DemoDataSeeder`:
   ```bash
   php artisan migrate:fresh --seed --seeder=DemoDataSeeder
   ```

2. (Optional) Run `DemoDataSeeder` on an existing database without dropping tables:
   ```bash
   php artisan db:seed --class=DemoDataSeeder
   ```

---

## 6. Starting the Application

1. Build frontend assets if not already compiled:
   ```bash
   npm run build
   ```

2. Start the local Laravel development server:
   ```bash
   php artisan serve
   ```

3. Open your browser and navigate to:
   ```
   http://localhost:8000/login
   ```

4. Sign in using any of the demo accounts listed in Section 3 (e.g. `admin@buildsync.com` / `password`).

---

## 7. Verifying Demo Data

To quickly verify that all tables and relationships loaded correctly:

```bash
php -r '
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$counts = [
    "Projects" => App\Models\Project::count(),
    "Schedules" => App\Models\ProjectSchedule::count(),
    "Materials" => App\Models\Material::count(),
    "Suppliers" => App\Models\Supplier::count(),
    "Purchase Orders" => App\Models\PurchaseOrder::count(),
    "Workers" => App\Models\Worker::count(),
    "Budgets" => App\Models\Budget::count(),
    "Cash Flows" => App\Models\CashFlow::count(),
    "Users" => App\Models\User::count(),
];

foreach ($counts as $module => $count) {
    echo sprintf("%-20s : %d records\n", $module, $count);
}
'
```

---

## 8. Safe Database Reset

To reset the database back to clean demo state at any time:

```bash
cp database/demo/buildsync_demo.sqlite database/database.sqlite
```
Or:
```bash
php artisan migrate:fresh --seed --seeder=DemoDataSeeder
```
