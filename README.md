# Sistema de Indicadores Académicos — Backend

Aplicación web en **Laravel 12** para gestionar la estructura académica de una institución (facultades, departamentos, carreras, planes de estudio, años, grupos, estudiantes, profesores) y evaluar **indicadores de calidad** a distintos niveles (facultad, departamento, carrera, año, grupo).

Incluye vistas Blade (Tailwind por CDN), una API JSON en `/api/*` consumida por esas vistas y datos de ejemplo generados con un seeder.

> ⚠️ **Estado actual:** la aplicación **no tiene autenticación real** (el login solo redirige al dashboard) y todas las rutas, incluidas las de escritura y borrado, son públicas. Úsala solo en local. Ver [Problemas conocidos](#problemas-conocidos).

---

## Tecnologías

| Capa | Herramienta |
|---|---|
| Backend | PHP 8.2+ (probado con 8.4), Laravel 12, Laravel Fortify, Livewire / Volt / Flux (starter kit) |
| Base de datos | SQLite por defecto (también MySQL/MariaDB) |
| Frontend | Blade + Tailwind CSS (CDN en las vistas; Vite 7 + `@tailwindcss/vite` para el build) |
| Tests | Pest 4 |
| Estilo de código | Laravel Pint |

---

## Requisitos

- **PHP ≥ 8.2** con las extensiones: `pdo_sqlite` (o `pdo_mysql`), `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `curl`, `zip`, `intl`, `tokenizer`, `xml`, `ctype`, `bcmath`.
- **Composer 2**
- **Node.js ≥ 20** (CI usa 22) y **npm**
- Git

> **Windows / XAMPP:** muchas instalaciones de XAMPP traen PHP 7.x, que **no sirve** para este proyecto. Comprueba con `php -v`. Si es menor que 8.2, instala PHP 8.4 (por ejemplo, el zip *NTS x64* de [windows.php.net](https://windows.php.net/download/), o [Laravel Herd](https://herd.laravel.com/)) y activa en `php.ini` `extension_dir = "ext"` y las extensiones de la lista anterior.

---

## Instalación

```bash
git clone https://github.com/Rashe7w7/backend.git
cd backend
```

### 1. Dependencias

```bash
composer install
npm install
```

### 2. Variables de entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Base de datos

**Opción A: SQLite (por defecto, sin servidor)**

`.env.example` ya trae `DB_CONNECTION=sqlite`. Solo crea el archivo:

```bash
touch database/database.sqlite
```

(En PowerShell: `New-Item database/database.sqlite`.)

**Opción B: MySQL / MariaDB**

Crea una base de datos vacía y edita `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=indicadores
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migraciones y datos de ejemplo

```bash
php artisan migrate --seed
```

Esto crea todas las tablas y ejecuta `DatabaseSeeder`, que llama a:

- `CursoAcademicoSeeder`: cursos académicos (p. ej. 2024-2025).
- `FakeDataSeeder`: facultades, carreras, planes, años, grupos, estudiantes, profesores, indicadores, etc., con Faker (`es_ES`).

Para empezar de cero en cualquier momento:

```bash
php artisan migrate:fresh --seed
```

### 5. Assets (opcional)

Las vistas actuales cargan Tailwind desde CDN, así que el build no es imprescindible para usar la app, pero sí lo exige CI:

```bash
npm run build
```

> Atajo: `composer run setup` ejecuta `install`, crea `.env`, genera la key, migra y compila los assets (sin seeders).

---

## Ejecución

**Todo junto** (servidor + cola + Vite, con `concurrently`):

```bash
composer run dev
```

**Solo el servidor:**

```bash
php artisan serve
```

Abre <http://127.0.0.1:8000>.

### Páginas principales

| Ruta | Descripción |
|---|---|
| `/` | Login (de momento solo redirige, sin validar credenciales) |
| `/dashboard` | Panel principal |
| `/indicadores` | Gestión de indicadores, categorías y valores |
| `/planestudio` | Planes de estudio por carrera |
| `/alumnoayudante` | Asignación de alumnos ayudantes a profesor/asignatura |
| `/profesor` | Profesores |
| `/roles` | Roles |
| `/tablas` | Visor de todas las tablas de la BD (solo desarrollo) |
| `/up` | Health check de Laravel |

### API JSON (`/api/*`)

| Método | Endpoint | Descripción |
|---|---|---|
| GET/POST | `/api/facultades` | Listar / crear facultades |
| GET/POST | `/api/departamentos` | Listar / crear departamentos |
| GET/POST | `/api/carreras` | Listar / crear carreras |
| GET/POST | `/api/planes-estudio` | Listar / crear planes de estudio |
| GET | `/api/planes-by-carrera/{carreraId}` | Planes de una carrera |
| GET/POST | `/api/disciplinas`, `/api/asignaturas`, `/api/curriculos` | Estructura curricular |
| GET | `/api/cursos-academicos` | Cursos (2024-2025, …) |
| GET/POST | `/api/anios-academicos` | Años de carrera (1.º, 2.º, …) |
| GET/POST | `/api/grupos`, `/api/alumnos` | Grupos y estudiantes |
| GET/POST | `/api/categorias-indicador` | Categorías de indicador |
| GET/POST | `/api/indicadores` | Indicadores |
| DELETE | `/api/indicadores/{id}` | Borrar indicador (en cascada) |
| POST | `/api/indicadores/create-and-assign` | Crear un indicador y asignarlo a una entidad |
| GET/POST | `/api/indicadores-carrera` | Indicadores por carrera |
| GET | `/api/tabla/{alias}` | Volcado genérico (`facultades`, `carreras`, `indicadores`, …) |
| POST/DELETE | `/api/alumnoayudante[/{id}]` | Alta / baja de alumno ayudante |

Lista completa: `php artisan route:list`.

### Comandos Artisan propios

| Comando | Qué hace |
|---|---|
| `php artisan db:audit` | Auditoría de integridad de la base de datos |
| `php artisan db:seed-missing` | Inserta disciplinas, asignaturas y relaciones profesor-asignatura que falten |
| `php artisan planes:update-informatica` | Renombra los planes de Informática (a, b, c, d) |
| `php artisan planes:verify` | Verifica los planes de estudio y las carreras |

---

## Tests y calidad

```bash
php artisan test      # o: composer test / ./vendor/bin/pest
./vendor/bin/pint     # formateo (Laravel Pint)
```

Los tests usan SQLite en memoria (ver `phpunit.xml`), así que no tocan tu base de datos local.

CI (GitHub Actions, `.github/workflows/`): `tests.yml` ejecuta el build de Vite y Pest; `lint.yml` ejecuta Pint. Los dos se lanzan en push/PR a `main` y `develop`. Usan los secretos `FLUX_USERNAME` / `FLUX_LICENSE_KEY` (solo hacen falta si se usa Flux Pro).

---

## Estructura del proyecto

```
app/
  Console/Commands/      Comandos artisan (db:audit, planes:*, …)
  Http/Controllers/      Controladores web (Indicador, Bajas, Categoria, AlumnoAyudante)
  Http/Controllers/Api/  DataController: toda la API JSON
  Models/                Modelos Eloquent (nombres de tabla en singular: facultad, carrera, indicador_carrera, …)
database/
  migrations/            Esquema (historial incremental)
  seeders/               CursoAcademicoSeeder, FakeDataSeeder
resources/views/         Vistas Blade (dashboard, indicadores, tables/*, auth/login)
routes/
  web.php                Páginas
  api.php                API JSON
scripts/dev/             Scripts de diagnóstico locales (no forman parte de la app)
ESTRUCTURA_BD.md         Descripción funcional del modelo de datos
```

> **Nota sobre nombres de tablas:** `ESTRUCTURA_BD.md` usa nombres en plural (`facultades`, `carreras`, …), pero las tablas reales están en **singular** (`facultad`, `carrera`, `plan_estudio`, `estudiante`, `indicador`, …) tras las migraciones de renombrado. Consulta `database/migrations` o `/tablas` para ver el esquema real.

### Modelo de datos (resumen)

```
Facultad
├── Departamento ── Profesor (miembro / jefe)
├── Carrera
│   ├── Plan de estudio ── Disciplina ── Asignatura ── Currículo
│   └── Año académico ── Grupo ── Estudiante (bajas, evaluación integral, alumno ayudante)
└── Indicadores (por facultad, departamento, carrera, año, grupo) ── valores por curso académico
```

---

## Problemas conocidos

Revisión hecha al poner en marcha el proyecto. El detalle completo está en [`ISSUES.md`](ISSUES.md), y las mejoras propuestas (hoja de ruta) en [`TODO.md`](TODO.md).

- **Sin autenticación:** el login es ficticio y ninguna ruta usa el middleware `auth`. Las escrituras y borrados (`DELETE /api/indicadores/{id}`, `DELETE /categorias/{id}`, …) son públicos, y `config/cors.php` permite cualquier origen.
- **Endpoints de depuración expuestos:** `/tablas`, `/api/debug/planes-by-carrera/{id}`, `/api/indicadores-debug`.
- **Conflicto con Fortify / starter kit:** hay dos rutas llamadas `login`, `GET /login` da 500 y los tests de auth/settings del starter kit fallan porque sus rutas (`profile.edit`, …) no existen.
- **Vista inexistente:** `GET /indicadores/bajas` renderiza `indicadores.bajas`, que no existe.
- `APP_DEBUG=true` y algunos controladores devuelven el mensaje de la excepción al cliente.

---

## Solución de problemas

| Síntoma | Causa / solución |
|---|---|
| `composer install` falla por versión de PHP | Necesitas PHP ≥ 8.2 (`php -v`). Ver la nota sobre XAMPP. |
| `could not find driver` | Activa `pdo_sqlite` (o `pdo_mysql`) en `php.ini`. |
| `Database file at path [...] does not exist` | Crea `database/database.sqlite` (paso 3). |
| `no such table: sessions` / `cache` | Ejecuta `php artisan migrate`. |
| `Vite manifest not found` | `npm run build` o `npm run dev`. |
| Cambios en `.env` no se aplican | `php artisan config:clear` |
