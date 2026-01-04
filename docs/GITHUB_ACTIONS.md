# GitHub Actions CI/CD

Ez az útmutató bemutatja, hogyan állíthatod be a GitHub Actions-t CI/CD-hez.

## Alapvető CI Workflow

Hozz létre egy `.github/workflows/ci.yml` fájlt:

```yaml
name: CI

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: test_db
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3

    steps:
    - uses: actions/checkout@v3

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: pdo, pdo_mysql, pdo_sqlite, mbstring, xml, curl, zip
        coverage: none

    - name: Install Composer dependencies
      run: composer install --prefer-dist --no-progress --no-interaction

    - name: Copy .env
      run: cp .env.example .env

    - name: Generate JWT Secret
      run: php -r "echo 'JWT_SECRET=' . bin2hex(random_bytes(32)) . PHP_EOL; >> .env"

    - name: Run migrations
      run: php maaf migrate

    - name: Run tests
      run: composer test

    - name: Run PHPStan
      run: vendor/bin/phpstan analyse src --level=5

    - name: Run Code Style Check
      run: composer lint
```

## Deployment Workflow

```yaml
name: Deploy

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3

    - name: Deploy to server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /var/www/your-app
          git pull origin main
          composer install --no-dev --optimize-autoloader
          php maaf migrate
          sudo systemctl reload php8.2-fpm
```

## Secrets Beállítása

GitHub repository → Settings → Secrets and variables → Actions:

- `HOST`: szerver IP vagy domain
- `USERNAME`: SSH felhasználó
- `SSH_KEY`: SSH private key

## További Források

- [GitHub Actions Dokumentáció](https://docs.github.com/en/actions)
- [PHP GitHub Actions](https://github.com/shivammathur/setup-php)

