# Deployment Útmutató

Ez az útmutató bemutatja, hogyan deployolhatod a MAAF alkalmazást különböző környezetekbe.

## Tartalomjegyzék

1. [Docker Deploy](#docker-deploy)
2. [Shared Hosting](#shared-hosting)
3. [VPS/Cloud Deploy](#vpscloud-deploy)
4. [Environment Konfiguráció](#environment-konfiguráció)
5. [Database Migrációk](#database-migrációk)
6. [Production Best Practices](#production-best-practices)

---

## Docker Deploy

### 1. Dockerfile Használata

A projekt tartalmaz egy `Dockerfile`-t. Build és futtatás:

```bash
# Build
docker build -t maaf-app .

# Run
docker run -p 8000:80 -e APP_ENV=production maaf-app
```

### 2. Docker Compose

A projekt tartalmaz egy `docker-compose.yml` fájlt:

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f
```

### 3. Production Dockerfile

```dockerfile
FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    nginx \
    sqlite \
    && docker-php-ext-install pdo pdo_sqlite

# Copy application
COPY . /var/www/html
WORKDIR /var/www/html

# Install Composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Configure PHP
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Expose port
EXPOSE 80

# Start services
CMD ["sh", "-c", "php-fpm && nginx -g 'daemon off;'"]
```

---

## Shared Hosting

### 1. Fájlok Feltöltése

```bash
# Fájlok feltöltése FTP-vel vagy SSH-val
# Fontos: csak a szükséges fájlokat töltsd fel

# .gitignore alapján NE töltsd fel:
# - vendor/ (feltöltés után: composer install --no-dev)
# - node_modules/
# - .env (készítsd el a szerveren)
# - storage/logs/*.log
```

### 2. Composer Telepítés

```bash
# SSH kapcsolaton keresztül
cd public_html
composer install --no-dev --optimize-autoloader
```

### 3. .env Fájl Beállítása

```env
APP_ENV=production
APP_DEBUG=false
APP_NAME="My MAAF App"

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

JWT_SECRET=your-secret-key-here
```

### 4. Web Server Konfiguráció

**Apache (.htaccess már benne van a public könyvtárban):**

```apache
# public/.htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

**Nginx:**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your/app/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## VPS/Cloud Deploy

### 1. Server Setup (Ubuntu/Debian)

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-sqlite3 \
    php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx

# Install MySQL (opcionális)
sudo apt install -y mysql-server
```

### 2. Application Deploy

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/yourusername/your-app.git
cd your-app

# Install dependencies
composer install --no-dev --optimize-autoloader

# Set permissions
sudo chown -R www-data:www-data /var/www/your-app
sudo chmod -R 755 /var/www/your-app

# Create .env file
cp .env.example .env
nano .env  # Edit with your settings

# Generate JWT secret
php -r "echo bin2hex(random_bytes(32));"

# Run migrations
php maaf migrate
```

### 3. Nginx Konfiguráció

```nginx
# /etc/nginx/sites-available/your-app
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/your-app/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/your-app /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 4. SSL/TLS Beállítás (Let's Encrypt)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal (automatikus)
sudo certbot renew --dry-run
```

---

## Environment Konfiguráció

### Production .env Példa

```env
APP_ENV=production
APP_DEBUG=false
APP_NAME="My MAAF Application"

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myapp_production
DB_USERNAME=myapp_user
DB_PASSWORD=secure_password_here

# JWT
JWT_SECRET=your-very-long-random-secret-key-here-min-32-chars

# CORS
CORS_ENABLED=true
CORS_ALLOWED_ORIGINS=https://your-frontend-domain.com

# Logging
LOG_REQUESTS=true

# Security
SESSION_SECURE=true
```

---

## Database Migrációk

### Production Migrációk Futtatása

```bash
# SSH kapcsolaton keresztül
cd /var/www/your-app
php maaf migrate

# Vagy Composer scripttel
composer migrate
```

### Backup Készítése

```bash
# MySQL backup
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# SQLite backup
cp database/database.sqlite backups/database_$(date +%Y%m%d).sqlite
```

---

## Production Best Practices

### 1. Security

- ✅ **Mindig használj HTTPS-t** production környezetben
- ✅ **Állítsd be az APP_DEBUG=false**-t
- ✅ **Erős JWT_SECRET** használata (min. 32 karakter)
- ✅ **Korlátozd a CORS allowed origins**-t
- ✅ **Rendszeres biztonsági frissítések**

### 2. Performance

- ✅ **Composer optimize**: `composer install --optimize-autoloader --no-dev`
- ✅ **OPcache engedélyezése** PHP-ban
- ✅ **Nginx caching** beállítása statikus fájlokhoz
- ✅ **Database indexing** megfelelő használata

### 3. Monitoring

- ✅ **Log fájlok monitorozása**: `storage/logs/`
- ✅ **Health check endpoint** használata: `/health`
- ✅ **Error tracking** (pl. Sentry integráció)

### 4. Backup Stratégia

- ✅ **Rendszeres database backup**
- ✅ **Code backup** (Git repository)
- ✅ **Environment változók backup** (biztonságos helyen)

---

## Cloud Provider Specifikus

### Heroku

```bash
# Procfile
web: vendor/bin/heroku-php-nginx public/

# Deploy
git push heroku main
```

### DigitalOcean App Platform

```yaml
# .do/app.yaml
name: maaf-app
region: fra
services:
- name: api
  source_dir: /
  github:
    repo: yourusername/your-app
    branch: main
  run_command: php -S 0.0.0.0:8080 -t public
  environment_slug: php
  instance_count: 1
  instance_size_slug: basic-xxs
  envs:
  - key: APP_ENV
    value: production
```

### AWS Elastic Beanstalk

```bash
# .ebextensions/php.config
option_settings:
  aws:elasticbeanstalk:container:php:
    document_root: /public
```

---

## További Források

- [Docker Dokumentáció](https://docs.docker.com)
- [Nginx Dokumentáció](https://nginx.org/en/docs/)
- [PHP-FPM Dokumentáció](https://www.php.net/manual/en/install.fpm.php)
- [Let's Encrypt Dokumentáció](https://letsencrypt.org/docs/)

