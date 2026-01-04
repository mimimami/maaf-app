# MAAF App Installer - Lehetséges Kiegészítések

Ez a dokumentum felsorolja azokat a funkciókat, amelyekkel az installer-t és a package-t lehetne még kiegészíteni.

## 🎯 Magas Prioritás

### 1. Frontend Inicializálás Implementálása
**Jelenlegi állapot:** TODO van az `install.php`-ben

**Mit kellene csinálni:**
- React + Vite inicializálása: `npm create vite@latest frontend -- --template react`
- Vue.js + Vite inicializálása: `npm create vite@latest frontend -- --template vue`
- Vanilla JS inicializálása: egyszerű HTML/CSS/JS struktúra
- Vite proxy konfiguráció beállítása a backend-hez
- Frontend `.env` fájl generálása (API URL stb.)

**Előnyök:**
- Teljes stack inicializálás egy parancsban
- Konzisztens projekt struktúra
- Kevesebb manuális beállítás

---

### 2. Git Inicializálás
**Mit kellene csinálni:**
- Automatikus `git init`
- Alapértelmezett `.gitignore` másolása (ha még nincs)
- Kezdeti commit létrehozása
- `.gitattributes` fájl hozzáadása

**Előnyök:**
- Azonnal készen áll a verziókezelésre
- Konzisztens Git konfiguráció

---

### 3. Database Migrációk Scaffold
**Mit kellene csinálni:**
- `database/migrations/` könyvtár létrehozása
- Példa migráció fájl (`0001_create_example_table.sql`)
- Migráció futtatási script (`run-migrations.php`)
- Migráció rollback támogatás

**Előnyök:**
- Azonnal látható, hogyan működnek a migrációk
- Konzisztens adatbázis struktúra kezelés

---

### 4. Database Connection Test
**Mit kellene csinálni:**
- Adatbázis kapcsolat tesztelése az installer végén
- Sikertelen kapcsolat esetén figyelmeztetés
- SQLite fájl létrehozása, ha nem létezik

**Előnyök:**
- Azonnal látható, hogy a konfiguráció működik
- Kevesebb debugging idő

---

### 5. Environment Validation
**Mit kellene csinálni:**
- `.env` fájl validálása (kötelező változók ellenőrzése)
- JWT Secret hossz ellenőrzése (minimum 32 karakter)
- Adatbázis kapcsolati adatok validálása

**Előnyök:**
- Kevesebb konfigurációs hiba
- Jobb developer experience

---

## 🚀 Közepes Prioritás

### 6. Docker Konfiguráció
**Mit kellene csinálni:**
- `Dockerfile` generálása
- `docker-compose.yml` generálása (PHP, MySQL, PostgreSQL, Redis)
- `.dockerignore` fájl
- Docker development environment setup

**Előnyök:**
- Könnyű fejlesztői környezet
- Konzisztens production deployment

---

### 7. Testing Framework Setup
**Mit kellene csinálni:**
- PHPUnit konfiguráció (`phpunit.xml`)
- Példa teszt fájl (`tests/ExampleTest.php`)
- Test database setup
- GitHub Actions CI workflow teszteléshez

**Előnyök:**
- Azonnal készen áll a tesztelésre
- Best practice követése

---

### 8. Code Style Configuration
**Mit kellene csinálni:**
- PHP CS Fixer konfiguráció (`.php-cs-fixer.php`)
- PHP_CodeSniffer konfiguráció (`phpcs.xml`)
- Pre-commit hook a code style ellenőrzéshez
- GitHub Actions workflow a code style ellenőrzéshez

**Előnyök:**
- Konzisztens kód stílus
- Automatikus formázás

---

### 9. Welcome Route/Endpoint
**Mit kellene csinálni:**
- `GET /` endpoint, ami visszaadja az alkalmazás információit
- API verzió
- Health check endpoint (`GET /health`)
- API dokumentáció link

**Előnyök:**
- Azonnal látható, hogy az API működik
- Könnyebb debugging

---

### 10. CORS Konfiguráció
**Mit kellene csinálni:**
- Alapértelmezett CORS middleware beállítása
- `.env` fájlban CORS beállítások (allowed origins)
- Development és production CORS konfiguráció

