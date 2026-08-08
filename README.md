# Real Estate CRM

[![PHP](https://img.shields.io/badge/PHP-%3E%3D8.3-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Livewire](https://img.shields.io/badge/Livewire-4.x-FB70A9?logo=livewire&logoColor=white)](https://livewire.laravel.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com/)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-77C1D2?logo=alpinejs&logoColor=white)](https://alpinejs.dev/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Vite](https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white)](https://vite.dev/)
[![Sanctum](https://img.shields.io/badge/Sanctum-4.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/docs/sanctum)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

A full-featured, AI-ready Customer Relationship Management system built for real estate businesses. Manages the complete sales lifecycle — from lead capture through opportunity tracking, quotations, invoicing, and payment collection.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 13.8, PHP 8.3+, Livewire 4.3 |
| **Frontend** | Livewire, Tailwind CSS v4, Alpine.js 3.15 |
| **Database** | MySQL 8.0 (Docker) / SQLite (local) |
| **Build** | Vite 8 with laravel-vite-plugin |
| **Auth** | Laravel Sanctum 4.3 (session-based) |
| **Infra** | Docker Compose (Nginx, PHP-FPM, MySQL, Node, phpMyAdmin) |

## Quick Start

### With Docker (Recommended)

```bash
docker-compose up -d
```

Services available at:
- **App**: http://localhost:8020
- **phpMyAdmin**: http://localhost:8021
- **Vite Dev Server**: http://localhost:5173

Then run setup inside the PHP container:

```bash
docker exec -it crm_php bash
cd /var/www/html
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

### Without Docker (Local)

```bash
cd src/
composer setup    # Installs deps, generates key, migrates, builds assets
composer dev      # Starts all dev services concurrently
```

The `composer dev` script runs server, queue worker, log viewer, and Vite simultaneously.

## Project Structure

```
real-estate-crm/
├── docker-compose.yml          # 5-service Docker stack
├── docker/nginx/default.conf   # Nginx reverse proxy config
├── scripts/fix-nav.js          # Utility for bulk sidebar nav updates
├── templates/                  # Static HTML prototypes (~35 pages)
└── src/                        # Laravel application
    ├── app/
    │   ├── Http/Livewire/      # 60+ Livewire components (29 modules)
    │   ├── Models/             # 26 Eloquent models
    │   └── Providers/
    ├── database/
    │   ├── migrations/         # 30 migration files
    │   └── seeders/            # 17 seeder files
    ├── resources/
    │   ├── views/livewire/     # Blade views per module
    │   └── views/layouts/      # Master layout (app.blade.php)
    └── routes/web.php          # All routes (single file)
```

## Features

### CRM Core
- **Customers** — Contact/company management with status, credit limits, account managers
- **Contacts** — People linked to customers (position, department, WhatsApp, primary flag)
- **Leads** — Lead tracking with source, priority, pipeline status, expected closing dates
- **Pipeline** — Kanban board for opportunities across 7 stages (New → Won/Lost)
- **Opportunities** — Deal tracking with probability, value, and stage progression

### Sales Cycle
- **Quotations** — Line items, tax, discount, validity, payment terms
- **Sales Orders** — Order processing linked to quotations, delivery tracking
- **Invoices** — Invoice generation with paid amount tracking and due dates
- **Payments** — Payment recording with method, reference, status
- **Products** — Catalog with SKU, pricing, stock, categories

### Activities
- **Tasks** — Assignable tasks with priority, due dates, linked entities
- **Activities** — Call/email/meeting logging with duration and outcomes
- **Follow-ups** — Scheduled follow-up actions with reminders
- **Calendar** — Event management with start/end times and locations

### Communication
- **Chat** — Internal messaging between users with read status
- **Notes** — Pinnable notes with tag support, linked to any entity
- **Files** — File attachment management

### Support
- **Tickets** — Ticket system with numbers, priority, assignment

### Administration
- **Users** — Role-based management (admin/manager/sales/support)
- **Tags** — Polymorphic tagging across all entities
- **Reports** — Date-filtered KPIs: revenue, conversion rates, pipeline value
- **Audit Logs** — Entity change tracking with old/new values
- **API Keys** — Key generation with read/write/admin permissions
- **Settings** — Key-value settings store

## Database

26 Eloquent models backed by 30 migration tables. Key patterns:
- **Soft deletes** on core entities (Customer, Lead, Opportunity, Task, etc.)
- **Polymorphic tagging** via `taggables` pivot table
- **Decimal(12,2)** for all monetary values
- **Enum fields** for status/type values throughout

Seed with realistic demo data:

```bash
php artisan db:seed
```

Default users: Admin (`admin@example.com` / `password`), plus 4 role-based users.

## Architecture

**TALL Stack** — All application logic lives in Livewire components. No traditional controllers. Every module follows a consistent pattern:

- `Index` — List with search, filters, sorting, pagination, bulk actions
- `Create` — Validated form → save → redirect
- `Edit` — Pre-populated form → update → redirect
- `Show` — Detail view (for entities with complex data)

Navigation uses `wire:navigate` for SPA-like transitions between pages.

## Running Tests

```bash
php artisan test
# or
composer test
```

## Useful Commands

| Command | Description |
|---------|-------------|
| `composer dev` | Start all dev services (server, queue, logs, Vite) |
| `composer setup` | Full project setup from scratch |
| `php artisan db:seed` | Seed database with demo data |
| `php artisan migrate:fresh --seed` | Reset and reseed database |
| `php artisan pail` | Real-time log viewer |

## License

MIT
