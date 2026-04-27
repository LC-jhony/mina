# GEMINI.md

## Project Overview: MVMS (Mining Vehicle Maintenance System)

MVMS is a centralized web platform for digitizing the logistical operation of vehicle fleets in mining companies. It covers driver registration, license management, trip planning, maintenance orders, spare parts control, and cost reporting.

### Core Stack
- **PHP:** 8.4
- **Laravel:** 13.0
- **Filament:** v5
- **Tailwind CSS:** v4
- **Pest PHP:** v4
- **Database:** MySQL 8 / MariaDB (Development), SQLite (Testing)

### Key Modules (FSD v2.0)
- **MOD-DRIVER (01):** Driver registration, license management, and expiration alerts.
- **MOD-VEHICLE (02):** Fleet management, status machine, mileage, and history.
- **MOD-TRIP (03):** Trip planning with a mandatory **2+2 driver allocation** rule (2 for outbound, 2 for return).
- **MOD-MAINT (04):** Maintenance orders, mechanic assignment, and spare parts consumption.
- **MOD-PARTS (05):** Spare parts inventory with **Snapshot Pricing** (price at time of use is preserved in the order).
- **MOD-MECH (06):** Mechanic management and workload (1 active OM per mechanic).
- **MOD-PREV (07):** Preventive maintenance scheduler based on KM or days.
- **MOD-REPORT (08):** Cost and operational reporting.
- **MOD-AUTH (09):** Roles (Admin, Coordinator, Mechanic Supervisor, Viewer).

---

## Development Guidelines

### Foundational Rules
- **Consistency First:** Follow existing patterns in sibling files.
- **Status Machine:** Vehicle and Trip status transitions **MUST** happen through Service classes (e.g., `app/Services/MaintenanceService.php`), never directly in controllers or resources.
- **Casting:** Use Enums for all status fields (located in `app/Enum/`).
- **Formatting:** Always run `vendor/bin/pint --dirty --format agent` after modifying PHP files.
- **Strict Typing:** Use PHP 8.4 type hints and return types everywhere.

### Core Domain Rules (FSD)
- **Trip Drivers:** A complete trip requires exactly 4 assignments: `outbound/1`, `outbound/2`, `return/1`, `return/2`.
- **License Validation:** Drivers must have an active license of a category compatible with the vehicle type before assignment.
- **Vehicle Statuses:** `available`, `on_trip`, `in_maintenance`, `out_of_service`.
- **Spare Parts:** Stock must be decremented atomically using `lockForUpdate`. `unit_price` in `maintenance_order_parts` is a snapshot of `spare_parts.unit_price` at the time of consumption.

### Testing Standards
- Use **Pest PHP**.
- Run tests with `php artisan test --compact`.
- Prefer feature tests using factories and states.
- **Database for tests:** SQLite in-memory (configured in `phpunit.xml`).

### Useful Commands
```bash
# First-time setup
composer run setup

# Development (Server + Queue + Logs + Vite)
composer run dev

# Run tests
php artisan test --compact

# Format PHP code
vendor/bin/pint --dirty --format agent

# Fresh migration + seed
php artisan migrate:fresh --seed
```

---

## Available Skills & Tools

### Skills (Activate when relevant)
- `laravel-best-practices`: Backend PHP logic, Eloquent, Architecture.
- `filament`: Admin panel resources, forms, tables, and actions.
- `pest-testing`: Writing and fixing Pest tests.
- `tailwindcss-development`: UI styling with Tailwind v4.

### MCP Tools (Laravel Boost)
- `search-docs`: **Mandatory** before any code change to check version-specific documentation.
- `database-schema`: Inspect table structures.
- `database-query`: Run read-only SQL for analysis.
- `browser-logs`: Debug frontend and JS issues.

---

## Architecture & Directory Structure
- `app/Enum/`: All domain-specific Enums.
- `app/Filament/`: Resources, Pages, and Widgets for the admin panel.
- `app/Models/`: Eloquent models with proper casting and relationships.
- `app/Services/`: Business logic and status transition management.
- `database/migrations/`: Order matters (referenced tables first).
- `database/seeders/`: Correct dependency order for factories.
- `app/Exceptions/`: Custom business logic exceptions (e.g., `InsufficientStockException`).