**Előnyök:**
- Azonnal működik a frontend-backend kommunikáció
- Kevesebb CORS hiba

---

## 💡 Alacsony Prioritás

### 11. Rate Limiting Konfiguráció
**Mit kellene csinálni:**
- Alapértelmezett rate limiting middleware
- `.env` fájlban rate limit beállítások
- IP-alapú rate limiting

**Előnyök:**
- Alapvető biztonsági réteg
- DDoS védelem

---

### 12. Logging Konfiguráció
**Mit kellene csinálni:**
- Monolog konfiguráció
- Log fájlok könyvtár (`storage/logs/`)
- Log rotation beállítások
- Error logging middleware

**Előnyök:**
- Jobb debugging lehetőség
- Production-ready logging

---

### 13. API Dokumentáció Scaffold
**Mit kellene csinálni:**
- OpenAPI/Swagger spec generálása
- API dokumentáció endpoint (`GET /api-docs`)
- Példa API endpoint dokumentációval

**Előnyök:**
- Könnyebb API integráció
- Automatikus dokumentáció generálás

---

### 14. Seed Adatok
**Mit kellene csinálni:**
- `database/seeds/` könyvtár
- Példa seed fájl (`ExampleSeeder.php`)
- Seed futtatási script (`run-seeds.php`)

**Előnyök:**
- Demo adatok azonnal elérhetők
- Könnyebb tesztelés

---

### 15. CLI Tool (Artisan-like)
**Mit kellene csinálni:**
- Egyszerű CLI tool (`php maaf`)
- Parancsok:
  - `php maaf migrate` - Migrációk futtatása
  - `php maaf seed` - Seed adatok futtatása
  - `php maaf make:module` - Új modul generálása
  - `php maaf make:controller` - Új controller generálása

**Előnyök:**
- Laravel-szerű developer experience
- Könnyebb workflow

---

### 16. Error Handling Konfiguráció
**Mit kellene csinálni:**
- Alapértelmezett error handler
- Error response formátum konzisztencia
- Development és production error handling különbségek

**Előnyök:**
- Jobb error messages
- Konzisztens API válaszok

---

### 17. Middleware Pipeline Konfiguráció
**Mit kellene csinálni:**
- Alapértelmezett middleware pipeline beállítása
- Middleware konfiguráció fájl (`config/middleware.php`)
- Példa middleware-ek (Auth, CORS, Rate Limiting, Logging)

**Előnyök:**
- Azonnal működő middleware rendszer
- Könnyebb bővíthetőség

---

### 18. Projekt-specifikus README Generálása
**Mit kellene csinálni:**
- README.md generálása a válaszok alapján
- Projekt név, leírás, author információk
- Telepítési útmutató
- API dokumentáció link

**Előnyök:**
- Azonnal dokumentált projekt
- Könnyebb onboarding

---

### 19. GitHub Actions CI/CD Workflow
**Mit kellene csinálni:**
- Alapértelmezett CI workflow (`.github/workflows/ci.yml`)
- PHPUnit tesztek futtatása
- Code style ellenőrzés
- PHPStan statikus analízis

**Előnyök:**
- Azonnal működő CI/CD
- Automatikus minőségellenőrzés

---

### 20. Health Check és Monitoring
**Mit kellene csinálni:**
- Health check endpoint (`GET /health`)
- Database connection check
- System info endpoint (`GET /info`) - csak development módban

**Előnyök:**
- Könnyebb monitoring
- Production-ready health checks

---

## 📊 Prioritási Rangsor

### Azonnal implementálni:
1. ✅ Frontend inicializálás
2. ✅ Git inicializálás
3. ✅ Database connection test
4. ✅ Environment validation

### Következő iterációban:
5. Database migrációk scaffold
6. Docker konfiguráció
7. Testing framework setup
8. Welcome route/endpoint
9. CORS konfiguráció

### Később:
10. Code style configuration
11. Rate limiting
12. Logging konfiguráció
13. CLI tool
14. API dokumentáció scaffold

---

## 💬 Megjegyzések

- A legtöbb funkció opcionális lehet (kérdezze meg az installer)
- Fontos, hogy ne legyen túl komplex az installer
- A Laravel installer is csak az alapvető dolgokat csinálja, a többi opcionális
- Fontos a backward compatibility

