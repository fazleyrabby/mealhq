# MealHQ Documentation

## Specification Documents

The following documents define the project requirements, architecture, and implementation plan. They are maintained locally under `docs/` (excluded from git).

| Document | Purpose |
| :--- | :--- |
| `docs/Specification.md` | Functional requirements, business rules, architecture, UI specs, database design, acceptance criteria |
| `docs/IMPLEMENTATION.md` | Execution protocol, development stages, quality gates, AI constraints, recovery protocol |
| `docs/BUILD_ORDER.md` | Module execution order, dependencies, completion criteria |
| `docs/Tasks.md` | Current project state, progress tracking, stage checklists |
| `docs/ADR.md` | Architecture decisions and technology constraints |
| `docs/NEXT_TASK.md` | Active task definition |

## Quick Reference

### Stack
- **Backend**: Laravel 13, PHP 8.4+, MySQL 8+
- **Frontend**: Blade, Alpine.js, Tailwind CSS v4, Vite
- **Containerization**: Docker, Docker Compose

### Admin UI
- Tabler Core 1.4 (reference: `/Users/rabbi/Downloads/Compressed/tabler--tabler-core-1.4.0`)

### Key Packages
- `spatie/laravel-permission` - RBAC
- `spatie/laravel-medialibrary` - Media management
- `spatie/laravel-activitylog` - Audit trails
- `maatwebsite/excel` - Excel exports
- `barryvdh/laravel-dompdf` - PDF generation
- `simplesoftwareio/simple-qrcode` - QR code generation

### Architecture
- Standard monolithic Laravel directories (`app/Actions`, `app/Services`, `app/Models`, etc.)
- Single-restaurant scope (nullable `branch_id` fields for future multi-branch)
- No Redis (database/file cache in V1)
- No broadcasting/websockets in V1

### Development Commands
```bash
# Start environment
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate:fresh --seed

# Run tests
docker compose exec app php artisan test

# Code formatting
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse
```

### Module Build Order
1. ✅ Foundation
2. ✅ Docker Infrastructure
3. ✅ Laravel Installation
4. Authentication
5. Roles & Permissions
6. Restaurant Settings
7. Website CMS
8. Public Website
9. Menu Management
10. Recipe Management
11. Inventory Management
12. Customer Ordering
13. POS System
14. Kitchen Display System (KDS)
15. Reservation System
16. Customer CRM
17. Reports & Analytics
18. Final Integration Testing
19. Performance Optimization
20. Production Deployment
