# Revisión del proyecto: incidencias encontradas

Informe generado al clonar el repositorio, instalarlo desde cero y ponerlo en marcha (PHP 8.4, SQLite, Node 24, Windows 11). Rama: `fix/setup-migrations-and-audit`.

- **Parte 1:** lo que se ha **corregido** en esta rama.
- **Parte 2:** lo que queda **abierto**. Son decisiones de producto o cambios grandes que conviene que decida el equipo.

---

## Parte 1. Corregido en esta rama

### Instalación y arranque

| # | Problema | Efecto | Corrección |
|---|---|---|---|
| 1 | No existía ninguna migración para `users`, `sessions`, `cache`, `jobs`, etc. (se borraron en el commit `32da47e`), y `.env.example` usa `SESSION_DRIVER=database` y `CACHE_STORE=database`. | Con una BD nueva, **cualquier petición web fallaba** (`no such table: sessions`). | Nueva migración `0001_01_01_000000_create_users_cache_jobs_tables.php` con las tablas estándar de Laravel 12, protegida con `hasTable`. |
| 2 | `npm run build` fallaba: `vite.config.js` apuntaba a `resources/css/app.css` y `resources/js/app.js`, que ya no existían. | El job `tests` de CI fallaba siempre en "Build Assets". | Se restauran los dos archivos y se añade el plugin `@tailwindcss/vite`, que ya era dependencia. |
| 3 | 14 vulnerabilidades npm (2 críticas, 10 altas: axios, rollup, postcss, lodash…). | Riesgo de seguridad en dependencias. | `npm audit fix` (solo `package-lock.json`). 0 vulnerabilidades. |
| 4 | 58 avisos de seguridad en paquetes Composer (16 paquetes, uno **crítico** en `laravel/framework`). | Riesgo de seguridad. | `composer update` dentro de las restricciones de `composer.json` (solo `composer.lock`): 0 avisos. Livewire se mantiene en 3.x (`--with livewire/livewire:^3.7`), porque la actualización intentaba subirlo a 4.x, que es un salto de versión mayor. |
| 5 | `config/filesystems.php:44`: `rtrim(env('APP_URL'))` con `APP_URL` vacío. | Aviso *Deprecated* en cada comando de PHP 8.4. | Valor por defecto `http://localhost`. |
| 6 | No había README y los pasos de instalación no estaban documentados. | Difícil de poner en marcha. | Nuevo `README.md`. |

### Migraciones (`migrate:fresh --seed` fallaba en una BD nueva)

| Archivo | Problema | Corrección |
|---|---|---|
| `2026_01_14_150000_update_indicadores_schema.php` | Borraba la columna `categoria_indicador_id` sin borrar antes su índice (falla en SQLite). | Se borra el índice primero. |
| `2026_01_17_remove_categoria_from_indicador_subtipos.php` | Borraba la columna antes que su clave foránea (falla en SQLite y MySQL). | Se borra la FK primero. |
| `2026_01_17_200000_add_nombre_to_indicador_relations.php` | Usaba la tabla `indicador_anio`, que no existe (la real es `indicador_anno`). | Nombre corregido. |
| *(faltaba)* `2026_01_19_create_anio_asignatura_table.php` | Ninguna migración creaba `anio_asignatura`, así que `fix_anio_asignatura_columns` fallaba. | Nueva migración que la crea. |
| `2026_01_19_fix_anio_asignatura_columns.php` | Consultaba `pg_indexes`, que solo existe en PostgreSQL. | Ahora usa `Schema::hasIndex()`, portable. |
| `2026_01_19_create_planes_informatica.php` | Con la BD vacía insertaba una carrera con `facultad_id=1`, que no existía, y usaba como id el número de filas que devuelve `insertOrIgnore`. | Busca la carrera, la crea solo si existe la facultad y usa el id real. |
| `2026_01_19_add_codigo_horas_to_asignatura.php` | `down()` borraba la columna antes que su índice único. | Orden corregido. |
| *(nueva)* `2026_01_24_000000_fix_indicador_anno_nombre_and_nullable.php` | `indicador_anno` no tenía `nombre`, y `anio_academico_id` y `curso_academico_id` eran NOT NULL aunque el código inserta `null`. | Añade `nombre` (rellenado desde `indicador`) y hace nulables las dos FK. Sirve también para BDs ya migradas. |

`FakeDataSeeder` estaba desfasado respecto al esquema y se ha adaptado:

- `estudiante_ayudante` ahora usa `profesor_asignatura_id`.
- Se rellena el campo `nombre` obligatorio en las tablas `indicador_*`.
- Los nombres de indicador son únicos.
- Ya no se generan duplicados en `profesor_categoria_grado`.

### Código de la aplicación

