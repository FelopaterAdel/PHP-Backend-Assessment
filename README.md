# PHP Backend - Inventory Management System

A Laravel 11 REST API for managing products and inventory with comprehensive validation and testing.

## Overview

This project provides a backend API for inventory management with features including:
- RESTful API endpoints for product management
- Input validation with detailed error responses
- PostgreSQL database with Redis caching
- Docker containerization for development and testing
- Comprehensive unit and feature tests

## Tech Stack

- **PHP**: 8.2+
- **Framework**: Laravel 11.31
- **Database**: PostgreSQL 15
- **Cache/Queue**: Redis 7
- **Web Server**: Nginx (Alpine)
- **Testing**: PHPUnit 11
- **Container**: Docker & Docker Compose

## Prerequisites

### For Docker Setup (Recommended)
- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Docker Compose 2.0+

### For Local Setup
- PHP 8.2 or higher
- PostgreSQL 15
- Redis 7
- Composer
- Node.js (for frontend assets, if needed)

## Quick Start with Docker

### 1. Clone and Setup
```bash
# Navigate to project directory
cd PHPTask

# Environment is already configured for Docker
# If needed, copy environment template:
cp .env.example .env
```

### 2. Start Services
```bash
# Build and start all containers (-d for detached)
docker-compose up -d

# View running containers
docker-compose ps

# View application logs
docker-compose logs -f app
```

### 3. Initialize Database
```bash
# Access the PHP container
docker-compose exec app bash

# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

The application will be available at **http://localhost:8000**

## Local Development Setup (Without Docker)

### 1. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies (if frontend assets needed)
npm install
```

### 2. Configure Environment
```bash
# Copy and edit environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Setup Database
```bash
# Create PostgreSQL database
createdb inventory_service

# Run migrations
php artisan migrate

# Seed with sample data
php artisan db:seed
```

### 4. Start Development Server
```bash
# Run development server (default: http://localhost:8000)
php artisan serve
```

## Running Tests

### With Docker
```bash
# Access container
docker-compose exec app bash

# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProductTest.php

# Run with coverage
php artisan test --coverage
```

### Without Docker
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProductTest.php
```

## API Documentation

### Base URL
- **Docker**: `http://localhost:8000/api`
- **Local**: `http://localhost:8000/api`

### Endpoints

#### Products

- **GET** `/products` - List all products
- **POST** `/products` - Create a new product
- **GET** `/products/{id}` - Get product details
- **PUT** `/products/{id}` - Update a product
- **DELETE** `/products/{id}` - Delete a product

See [VALIDATION.md](VALIDATION.md) for detailed validation rules and error responses.

## Project Structure

```
app/
  ├── Http/Controllers/      # API controllers
  ├── Http/Requests/         # Form request validation
  ├── Models/                # Database models (Product, User)
  └── Exceptions/            # Custom exception handlers

database/
  ├── migrations/            # Database schema
  ├── factories/             # Model factories for testing
  └── seeders/               # Database seeders

routes/
  └── api.php               # API route definitions

tests/
  ├── Feature/              # Integration tests
  └── Unit/                 # Unit tests

config/
  ├── database.php          # Database configuration
  ├── cache.php             # Cache configuration
  └── queue.php             # Queue configuration
```

## Docker Services

All services are configured in `docker-compose.yml`:

| Service | Container | Port | Details |
|---------|-----------|------|---------|
| **PHP App** | inventory_app | - | Laravel application container |
| **Nginx** | inventory_nginx | 8000 | Web server (http://localhost:8000) |
| **PostgreSQL** | inventory_db | 5432 | Main database |
| **PostgreSQL** | inventory_test_db | - | Test database (internal only) |
| **Redis** | inventory_redis | 6379 | Cache & queue driver |

See [DOCKER.md](DOCKER.md) for detailed Docker setup and troubleshooting.

## Useful Commands

### Docker Commands
```bash
# View all container logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f redis

# Stop all services
docker-compose down

# Stop and remove volumes
docker-compose down -v

# Rebuild containers
docker-compose up -d --build
```

### Laravel Artisan Commands
```bash
# Clear application cache
php artisan cache:clear

# Run database migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration (WARNING: drops all data)
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Interactive shell
php artisan tinker
```

### Testing Commands
```bash
# Run all tests
php artisan test

# Run tests with verbose output
php artisan test --verbose

# Run tests matching pattern
php artisan test --filter=testProductCreation

# Generate code coverage report
php artisan test --coverage
```

## Environment Variables

Key environment variables (set in `.env`):

```env
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:...

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=inventory_service
DB_USERNAME=postgres
DB_PASSWORD=6262

REDIS_HOST=redis
REDIS_PORT=6379

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Database Seeding

Run seeders to populate sample data:

```bash
# Seed with ProductFactory (products table)
php artisan db:seed --class=DatabaseSeeder
```

The `DatabaseSeeder` includes factories that generate:
- Test users
- Sample products with realistic inventory data

## Troubleshooting

### Database Connection Issues
```bash
# Check if database container is running
docker-compose ps

# Verify database connectivity from app container
docker-compose exec app php artisan tinker
# Then: DB::connection()->getPdo();
```

### Redis Connection Issues
```bash
# Check Redis container status
docker-compose ps

# Test Redis connectivity
docker-compose exec app php artisan tinker
# Then: Redis::ping();
```

### Port Already in Use
If port 8000 is already in use, modify `docker-compose.yml`:
```yaml
ports:
  - "8001:80"  # Change 8000 to any available port
```

### Fresh Start
```bash
# Clean shutdown and rebuild
docker-compose down -v
docker-compose up -d --build
docker-compose exec app php artisan migrate --seed
```

## Running in Production

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Ensure proper database backups
4. Configure Redis persistence if needed
5. Set appropriate PostgreSQL backup policies
6. Monitor container logs and resource usage

## License

MIT License

## Support

For API validation details, see [VALIDATION.md](VALIDATION.md)
For Docker setup details, see [DOCKER.md](DOCKER.md)
