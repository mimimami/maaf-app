# MAAF CLI Parancsok

A MAAF framework tartalmaz egy CLI tool-t (`maaf`), amely segít a fejlesztésben.

## Telepítés

A CLI tool automatikusan települ a `maaf/app` package telepítésekor.

## Parancsok

### `php maaf serve`

Indítja a development szervert.

```bash
php maaf serve
# Server: http://localhost:8000

# Egyedi host és port
php maaf serve 0.0.0.0 8080
```

### `php maaf migrate`

Futtatja az adatbázis migrációkat.

```bash
php maaf migrate
```

### `php maaf seed`

Futtatja a seed adatokat.

```bash
php maaf seed
```

### `php maaf make:module <ModuleName>`

Létrehoz egy új modult.

```bash
php maaf make:module UserManagement
```

Ez létrehozza:
```
src/Modules/UserManagement/
├── Module.php
├── Controllers/
│   └── UserManagementController.php
├── Services/
│   └── UserManagementService.php
└── Repositories/
    └── UserManagementRepository.php
```

### `php maaf make:controller <ControllerName>`

Létrehoz egy új kontrollert.

```bash
php maaf make:controller UserController
```

### `php maaf route:list`

Listázza az összes regisztrált route-ot.

```bash
php maaf route:list
```

Kimenet példa:
```
GET     /                    WelcomeController@index
GET     /health              HealthController@health
GET     /api-docs            ApiDocsController@index
POST    /auth/login          AuthController@login
```

## További Parancsok (Tervezett)

- `php maaf make:service <ServiceName>` - Service létrehozása
- `php maaf make:model <ModelName>` - Model létrehozása
- `php maaf make:migration <MigrationName>` - Migration létrehozása
- `php maaf cache:clear` - Cache törlése
- `php maaf config:cache` - Config cache

