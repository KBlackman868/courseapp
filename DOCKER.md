# MOH Course Application - Docker Deployment Guide

This guide covers deploying the Ministry of Health Course Application using Docker.

## Prerequisites

- Docker Engine 20.10+
- Docker Compose v2.0+
- At least 2GB RAM available for containers
- Ports 8000, 3306, 6379 available (or configure alternatives)

## Quick Start

### 1. Clone and Configure

```bash
# Navigate to the project directory
cd courseapp

# Copy Docker environment file
cp .env.docker .env

# Generate application key
docker-compose run --rm app php artisan key:generate
```

### 2. Configure Environment Variables

Edit `.env` and update the following required variables:

```env
# Application
APP_URL=http://your-server-ip:8000

# Database passwords
DB_PASSWORD=your_secure_password
DB_ROOT_PASSWORD=your_root_password

# Google OAuth (required for authentication)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://your-server-ip:8000/auth/google/callback

# Moodle Integration
MOODLE_TOKEN=your-moodle-webservice-token
```

### 3. Start the Application

```bash
# Build and start containers
docker-compose up -d

# View logs
docker-compose logs -f app
```

### 4. Access the Application

- **Application**: http://localhost:8000
- **Health Check**: http://localhost:8000/health

## Services Overview

| Service | Port | Description |
|---------|------|-------------|
| app | 8000 | Main Laravel application |
| mysql | 3306 | MySQL 8.0 database |
| redis | 6379 | Redis cache/queue |
| queue | - | Background job processor |
| scheduler | - | Laravel task scheduler |

## Common Commands

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f [service-name]

# Access application shell
docker-compose exec app sh

# Run artisan commands
docker-compose exec app php artisan [command]

# Run migrations
docker-compose exec app php artisan migrate

# Clear caches
docker-compose exec app php artisan optimize:clear

# Database backup
docker-compose exec mysql mysqldump -u courseapp -p courseapp > backup.sql
```

## Development Setup

For local development with hot reloading:

```bash
# Start with development configuration
docker-compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```

This enables:
- **phpMyAdmin**: http://localhost:8080
- **Redis Commander**: http://localhost:8081
- **Mailhog**: http://localhost:8025 (email testing)
- Source code hot reloading

## SSL/HTTPS Configuration

For production, use a reverse proxy (nginx, traefik) with SSL:

```bash
# Example with Traefik labels (add to docker-compose.yml)
labels:
  - "traefik.enable=true"
  - "traefik.http.routers.courseapp.rule=Host(`courses.health.gov.tt`)"
  - "traefik.http.routers.courseapp.tls=true"
  - "traefik.http.routers.courseapp.tls.certresolver=letsencrypt"
```

## Scaling

Scale queue workers for heavy workloads:

```bash
docker-compose up -d --scale queue=3
```

## Backup & Restore

### Database Backup

```bash
# Backup
docker-compose exec mysql mysqldump -u root -p courseapp > backup_$(date +%Y%m%d).sql

# Restore
docker-compose exec -T mysql mysql -u root -p courseapp < backup.sql
```

### Volume Backup

```bash
# Backup storage volume
docker run --rm -v moh_courseapp_storage:/data -v $(pwd):/backup alpine tar czf /backup/storage_backup.tar.gz /data

# Backup database volume
docker run --rm -v moh_courseapp_mysql_data:/data -v $(pwd):/backup alpine tar czf /backup/mysql_backup.tar.gz /data
```

## Troubleshooting

### Container won't start

```bash
# Check logs
docker-compose logs app

# Check health
docker-compose ps

# Restart services
docker-compose restart
```

### Database connection errors

```bash
# Check MySQL is running
docker-compose exec mysql mysqladmin ping -u root -p

# Check database exists
docker-compose exec mysql mysql -u root -p -e "SHOW DATABASES;"
```

### Permission errors

```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Clear all caches

```bash
docker-compose exec app php artisan optimize:clear
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Environment Variables Reference

| Variable | Description | Default |
|----------|-------------|---------|
| APP_URL | Application URL | http://localhost:8000 |
| APP_PORT | Host port mapping | 8000 |
| DB_CONNECTION | Database driver | mysql |
| DB_DATABASE | Database name | courseapp |
| DB_USERNAME | Database user | courseapp |
| DB_PASSWORD | Database password | (required) |
| GOOGLE_CLIENT_ID | Google OAuth ID | (required) |
| GOOGLE_CLIENT_SECRET | Google OAuth Secret | (required) |
| MOODLE_BASE_URL | Moodle LMS URL | (required) |
| MOODLE_TOKEN | Moodle API token | (required) |

## Security Recommendations

1. **Change default passwords** in production
2. **Use SSL/TLS** with a reverse proxy
3. **Restrict database port** access (don't expose 3306 publicly)
4. **Keep Docker images updated** regularly
5. **Enable firewall** rules for container network
6. **Use secrets management** for sensitive data