| Archivo | Problema | Efecto | Corrección |
|---|---|---|---|
| `app/Http/Controllers/Api/DataController.php` | Las reglas `unique:`/`exists:` apuntaban a tablas en plural (`facultades`, `carreras`…), que no existen. | **Todos los POST de la API devolvían 500.** | Tablas reales en singular. |
| `DataController::storeIndicador` | No validaba que el nombre fuera único (sí hay índice único en BD). | Un nombre duplicado daba 500. | `unique:indicador,nombre`, ahora 422. |
| `DataController::storeIndicadoresCarrera` e `IndicadorController::store` | No rellenaban `nombre` (NOT NULL) en las tablas `indicador_*`. | 500 al asignar indicadores. | Se copia el nombre del indicador padre. |
| `IndicadorController::storeValores` | Guardaba valores no numéricos en una columna `decimal`. | Error o datos corruptos según el motor. | Se ignoran los valores no numéricos. |
| `app/Models/BajasEstudiantes.php` | El archivo declaraba la clase `BajaEstudiante` (incumple PSR-4). | La clase **nunca se cargaba**. | Renombrado a `BajaEstudiante.php`. |
| `BajasController.php` | Cualificaba columnas con tablas en plural (`facultades.id`…). | Error SQL. | Nombres en singular. |
| `AnioAcademico::bajas()` | `hasManyThrough` mal definido: comparaba `estudiante.grupo_id` con `anio_academico.id`. | Bajas por año incorrectas. | Consulta correcta a través de `alumno.grupo`. |
| `Alumno::alumnoAyudante()` | Apuntaba a la clase inexistente `EstudianteAyudante`. | Error al usar la relación. | Usa `AlumnoAyudante`. |
| `Version` / `Modificacion` | La tabla pivote se llamaba `version_modificaciones` (la real es `version_modificacion`). | Error SQL. | Corregido. |
| `Indicador` | Relaciones a clases inexistentes (`IndicadorGrupoValor`…), sin uso. | Código muerto que fallaría al usarse. | Eliminadas. |
| `resources/views/indicadores.blade.php` | Llamaba 4 veces a `actualizarAvisoEspecificacion()`, función que ya no existe. | Error JS al cargar la página. Al añadir o limpiar indicadores se cortaba la ejecución y no salía el aviso de confirmación. | Llamadas eliminadas. |

### Tests

| Problema | Corrección |
|---|---|
| 22 de 33 tests fallaban: probaban las pantallas de login, registro, 2FA y perfil del starter kit, que se habían eliminado. | Se eliminan esos tests y se añaden tests de humo para las páginas reales y la API JSON. Suite en verde. |

### Higiene del repositorio

- 11 scripts de diagnóstico sueltos en la raíz (`check_db.php`, `audit_db.php`, `seed_data.php`, …) se mueven a `scripts/dev/`, con rutas corregidas y un README.
- Fuera del control de versiones: `server.log` (un log de PowerShell) y 48 vistas Blade compiladas (`storage/framework/views/*.php`). `.gitignore` actualizado.

---

## Parte 2. Incidencias abiertas (no corregidas)

### 🔴 Alta

1. **No hay autenticación.**
   - `POST /login` solo redirige al dashboard (`routes/web.php:23-26`) y ninguna ruta usa el middleware `auth`.
   - Todo es público, incluidas las escrituras y los borrados: `DELETE /api/indicadores/{id}` y `DELETE /categorias/{id}`. Este último borra en cascada todos los indicadores de la categoría.
   - **Recomendación:** activar el login de Fortify, que ya está instalado, y proteger `web.php` y `api.php` con `auth`, o con `auth:sanctum` para la API.
2. **La API acepta peticiones de cualquier origen y no tiene límite de peticiones.**
   - `config/cors.php` tiene `allowed_origins => ['*']`, el grupo `api` no tiene CSRF y no hay `throttle`.
   - Cualquier web puede llamar a la API desde el navegador de un usuario.
3. **`/tablas` vuelca el contenido de todas las tablas** (5 filas de cada una), incluida `users` (hashes de contraseña y secretos 2FA) y es pública.
   - **Recomendación:** eliminarla o limitarla a `APP_ENV=local`.
4. **`GET /indicadores/bajas` devuelve 500:** `BajasController` renderiza la vista `indicadores.bajas`, que no existe. La lógica del controlador ya está corregida; solo falta la vista.
5. **Conflicto con las rutas de Fortify.**
   - Fortify registra `GET`/`POST /login` con el nombre `login`, y `web.php` también llama `login` a `/`.
   - `GET /login` da 500, porque las vistas del starter kit usan rutas que no existen (`login.store`, `home`, `profile.edit`).
   - `php artisan route:cache` puede fallar por el nombre duplicado.
   - **Recomendación:** decidir entre usar Fortify de verdad o desinstalarlo junto con las vistas `livewire/auth` y `livewire/settings`.

