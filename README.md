# Dockerized Laravel + Nuxt Full-Stack Application

A modern full-stack application featuring Laravel 12.x backend and Nuxt 4.x frontend, fully containerized with Docker.

## 🚀 Tech Stack

### Backend
- **Laravel 12.x** (Latest) - PHP Framework
- **PHP 8.3** - Programming Language
- **MySQL 8.0** - Primary Database
- **PostgreSQL 16** - Alternative Database
- **Redis 7** - Caching, Sessions & Queues
- **Apache** - Web Server

### Frontend
- **Vue 3.5** - JavaScript Framework
- **Node.js 20** - Runtime Environment
- **PNPM** - Package Manager

### DevOps
- **Docker** - Containerization
- **Docker Compose** - Multi-container orchestration

## 🛠 Getting Started

### Prerequisites
- Docker Desktop
- Docker Compose

#### Database Switching (Laravel)
```bash
docker-compose exec backend php artisan datastore:switch 
```

### Quick Start

1. **Clone and navigate to the project**
   ```bash
   cd dockerized-app
   ```

2. **Generate Laravel application key**
   ```bash
   # Generate a key for Laravel
   cd backend
   php artisan key:generate --show
   cd ..
   ```

3. **Set the APP_KEY in docker-compose.yml or create .env file**
   ```bash
   echo "APP_KEY=your_generated_key_here" > .env
   ```

4. **Start all services**
   ```bash
   docker-compose up -d
   ```

5. **Run Laravel migrations**
   ```bash
   docker-compose exec backend php artisan migrate
   ```
6. **Run Laravel migrations**
   ```bash
   docker-compose exec backend php artisan db:seed
   ```

## 🌐 Access Points

- **Frontend (Nuxt)**: http://localhost:5173
- **Backend (Laravel)**: http://localhost:8000
- **phpMyAdmin (MySQL)**: http://localhost:8080
- **pgAdmin (PostgreSQL)**: http://localhost:8081
- **MySQL Database**: localhost:3306
- **PostgreSQL Database**: localhost:5432
- **Redis**: localhost:6379

### Database Credentials

#### MySQL
- **Host**: mysql (container) / localhost (external)
- **Database**: zenrows
- **Username**: root
- **Password**: mycomputer
- **Root Password**: mycomputer

#### PostgreSQL
- **Host**: postgres (container) / localhost (external)
- **Database**: zenrows
- **Username**: my_user
- **Password**: my_password

#### Redis
- **Host**: redis (container) / localhost (external)
- **REDIS_CLIENT**: predis
- **REDIS_PORT**: 6379
- **Password**: null

## 🐳 Docker Commands

### Basic Operations
```bash
# Build and start all services
docker-compose up -d

# Stop all services
docker-compose down

# Rebuild services
docker-compose up -d --build

# View logs
docker-compose logs

# View specific service logs
docker-compose logs backend
docker-compose logs frontend
```

### Development Commands
```bash
# Laravel Artisan commands
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan migrate:refresh --seed
docker-compose exec backend php artisan tinker
docker-compose exec backend php artisan route:list

# Composer commands
docker-compose exec backend composer install
docker-compose exec backend composer update

# Node.js commands in frontend
docker-compose exec frontend pnpm install
docker-compose exec frontend pnpm run dev
docker-compose exec frontend pnpm run build
```

### Database Operations

#### MySQL Operations
```bash
# Access MySQL CLI
docker-compose exec mysql mysql -u laravel_user -p laravel_app

# Backup MySQL database
docker-compose exec mysql mysqldump -u laravel_user -p laravel_app > mysql_backup.sql

# Restore MySQL database
docker-compose exec -T mysql mysql -u laravel_user -p laravel_app < mysql_backup.sql
```

#### PostgreSQL Operations
```bash
# Access PostgreSQL CLI
docker-compose exec postgres psql -U laravel_user_pg -d laravel_app_pg

# Backup PostgreSQL database
docker-compose exec postgres pg_dump -U laravel_user_pg laravel_app_pg > postgres_backup.sql

# Restore PostgreSQL database
docker-compose exec -T postgres psql -U laravel_user_pg -d laravel_app_pg < postgres_backup.sql
```

#### Redis Operations
```bash
# Access Redis CLI
./scripts/redis-cli.sh

# Execute Redis commands
./scripts/redis-cli.sh KEYS "*"
./scripts/redis-cli.sh FLUSHALL

# Monitor Redis activity
docker-compose exec redis redis-cli MONITOR
```

### Making Changes
1. **Backend changes**: Edit files in `./backend/` - changes are reflected via volume mounts
2. **Frontend changes**: Edit files in `./frontend/` - hot reload is enabled in development
3. **Database changes**: Create migrations in Laravel and run them in the container