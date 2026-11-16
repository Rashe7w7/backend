# Sistema de Gestión Académica

Aplicación Laravel 12 con vistas Blade, API JSON interna, PostgreSQL y assets compilados con Vite/Tailwind CSS.

## Requisitos

- PHP 8.2 o posterior con las extensiones `pgsql` y `pdo_pgsql`.
- PostgreSQL (el CI usa la versión 17), con las bases `rachel` y `rachel_testing` creadas.
- Composer.
- Node.js y npm.
- Una herramienta de extracción disponible para Composer (`ext-zip`, 7-Zip o `unzip`).

## Instalación

Desde la raíz del proyecto:

```powershell
composer run setup
```

Antes de ejecutar la instalación, crea la base y ajusta las credenciales `DB_*` de `.env` si no usas los valores de ejemplo:

```sql
CREATE DATABASE rachel;
CREATE DATABASE rachel_testing;
```

El comando instala dependencias, crea `.env`, genera la clave, ejecuta las migraciones y seeders sobre PostgreSQL, instala paquetes npm y compila los assets.

Para iniciar el entorno de desarrollo:

```powershell
composer run dev
```

La aplicación estará en <http://127.0.0.1:8000>. El puerto de Vite (`5173` o similar) no es la aplicación web y no debe abrirse directamente.

Las credenciales locales creadas por el seeder son:

- Correo: `admin@example.com`
- Contraseña: `password`

Pueden cambiarse con `APP_ADMIN_EMAIL` y `APP_ADMIN_PASSWORD` antes de ejecutar los seeders. Este usuario automático solo se crea en los entornos `local` y `testing`.

## Comandos de verificación

```powershell
npm run build
php artisan migrate:fresh --seed
composer test
```

`migrate:fresh` elimina los datos de la base actual; úsalo únicamente en desarrollo o testing.

## Producción

En producción se deben proporcionar las variables de entorno reales, usar `APP_ENV=production`, `APP_DEBUG=false` y no ejecutar seeders de datos ficticios.

```powershell
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
```

La API es interna y requiere la misma sesión autenticada que las páginas Blade. CORS solo permite `FRONTEND_URL` (o `APP_URL` si no se define).
