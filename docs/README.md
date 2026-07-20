# MealHQ

A modern Restaurant Operating System built for restaurants, cafés, cloud kitchens, bakeries, and food courts. Combines a public restaurant website, QR menu, online ordering, point-of-sale (POS), kitchen display system (KDS), inventory, CRM, employee management, analytics, and reporting into one unified platform.

## Tech Stack

- **Backend**: Laravel 13, PHP 8.4+, MySQL 8+
- **Frontend**: Blade, Alpine.js, Tailwind CSS v4, Vite
- **Containerization**: Docker, Docker Compose

## Getting Started

### Prerequisites

- Docker and Docker Compose
- Git

### Installation

```bash
# Clone the repository
git clone https://github.com/your-username/mealhq.git
cd mealhq

# Copy environment file
cp .env.example .env

# Start development containers
docker compose up --build -d

# Install dependencies
docker compose exec app composer install

# Run database migrations
docker compose exec app php artisan migrate --seed

# Build frontend assets
docker compose exec node npm install
docker compose exec node npm run dev
```

### Access

- **Application**: http://localhost:8080
- **Mailpit UI**: http://localhost:8025
- **Database**: localhost:3307 (MySQL)

## Architecture

MealHQ follows a monolithic Laravel architecture with domain-oriented organization:

- `app/Actions/` - Single-responsibility business workflow classes
- `app/DTOs/` - Data transfer objects
- `app/Enums/` - PHP enum types
- `app/Services/` - Domain business logic
- `app/Http/Controllers/` - Thin controllers
- `app/Http/Requests/` - Form request validation
- `app/Models/` - Eloquent models
- `app/Policies/` - Authorization policies
- `app/Observers/` - Model observers
- `app/Events/` & `app/Listeners/` - Decoupled event handling

## Modules

1. Foundation
2. Docker Infrastructure
3. Laravel Installation
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
14. Kitchen Display System
15. Reservation System
16. Customer CRM
17. Reports & Analytics
18. Final Integration Testing
19. Performance Optimization
20. Production Deployment
