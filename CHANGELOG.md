# Changelog

## [Unreleased]

### Added
- ✅ **Frontend inicializálás** - Teljes implementáció React, Vue és Vanilla JS támogatással
  - Automatikus Vite projekt inicializálás
  - Vite proxy konfiguráció a backend-hez
  - Frontend `.env` fájl generálása
- ✅ **Git inicializálás** - Automatikus Git repository inicializálás
  - `.gitignore` fájl automatikus létrehozása
  - Kezdeti commit létrehozása
- ✅ **Database connection test** - Adatbázis kapcsolat tesztelése az installer végén
  - SQLite fájl automatikus létrehozása
  - MySQL/PostgreSQL kapcsolat validálása
- ✅ **Environment validation** - Konfiguráció validálása
  - JWT Secret hossz ellenőrzése (minimum 32 karakter)
  - Adatbázis név ellenőrzése MySQL/PostgreSQL esetén
  - Figyelmeztetések hiányzó vagy hibás beállításokra

### Changed
- A "Git hooks telepítése?" kérdés átnevezve "Git inicializálása?"-ra
- Jobb hibaüzenetek és visszajelzések az installer során

### Fixed
- Szintaktikai hiba javítva a `.env` fájl generálásánál

## [1.0.0] - 2024-01-XX

### Added
- Alapvető interaktív installer
- Adatbázis konfiguráció (SQLite, MySQL, PostgreSQL)
- Frontend konfiguráció (React, Vue, Vanilla JS, vagy nincs)
- JWT Secret generálás
- Környezeti változók beállítása
- Példa modul opcionális telepítése
