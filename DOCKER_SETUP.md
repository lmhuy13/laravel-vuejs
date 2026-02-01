# Laravel Docker Setup

## Services

- **Laravel App** (Port 8000): Laravel application
- **PostgreSQL** (Port 5432): Database
- **PgAdmin** (Port 5060): Database Management
- **Redis** (Port 6379): Cache & Queue
- **Supervisor**: Queue Jobs & Scheduler Management
- **Vite** (Port 5173): Vue.js Development Server

## Usage Guide

### 1. Start Docker

```bash
docker-compose up -d
```

### 2. Access Services

- **Laravel App**: http://localhost:8000
- **PgAdmin**: http://localhost:5060
  - Email: admin@admin.com
  - Password: admin
- **Vite Dev Server**: http://localhost:5173

### 3. Run Migrations

```bash
docker-compose exec app php artisan migrate
```

### 4. View Logs

```bash
docker-compose logs -f app
docker-compose logs -f supervisor
```

### 5. Stop Docker

```bash
docker-compose down
```

### 6. Remove Volumes (Database)

```bash
docker-compose down -v
```

## Configure Database in PgAdmin

1. Access http://localhost:5060
2. Login: admin@admin.com / admin
3. Add New Server:
   - Hostname: postgres
   - Username: postgres
   - Password: postgres
   - Database: laravel

## Important Configuration Files

- `.env`: Laravel configuration (auto-created from .env.example)
- `docker-compose.yml`: Docker orchestration
- `Dockerfile`: PHP-FPM
- `docker/supervisor/conf.d/`: Supervisor configuration

## Supervisor Configuration

Supervisor manages:
- **laravel-worker**: 4 processes for queue jobs
- **laravel-scheduler**: Scheduled tasks

## Queue with Redis

- Redis configured for cache, sessions, and jobs
- Supervisor worker processes queue jobs from Redis
- Database backend supports PostgreSQL

## Vue.js Integration

- Vite server runs on port 5173
- HMR (Hot Module Replacement) configured
- `package.json` uses Laravel/Vite plugin

## Troubleshooting

### Container won't start

```bash
docker-compose logs app
```

### Permission errors

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Reset database

```bash
docker-compose down -v
docker-compose up -d
```
