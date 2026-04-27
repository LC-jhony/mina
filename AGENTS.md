# Agent Guidance

## Tech Stack
- PHP 8.3+ (via WSL), Laravel 13, Filament 5, Livewire 4, Tailwind 4
- Pest 4 for testing, Pint for formatting

## Commands
```bash
# First-time setup
composer run setup

# Dev (server + queue + logs + vite hot reload)
composer run dev

# Run tests
php artisan test --compact
php artisan test --compact --filter=testName

# Format PHP
vendor/bin/pint --dirty --format agent
```

## Database
- Dev: MariaDB at 127.0.0.1:3306, user root, pass admin (`.env:28`)
- Tests: SQLite in-memory (`phpunit.xml:27`)

## Vite
- Vite manifest may need rebuild on errors: `npm run build`

## MCP Tools
Use Laravel Boost MCP instead of manual alternatives:
- `database-query` — read-only SQL
- `database-schema` — table structure
- `get-absolute-url` — resolve URLs
- `search-docs` — query Laravel docs (always use before code changes)

## Skills
Activate when relevant:
- `laravel-best-practices` — PHP/Laravel backend code
- `pest-testing` — Pest PHP tests
- `tailwindcss-development` — Tailwind utility classes
- `filament` — Filament v5 admin panel development

## Code Style
- Always run `vendor/bin/pint --dirty --format agent` after modifying PHP
- Use `php artisan make:` commands with `--no-interaction`
- Do not delete tests without approval

## Database & Migrations
```bash
# Fresh migration + seed (common during dev)
php artisan migrate:fresh --seed
```

**Migration ordering**: Foreign key tables must run after referenced tables. If tables share timestamps, rename migration file (e.g., `...523_create_orders_table.php` → `...524_create_order_parts_table.php`).

**Seeders & Factories**:
- Factories with relationships must be called in correct order in seeder
- Pass variables into closures: `$trips->each(function ($trip) use ($drivers) { ... })`
- For unique constraints, calculate values deterministically from available data instead of random generation

## Post-Update Hook
The `post-update-cmd` in `composer.json` only runs `vendor:publish`. The Boost AI commands are intentionally removed because they require local development environment.