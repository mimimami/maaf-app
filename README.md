# MAAF App

A fresh MAAF application skeleton with interactive installer.

## Telepítés

```bash
composer create-project maaf/app my-app
```

### Mi történik a telepítés során?

1. ✅ **Letölti a keretrendszert** (`maaf/core` és függőségei)
2. ✅ **Felrakja a vendor csomagokat** (automatikusan)
3. ✅ **Interaktív installer elindul** és kérdéseket tesz fel:
   - 📊 Adatbázis típus (SQLite, MySQL, PostgreSQL)
   - 🎨 Frontend framework (React, Vue, Vanilla JS, vagy nincs)
   - 🔐 JWT Secret kulcs (vagy automatikus generálás)
   - 🌍 Környezeti változók (APP_ENV, APP_DEBUG)
   - ⚙️ További beállítások (példa modul, Git inicializálás)
4. ✅ **Létrehozza a `.env` fájlt** a válaszok alapján
5. ✅ **Generál JWT secret kulcsot** (ha üresen hagytad)
6. ✅ **Konfigurálja az adatbázist** (`config/database.php`)
7. ✅ **Frissíti a szolgáltatásokat** (`config/services.php`)
8. ✅ **Validálja a konfigurációt** (JWT Secret hossz, adatbázis beállítások)
9. ✅ **Teszteli az adatbázis kapcsolatot**
10. ✅ **Inicializálja a frontend-et** (ha választottál)
11. ✅ **Inicializálja a Git repository-t** (ha kérted)

Lásd: [INSTALLATION.md](INSTALLATION.md) részletes leírásért.

## Struktúra

```
my-app/
├── composer.json
├── config/
│   ├── services.php
│   ├── routes.php
│   └── database.php (generált)
├── public/
│   └── index.php
├── .env (generált)
└── src/
    └── Modules/
        └── Example/ (opcionális)
```

## Használat

1. Telepítsd a package-et: `composer create-project maaf/app my-app`
2. Válaszolj az installer kérdéseire
3. Indítsd el a webszervert:
   ```bash
   cd my-app
   php maaf serve
   ```
   Vagy manuálisan:
   ```bash
   cd my-app/public
   php -S localhost:8000
   ```
4. Nyisd meg a böngészőben: http://localhost:8000
   - 🎨 Szép welcome oldal jelenik meg
   - 📊 Health check: http://localhost:8000/health
   - 📚 API docs: http://localhost:8000/api-docs

## Dokumentáció

### Alapvető Dokumentáció

- [Telepítési Útmutató](INSTALLATION.md) - Részletes telepítési lépések
- [MAAF Core Dokumentáció](https://github.com/mimimami/maaf-core) - Framework dokumentáció

### Fejlesztési Útmutatók

- [Frontend Integráció](docs/FRONTEND_INTEGRATION.md) - React, Vue, Vanilla JS integráció
- [Deployment Útmutató](docs/DEPLOYMENT.md) - Docker, VPS, Cloud deploy
- [Best Practices](docs/BEST_PRACTICES.md) - Ajánlott fejlesztési gyakorlatok
- [CLI Parancsok](docs/CLI_COMMANDS.md) - MAAF CLI tool használata
- [GitHub Actions CI/CD](docs/GITHUB_ACTIONS.md) - Automatizált tesztelés és deploy

## Következő Lépések

1. Hozz létre saját modulokat a `src/Modules/` könyvtárban
2. Regisztráld a szolgáltatásokat a `config/services.php` fájlban
3. Regisztráld a route-okat a modulok `Module.php` fájljában

## Példa Modul

A package tartalmaz egy példa modult (`Example`), amely bemutatja, hogyan kell modult létrehozni. Az installer megkérdezi, hogy szeretnéd-e megtartani.
