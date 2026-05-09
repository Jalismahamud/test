# Smart POS System

Laravel 11 + Inertia.js + Vue 3 + Tailwind CSS

## Requirements
- PHP 8.2+
- MySQL 8.0+
- Node.js 18+
- Composer 2+

## Installation

```bash
# 1. Clone and install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Configure .env
# Set DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Run migrations and seed demo data
php artisan migrate
php artisan db:seed

# 5. Start development
php artisan serve --port=8004
npm run dev
```

## Demo Credentials
| Role    | Email              | Password |
|---------|--------------------|----------|
| Admin   | admin@demo.com     | password |
| Manager | manager@demo.com   | password |
| Cashier | cashier@demo.com   | password |

## Features
- POS Terminal with offline support
- Inventory management
- Sales history and reports
- Multi-role authentication
- PWA installable
- IndexedDB offline storage
- Background sync when back online
