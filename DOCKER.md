# Docker Setup Guide

## Overview
This project is containerized with Docker and includes:
- **PHP 8.2  - Application container
- **Nginx** - Web server
- **PostgreSQL 15** - Database
- **Redis 7** - Caching & Queue driver
- **Database Test Container** - Separate PostgreSQL instance for testing

## Prerequisites
- Docker Desktop (Windows/Mac) or Docker Engine (Linux)
- Docker Compose 2.0+

## Quick Start

### 1. Setup Environment
```bash
# Copy environment file (already configured)
cp .env.example .env

# Or use the existing .env
```

### 2. Build and Run Services
```bash
# Build and start all containers
docker-compose up -d

# View container logs
docker-compose logs -f

# Specific container logs
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f redis
docker-compose logs -f nginx
```

### 3. Run Application Setup
```bash
# Get into the PHP container
docker-compose exec app bash

# Inside the container, run migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Generate app key (if needed)
php artisan key:generate
```

### 4. Access Application
- **Application**: http://localhost:8000
- **PostgreSQL**: localhost:5432
- **Redis**: localhost:6379
- **PostgreSQL (Test)**: localhost:5433

## Environment Variables

Key environment variables in `.env`:
```
DB_CONNECTION=pgsql
DB_HOST=db                    # Uses Docker service name
DB_PORT=5432
DB_DATABASE=inventory_service
DB_USERNAME=postgres
DB_PASSWORD=6262

REDIS_HOST=redis              # Uses Docker service name
REDIS_PORT=6379
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Common Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# Stop and remove volumes
docker-compose down -v

# Rebuild containers
docker-compose up -d --build

# Execute command in container
docker-compose exec app php artisan <command>

# SSH into app container
docker-compose exec app bash

# View container status
docker-compose ps

# Check container health
docker-compose logs
```

## Development Workflow

### Running Tests
```bash
docker-compose exec app php artisan test
docker-compose exec app php artisan test --filter=TestClassName
```

### Database Management
```bash
# Connect to PostgreSQL
docker-compose exec db psql -U postgres -d inventory_service

# Backup database
docker-compose exec db pg_dump -U postgres inventory_service > backup.sql

# Restore database
docker-compose exec db psql -U postgres inventory_service < backup.sql
```

### Cache Management
```bash
# Clear all cache
docker-compose exec app php artisan cache:clear

# Flush Redis
docker-compose exec redis redis-cli FLUSHALL
```

## File Structure

```
docker/
├── php/
│   ├── Dockerfile          # PHP 8.2-FPM image configuration
│   └── local.ini           # PHP configuration
└── nginx/
    └── default.conf        # Nginx configuration
```