### 🟠 Media

6. **Endpoints de depuración expuestos:** `/api/debug/planes-by-carrera/{id}` y `/api/indicadores-debug` (`routes/api.php`).
7. **Se devuelven al cliente mensajes de excepción internos**, que pueden revelar SQL y rutas de archivos: `DataController` (~líneas 66, 108, 372) y `AlumnoAyudanteController` (~103-127). Además `APP_DEBUG=true` en `.env.example`.
8. **`AlumnoAyudanteController::storeFromForm`** no usa `$request->validate()`, registra en el log la petición completa y busca en `profesor_asignatura` sin filtrar por `curso_academico_id`.
9. **No hay transacciones al crear indicadores.** `IndicadorController::store` y `DataController::createAndAssignIndicador` crean el `Indicador` padre y después la fila hija. Si la segunda falla, queda un indicador huérfano. Hay que envolverlo en `DB::transaction`.
10. **Dos modelos para la misma tabla:** `Plan` y `PlanEstudio` usan `plan_estudio`, y `Plan::$fillable` incluye columnas que no existen (`codigo`, `version`).
11. **`migrate:rollback` no funciona** más allá de unas pocas migraciones.
    - `2026_01_18_modify_estudiante_ayudante_to_profesor_asignatura::down()` añade una columna NOT NULL sin valor por defecto.
    - `2026_01_15_remove_unique_nombre_indicadores::down()` usa la tabla `indicadores`, que no existe.
    - En la práctica hay que usar `migrate:fresh`.
12. **Orden de migraciones frágil.** Muchas migraciones solo llevan fecha (`2026_01_15_*.php`) y se ordenan alfabéticamente dentro del mismo día. Por ejemplo, `cleanup_duplicate_carrera` se ejecuta antes que `create_planes_informatica`, que era la que creaba los duplicados. Para el futuro: usar siempre `php artisan make:migration`, que pone fecha y hora.
13. **`ESTRUCTURA_BD.md` está desactualizado:** usa nombres de tabla en plural y modelos antiguos (`bajas_estudiantes`, `alumno_ayudante`…).

### 🟡 Baja

14. **Escrituras duplicadas para alumno ayudante.** `web.php` (`/alumnoayudante`, con `store`) y `api.php` (`/api/alumnoayudante`, con `storeFromForm`) validan de forma distinta. La vista solo usa la de la API, así que las rutas web POST/DELETE no se usan.
15. **`web.php` define rutas `/api/...`** (`profesores-by-asignatura`, `asignaturas-by-profesor`), que deberían estar en `api.php`.
16. **Código muerto:**
    - `TablesController` (sin ruta y con una vista inexistente).
    - `indicadores/create.blade.php` y `show.blade.php` (sin ruta; `show` usa rutas inexistentes).
    - Las rutas `categorias.*`, que ninguna vista con ruta usa.
    - El `use App\Models\Plan` sin uso en `web.php`.
17. **`/planestudio` pasa a la vista variables que no usa** (`$carreras`, `$anios`), porque la vista carga sus datos por API.
18. **`FakeDataSeeder`, bloque "INDICADORES POR FACULTAD":** recorre los IDs de categoría en vez de los de indicador.
19. **`populate_anio_asignatura` escribe filas en la tabla `migrations`** a mano y no crea datos en una BD vacía.
20. **Las vistas cargan Tailwind desde `cdn.tailwindcss.com`,** que no está pensado para producción y obliga a tener conexión a Internet. El build de Vite ya funciona; se podría pasar a `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
21. **`package.json` fija versiones de binarios solo para Linux** en `optionalDependencies` (`@rollup/rollup-linux-x64-gnu@4.9.5`). Es innecesario y puede chocar con la versión real de rollup.
22. **Las migraciones no se han probado en MySQL ni PostgreSQL.** Se usan llamadas estándar del Schema builder, pero conviene ejecutarlas una vez sobre MySQL si es el motor de producción.

---

## Cómo se verificó

- `composer install`, `npm install` y `npm run build`, todos correctos.
- `php artisan migrate:fresh --seed` sobre SQLite nuevo, correcto en ejecuciones repetidas (52 tablas, unos 3200 estudiantes de ejemplo).
- `php artisan serve` y peticiones a todas las páginas y endpoints GET de la API: 200 en todos excepto `/indicadores/bajas` y `/login` (incidencias 4 y 5).
- POST de la API: 201 al crear, 422 con duplicados o FK inexistentes.
- Revisión en navegador de `/indicadores`, `/planestudio`, `/alumnoayudante` y `/dashboard`, sin errores JS tras la corrección.
- `./vendor/bin/pest`: suite en verde.
