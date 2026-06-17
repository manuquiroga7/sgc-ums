# SGC-UMS — Sistema de Gestión de Certificaciones

Sistema para digitalizar y automatizar la certificación de productos de seguridad
**marítima** (UMS — Universal Monitoring Systems / Portal de Certificación).

> **Estado actual: _scaffold base_.** Backend Laravel + base MySQL + autenticación
> simple + frontend Angular con el shell y el dashboard. Los módulos funcionales
> (wizard de certificación, generación de PDF, historial con filtros y dashboard
> analítico) se implementarán en fases posteriores.

## Stack

| Capa      | Tecnología                          |
|-----------|-------------------------------------|
| Backend   | Laravel 13 (PHP 8.3) · API REST · Sanctum |
| Frontend  | Angular 21 (standalone) · Tailwind CSS v4 |
| Base de datos | MySQL 8 (`sgc_ums`)             |
| Entorno   | WampServer (`C:\wamp64`)            |

## Estructura

```
SGC-UMS/
├── backend/    → Laravel (API REST)
├── frontend/   → Angular (SPA)
└── README.md
```

## Requisitos

- PHP **8.3+** (en WAMP: `C:\wamp64\bin\php\php8.3.28\php.exe`)
- Composer 2
- Node 20+ y npm
- Angular CLI 21 (`npm i -g @angular/cli`)
- MySQL 8 corriendo (servicio `wampmysqld64`)

## Puesta en marcha

### 1. Base de datos
Asegurate de que **MySQL** esté corriendo en WAMP y que exista la base:

```sql
CREATE DATABASE IF NOT EXISTS sgc_ums CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env          # ajustar credenciales de MySQL si hace falta
php artisan key:generate
php artisan migrate --seed     # crea el esquema y un usuario de prueba
php artisan serve --port=8000
```

> En WAMP, ejecutá `php`/`composer` con PHP 8.3:
> `C:\wamp64\bin\php\php8.3.28\php.exe artisan ...`

**Usuario de prueba (seed):** `admin@sgc-ums.com` / `password`

### 3. Frontend (Angular)

```bash
cd frontend
npm install
ng serve --port=4200
```

Abrí **http://localhost:4200**. El dev server proxea `/api` → `http://127.0.0.1:8000`
(ver `frontend/proxy.conf.json`).

## API (scaffold)

| Método | Ruta            | Auth | Descripción                  |
|--------|-----------------|------|------------------------------|
| GET    | `/api/health`   | —    | Healthcheck                  |
| POST   | `/api/login`    | —    | Login, devuelve token Sanctum|
| GET    | `/api/me`       | ✔    | Usuario autenticado          |
| POST   | `/api/logout`   | ✔    | Revoca el token actual       |

## Esquema de base de datos (marítimo)

`buques`, `tipos_certificado`, `productos`, `certificados`, `items_certificado`,
`trabajos_realizados` — según el diagrama entidad-relación provisto.

## Notas técnicas

- El MySQL de este WAMP usa **MyISAM** por defecto (claves máx. 1000 bytes). El proyecto
  fuerza **InnoDB** en `config/database.php` y fija `Schema::defaultStringLength(191)`
  en `AppServiceProvider` para soportar índices `utf8mb4`.
