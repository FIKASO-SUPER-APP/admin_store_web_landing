---
description: Repository Information Overview
alwaysApply: true
---

# Fikaso Repository Information

## Summary
Fikaso is a multi-service platform consisting of a management suite with Admin, Store, and Website panels built on Laravel 10, along with a static landing page. The entire stack is containerized using Docker and orchestrated with Docker Compose, featuring an integrated Nginx reverse proxy and automated SSL management.

## Repository Structure
The repository follows a monorepo-style structure with independent applications for different stakeholders:
- **Admin Panel**: Core management application for administrators.
- **Store Panel**: Interface for store owners to manage their inventory and orders.
- **Website Panel**: Consumer-facing web application.
- **Landing Panel**: Static promotional landing page.
- **nginx/**: Configuration files for the reverse proxy and service-specific Nginx instances.
- **init-db/**: Database initialization scripts.

## Shared Infrastructure
- **Database**: MySQL 8.0 (`fikaso_mysql`)
- **Reverse Proxy**: Nginx Alpine (`fikaso_nginx_proxy`) with Certbot for SSL automation.
- **Network**: Shared bridge network `fikaso_network`.

## Projects

### Admin Panel
**Configuration File**: `Admin Panel/composer.json`, `Admin Panel/package.json`

#### Language & Runtime
**Language**: PHP, JavaScript  
**Version**: PHP 8.4 (Docker), Laravel 10  
**Build System**: Laravel Mix  
**Package Manager**: Composer, NPM

#### Dependencies
**Main Dependencies**:
- `laravel/framework: ^10.0`
- `braintree/braintree_php`
- `google/apiclient`
- `stripe/stripe-php`
- `firebase-admin` (NPM)

#### Build & Installation
```bash
# From root
docker-compose build admin_panel
./deploy.sh
```

#### Docker
**Dockerfile**: `Admin Panel/Dockerfile`
**Image**: `php:8.4-fpm` base
**Configuration**: Runs PHP-FPM on port 9000, proxied by `admin_nginx`.

#### Testing
**Framework**: PHPUnit
**Test Location**: `Admin Panel/tests`
**Naming Convention**: `*Test.php`
**Run Command**:
```bash
docker-compose exec admin_panel php artisan test
```

---

### Store Panel
**Configuration File**: `Store Panel/composer.json`, `Store Panel/package.json`

#### Language & Runtime
**Language**: PHP, JavaScript  
**Version**: PHP 8.4 (Docker), Laravel 10  
**Build System**: Laravel Mix  
**Package Manager**: Composer, NPM

#### Dependencies
**Main Dependencies**:
- `laravel/framework: ^10.10`
- `razorpay/razorpay`
- `stripe/stripe-php`
- `xendit/xendit-php`

#### Build & Installation
```bash
docker-compose build store_panel
```

#### Docker
**Dockerfile**: `Store Panel/Dockerfile`
**Image**: `php:8.4-fpm` base
**Configuration**: Independent PHP-FPM service proxied by `store_nginx`.

#### Testing
**Framework**: PHPUnit
**Test Location**: `Store Panel/tests`
**Run Command**:
```bash
docker-compose exec store_panel php artisan test
```

---

### Website Panel
**Configuration File**: `Website Panel/composer.json`, `Website Panel/package.json`

#### Language & Runtime
**Language**: PHP, JavaScript (Vue.js)  
**Version**: PHP 8.4 (Docker), Laravel 10  
**Build System**: Laravel Mix  
**Package Manager**: Composer, NPM

#### Dependencies
**Main Dependencies**:
- `laravel/framework: ^10.0`
- `vue: ^2.6.12`
- `stripe/stripe-php`

#### Build & Installation
```bash
docker-compose build website_panel
```

#### Docker
**Dockerfile**: `Website Panel/Dockerfile`
**Image**: `php:8.4-fpm` base

#### Testing
**Framework**: PHPUnit
**Test Location**: `Website Panel/tests`
**Run Command**:
```bash
docker-compose exec website_panel php artisan test
```

---

### Landing Panel
**Type**: Non-traditional (Static Site)

#### Specification & Tools
**Type**: HTML/CSS/JS  
**Version**: Bootstrap 5  
**Required Tools**: Nginx

#### Key Resources
**Main Files**:
- `Landing Panel/index.html`
- `Landing Panel/css/style.css`
- `Landing Panel/js/bootstrap.bundle.min.js`

#### Usage & Operations
**Key Commands**:
```bash
docker-compose up landing_nginx
```

#### Validation
**Quality Checks**: Manual validation as per `PLAN_DE_TESTS.md`.
