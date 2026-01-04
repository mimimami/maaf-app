# MAAF App Telepítési Folyamat

Ez a dokumentum részletesen leírja, hogy mi történik a `composer create-project maaf/app my-app` parancs során.

## Telepítési Lépések

### 1. Composer Create Project

```bash
composer create-project maaf/app my-app
```

**Mi történik:**

1. ✅ **Package letöltése**
   - Composer letölti a `maaf/app` package-et a Packagist-ről
   - Létrehozza a `my-app` könyvtárat
   - Másolja az összes fájlt a package-ből a célkönyvtárba

2. ✅ **Függőségek telepítése**
   - Telepíti a `maaf/core` framework-ot
   - Telepíti az összes szükséges vendor csomagot (`php-di/php-di`, `nikic/fast-route`, stb.)
   - Létrehozza a `vendor/` könyvtárat

3. ✅ **Autoloader generálása**
   - Generálja a Composer autoloader-t
   - Regisztrálja a PSR-4 autoloading szabályokat

### 2. Post-Create-Project Script

A `composer.json`-ban definiált `post-create-project-cmd` script automatikusan lefut:

```json
{
    "scripts": {
        "post-create-project-cmd": [
            "@php install.php"
        ]
    }
}
```

Ez meghívja az `install.php` script-et, ami **interaktív kérdéseket tesz fel**.

### 3. Interaktív Installer (`install.php`)

Az installer a következő lépéseket hajtja végre:

#### 3.1. Adatbázis konfiguráció
- 📊 Kérdezi az adatbázis típusát (SQLite, MySQL, PostgreSQL)
- 📊 Kérdezi az adatbázis kapcsolati adatokat:
  - SQLite: fájl elérési út
  - MySQL/PostgreSQL: host, port, database név, username, password

#### 3.2. Frontend konfiguráció
- 🎨 Kérdezi a frontend framework-ot:
  - Nincs frontend (API only)
  - React + Vite
  - Vue.js + Vite
  - Vanilla JavaScript

#### 3.3. Biztonsági beállítások
- 🔐 Kérdezi a JWT Secret kulcsot
- 🔐 Ha üresen hagyod, **automatikusan generál** egy 64 karakteres hexadecimális kulcsot

#### 3.4. Környezeti változók
- 🌍 Kérdezi az alkalmazás környezetét (development, production, stb.)
- 🌍 Kérdezi a debug mód beállítását

#### 3.5. További opciók
- ⚙️ Kérdezi, hogy telepítsem-e a példa modult
- ⚙️ Kérdezi, hogy telepítsem-e a Git hooks-okat

### 4. Generált Fájlok

Az installer a következő fájlokat hozza létre/frissíti:

#### 4.1. `.env` fájl
```env
APP_ENV=development
APP_DEBUG=true

# Database Configuration
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# JWT Configuration
JWT_SECRET=<generált-kulcs>

# Frontend Configuration
FRONTEND_TYPE=none
```

#### 4.2. `config/database.php`
Az adatbázis konfiguráció fájl, amely tartalmazza:
- Adatbázis driver (sqlite, mysql, pgsql)
- Kapcsolati adatok
- Charset és collation beállítások

#### 4.3. `config/services.php`
Frissítve a következőkkel:
- PDO factory függvény az adatbázis kapcsolathoz
- JWT secret konfiguráció

### 5. Opcionális Lépések

#### 5.1. Példa modul eltávolítása
Ha a felhasználó azt választja, hogy nem kell a példa modul, az installer törli a `src/Modules/Example/` könyvtárat.

#### 5.2. Frontend inicializálás
Ha frontend framework-ot választott, az installer megpróbálja inicializálni (jelenleg még nincs teljesen implementálva).

## Összefoglalás

A `composer create-project maaf/app my-app` parancs:

1. ✅ **Letölti a keretrendszert** (`maaf/core` és függőségei)
2. ✅ **Felrakja a vendor csomagokat** (automatikusan)
3. ✅ **Létrehozza a `.env` fájlt** (interaktív kérdések után)
4. ✅ **Generál egy JWT secret kulcsot** (ha üresen hagyod)
5. ✅ **Adatbázis konfiguráció** (interaktív kérdések után)
6. ✅ **Frontend konfiguráció** (interaktív kérdések után)

## Különbség a Laravel-hez képest

| Laravel | MAAF |
|---------|------|
| `APP_KEY` generálása | `JWT_SECRET` generálása |
| `php artisan key:generate` | Automatikus az installer-ben |
| `.env.example` másolása | `.env` generálása interaktívan |
| `composer install` külön parancs | Automatikusan lefut |

## Következő Lépések

A telepítés után:

```bash
cd my-app
cd public
php -S localhost:8000
```

Az alkalmazás elérhető lesz: http://localhost:8000

