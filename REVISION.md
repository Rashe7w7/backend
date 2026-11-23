# Revisión del proyecto: Sistema de Indicadores Académicos (segunda revisión)

Revisión de solo lectura de la rama `main` del repositorio `backend` (último commit `e6cddd7`, «home view», 2026-11-17). Desde la revisión anterior has fusionado en `main` las ramas `issues-todo`, `indicadores` y `alumno-ayudante`. La rama del profesor `origin/fix/setup-migrations-and-audit` **no** se fusionó, así que he comprobado una a una cuáles de sus correcciones has vuelto a aplicar por tu cuenta.

No he modificado, instalado ni ejecutado nada del repositorio: ni `artisan`, ni `composer`, ni `npm`, ni los tests. En esta máquina no hay PHP, así que tampoco he podido pasar `php -l`. Todos los hallazgos se han comprobado leyendo el código de `main` y llevan referencia `archivo:línea`. Cuando algo es una deducción y no una lectura directa (por ejemplo, qué tests fallan), lo indico como **(inferido)**.

Las rutas son relativas a `backend/`.

---

## 0. Resumen

**Qué hace.** Es una aplicación web interna para gestionar indicadores académicos de una universidad:

- **Estructura académica.** Facultades, departamentos, carreras, planes de estudio, disciplinas, asignaturas, años académicos (con `nivel` 1-5), grupos, cursos académicos, estudiantes y profesores.
- **Indicadores.** Se definen con nombre, tipo de valor (categoría: numérico, porcentual…) y una **especificación** (`grupo`, `departamento`, `carrera`, `nivel_academico`, `facultad`). Cada rol solo puede crear y gestionar ciertos tipos (`app/Models/User.php:47-59`).
- **Alumnos ayudantes.** Designación de estudiantes a una asignatura-profesor-curso, con informes por carrera, facultad y año.
- **Bajas.** Estadísticas de bajas por causa, año, carrera y grupo.
- **Importación** de estudiantes, profesores y asignaturas desde CSV/XLSX con vista previa, y **exportación** de indicadores a Excel (XML) y PDF.
- **Usuarios y roles:** administrador, PPA, jefe de departamento, decano, rector, vicerrector y secretaria.

**Stack.**

- Laravel 12 (Blade + una API JSON interna con sesión), Fortify para el login, PostgreSQL.
- Vite 7 + Tailwind 4, sin CDN. Mucho JavaScript en línea en las vistas (`indicadores.blade.php`, 816 líneas; `planestudio.blade.php`, 1.489 líneas).
- Pest con 80 casos de test y CI en GitHub Actions contra PostgreSQL 17 (`.github/workflows/tests.yml:17-38`), más Pint en modo `--test` (`lint.yml`).

**Valoración general.** Has avanzado **mucho** desde la primera revisión. Ahora hay login real, todas las rutas exigen sesión, el registro público está cerrado, la API tiene CSRF, CORS restringido y límite de peticiones, las migraciones vuelven a funcionar en una BD nueva y ya hay Form Requests, CI con PostgreSQL y una batería de tests de verdad. Has reaplicado por tu cuenta casi todas las correcciones de la rama del profesor.

El problema es que **la fusión final de las tres ramas (17-11-2026) rompió la integración**, y, a juzgar por el código, los tests no se ejecutaron después **(inferido)**:

1. **La gestión de usuarios no funciona.** `/roles` da error 500 y crear o editar un usuario también falla (B1, B2). La fusión de `alumno-ayudante` sustituyó `User.php` y se perdieron `roles()`, `hasRole()`, `asignacionesPpa()` y `alcancesAcademicos()` (B3).
2. **Se perdió el control de alcance.** Las asignaciones PPA a grupos, los alcances de la secretaria, la pantalla de auditoría y la exportación CSV auditada desaparecieron de las rutas en la fusión de `indicadores` (`9d35f4d`). Ahora un PPA puede designar alumnos ayudantes en cualquier grupo y cualquier usuario autenticado ve todos los estudiantes y bajas (S2, S3).
3. **Hay cuentas con contraseñas conocidas que se crean también en producción** (S1).
4. **La funcionalidad central está a medias.** Los valores que se escriben en la tabla de indicadores no se guardan nunca, y el backend solo admite **un valor global por indicador**, sin entidad ni curso (B5, U3).

**Puntos fuertes**

- **Autenticación y superficie de ataque bien cerradas.** Todo `web.php` va dentro de `auth` (`routes/web.php:20`). La API usa `web` + `auth` + `throttle:60,1` (`routes/api.php:7`), así que tiene CSRF (lo prueba `tests/Feature/SecurityRegressionTest.php:83-93`). El registro está desactivado (`config/fortify.php:146-148`) y el login tiene límite de 5 intentos por minuto (`app/Providers/FortifyServiceProvider.php:66-70`).
- **Permisos de escritura por departamento bien pensados.** `EnsureDepartmentScope` sube por la jerarquía (grupo → año → carrera → departamento) y exige que **todos** los departamentos del recurso pertenezcan al jefe (`app/Http/Middleware/EnsureDepartmentScope.php:34-39,107-150`). Los Gates están en un solo sitio (`app/Providers/AppServiceProvider.php:29-43`).
- **Migraciones y datos de referencia saneados.** Has corregido todas las migraciones que fallaban (ver 0.1). La migración `2026_11_17_212847_normalize_academic_schema_constraints.php` comprueba duplicados antes de crear índices únicos compuestos (`:12-31,52-66`). `AcademicCatalogSeeder` es idempotente y tiene test (`tests/Feature/AcademicCatalogSeederTest.php`).
- **Importación con vista previa y transacción.** Se valida fila a fila y no se importa nada si hay errores (`app/Http/Controllers/ImportController.php:33-64`).
- **Mejor manejo de errores y rendimiento.** Las `QueryException` de la API devuelven un mensaje genérico (`bootstrap/app.php:27-35`), `preventLazyLoading` está activo fuera de producción (`AppServiceProvider.php:27`) y `/api/alumnos` está paginado con un máximo de 100 (`app/Http/Requests/ListAlumnosRequest.php:26`).

### 0.1 Evolución desde la revisión anterior

`ISSUES.md` y `TODO.md` solo existen en la rama del profesor (`git show origin/fix/setup-migrations-and-audit:ISSUES.md`). No están en `main`.

**Incidencias abiertas de `ISSUES.md` (parte 2)**

| # | Incidencia | Estado | Evidencia en `main` |
|---|---|---|---|
| 1 | No hay autenticación | ✅ Resuelto | `routes/web.php:20`, `routes/api.php:7`, `FortifyServiceProvider.php:48` |
| 2 | API con CORS `*` y sin límite | ✅ Resuelto | `config/cors.php:6`; `routes/api.php:7` (`throttle:60,1` y `web`, con CSRF) |
| 3 | `/tablas` público y con `users` | ✅ Resuelto | `routes/web.php:51-57`: solo en `local` y excluye `users`. Ojo: `.env.example:2` trae `APP_ENV=local` (ver S1). |
| 4 | Falta la vista de bajas | ✅ Resuelto | `resources/views/indicadores/bajas.blade.php`. Los datos no están filtrados por alcance (S2). |
| 5 | Conflicto con las rutas de Fortify | ✅ Resuelto | `/` se llama `home` (`web.php:16-18`), el login usa `auth.login` y el registro está desactivado. Quedan vistas muertas en `livewire/auth` (`register`, `login`, `two-factor-challenge`, `verify-email`, `confirm-password`) y en `livewire/settings` (B13). `forgot-password` y `reset-password` sí se usan (`FortifyServiceProvider.php:53-54`). |
| 6 | Endpoints de depuración | 🟡 Parcial | Ya no están en `api.php`, pero siguen en `public/`: `api-test.php`, `test-api.html`, `debug-carreras.html`, etc. (S8) |
| 7 | Mensajes de excepción al cliente | ✅ Resuelto | `bootstrap/app.php:27-35`; `DataController.php:68-70,113`; `.env.example:4` (`APP_DEBUG=false`) |
| 8 | `storeFromForm` sin validar | ✅ Resuelto | Sustituido por `StoreAlumnoAyudanteRequest`. Pero falta comprobar el alcance del PPA (S3). |
| 9 | Sin transacciones al crear indicadores | ✅ Resuelto por rediseño | Ya no se crean filas hijas (`DataController.php:369-374`). Hay un flujo nuevo no atómico en `UserController.php:51-53` (B2). |
| 10 | `Plan` y `PlanEstudio` duplicados | ✅ Resuelto | `app/Models/Plan.php` ya no existe |
| 11 | `migrate:rollback` roto | 🟡 Parcial | Arreglados `2026_01_18_modify_…:46-47` y `2026_01_15_remove_unique_…:25`. La nueva `2026_11_16_220658:15-16` borra datos y su `down()` no puede recuperarlos (S10). |
| 12 | Orden de migraciones frágil | 🟡 Parcial | Las migraciones nuevas tienen hora (`2026_11_*`). Las antiguas sin hora siguen igual. |
| 13 | `ESTRUCTURA_BD.md` desactualizado | ❌ Pendiente | Sigue con tablas en plural: `ESTRUCTURA_BD.md:30-127` (`facultades`, `bajas_estudiantes`, `alumnos`…) |
| 14 | Escrituras duplicadas de alumno ayudante | ✅ Resuelto | Un solo `POST` (`routes/api.php:39`) |
| 15 | Rutas `/api` en `web.php` | ✅ Resuelto | Movidas a `routes/api.php:42-43` |
| 16 | Código muerto | ❌ Pendiente (ha crecido) | Siguen `TablesController.php` e `indicadores/create.blade.php` y `show.blade.php`. Tras la fusión hay más código muerto (B3). |
| 17 | `/planestudio` con variables sin uso | ✅ Resuelto | `routes/web.php:29` (`Route::view`) |
| 18 | `FakeDataSeeder` | ✅ Resuelto | Sustituido por `AcademicCatalogSeeder` y `DemoAcademicDataSeeder` |
| 19 | `populate_anio_asignatura` escribe en `migrations` | ✅ Resuelto | Ya no lo hace (`2026_01_19_populate_anio_asignatura.php`) |
| 20 | Tailwind por CDN | ✅ Resuelto | `@vite([...])` en las 15 vistas; ninguna usa `cdn.tailwindcss.com` |
| 21 | `optionalDependencies` solo para Linux | ✅ Resuelto | `package.json` |
| 22 | Migraciones no probadas en un motor real | ✅ Resuelto | PostgreSQL en CI (`tests.yml:17-38`) y en `phpunit.xml:26-32` |

**Puntos P1 de `TODO.md`**

| P1 | Estado | Evidencia |
|---|---|---|
| Login real con Fortify | ✅ Resuelto | `config/fortify.php:146-148`; `resources/views/auth/login.blade.php:66,113` (incluye «¿Olvidé contraseña?») |
| Proteger `web.php` y `api.php` | ✅ Resuelto | `web.php:20`; `api.php:7` |
| Roles y permisos con Gates | 🟡 Parcial | Los Gates funcionan (`AppServiceProvider.php:29-43`), pero la tabla `role_user`, los alcances y las asignaciones PPA quedaron rotos tras la fusión (B1-B3) |
| CORS y `throttle` | ✅ Resuelto | Ver #2 |
| Quitar `/tablas` y las rutas de depuración | 🟡 Parcial | Ver #3 y #6 |

**Correcciones de la rama del profesor (no fusionada)**

| Corrección | ¿Está en `main`? | Evidencia |
|---|---|---|
| Migración de `users`, `sessions`, `cache` y `jobs` | ✅ Reaplicada | `database/migrations/0001_01_01_000000_create_users_cache_jobs_tables.php` |
| Entradas de Vite (`app.css`, `app.js`) | ✅ Reaplicada | `vite.config.js:9-12`; existen `resources/css/app.css` y `resources/js/app.js` |
| `dropIndex` antes de `dropColumn` | ✅ | `2026_01_14_150000_update_indicadores_schema.php:37` |
| FK antes que la columna | ✅ | `2026_01_17_remove_categoria_from_indicador_subtipos.php:18-19,25-26` |
| `indicador_anio` → `indicador_anno` | ✅ | `2026_01_17_200000_add_nombre_to_indicador_relations.php:16,36` |
| Crear `anio_asignatura` | ✅ | `2026_01_19_000000_create_anio_asignatura_table.php` |
| `pg_indexes` → `Schema::hasIndex` | ✅ | `2026_01_19_fix_anio_asignatura_columns.php:37` |
| `create_planes_informatica` con la BD vacía | ✅ | `2026_01_19_create_planes_informatica.php:13-18` (busca la facultad y usa `insertGetId`) |
| `indicador_anno.nombre` y FK nulables | ✅ | `2026_01_24_000000_fix_indicador_anno_nombre_and_nullable.php` |
| Tablas en singular en las reglas de validación | ✅ En `DataController` | `DataController.php:36,52,121,152,212,229,268,301,332-334`. ❌ Pero siguen en plural los comandos `db:audit` y `db:seed-missing` (B8). |
| `BajasEstudiantes.php` → `BajaEstudiante.php` | ✅ | `app/Models/BajaEstudiante.php` |
| `AnioAcademico::bajas()` | ✅ | `app/Models/AnioAcademico.php:42-47` |
| `Alumno::alumnoAyudante()` | ✅ | `app/Models/Alumno.php:30-33` |
| Pivote `version_modificacion` | ✅ | `Version.php:15`; `Modificacion.php:20` |
| Llamadas a `actualizarAvisoEspecificacion()` | ✅ | 0 apariciones en `indicadores.blade.php` |
| `APP_URL` vacío en `filesystems.php` | ✅ | `config/filesystems.php:44` |
| Tests del starter kit rotos | 🟡 | Los reescribiste, pero hay tests nuevos rotos tras la fusión (B10) |
| Scripts sueltos movidos a `scripts/dev/` | ❌ | Siguen 11 `.php` en la raíz (`audit_db.php`, `check_db.php`, `seed_data.php`…) |
| `server.log` y vistas compiladas fuera de git | 🟡 | Están en `.gitignore:8-9`, pero siguen versionados: `server.log` y **649** archivos en `storage/framework/views/`. El último commit, «home view» (`e6cddd7`), solo toca uno de ellos. |
| `npm audit` y `composer update` | ℹ️ No verificable | Revisión de solo lectura; los *lock* de `main` difieren de los de la rama |

**Veredicto:** has reaplicado casi todas las correcciones de la rama y has cerrado las 5 incidencias 🔴 de la revisión anterior. Pero la fusión del 17-11 introdujo **regresiones nuevas de gravedad similar**: gestión de usuarios rota, alcance perdido y cuentas con contraseña conocida en producción.

---

## 1. Usabilidad

| # | Problema | Dónde | Recomendación |
|---|---|---|---|
| U1 | **El administrador no puede gestionar usuarios.** `/roles` da error 500 (ver B1). Además, ningún menú enlaza a `/roles`, `/importar`, `/indicadores/bajas` ni a los informes: hay que saberse las URL. | `resources/views/partials/academic-nav.blade.php:4-9`; `dashboard.blade.php:29-42` | Arreglar B1 y mostrar en el menú las opciones de cada rol con `@can('manage-users')`, etc. |
| U2 | **El menú y el panel enseñan módulos prohibidos.** «Alumnos ayudantes» sale para todos los roles, pero solo admin y PPA pueden entrar. El resto recibe un 403 sin explicación. | `academic-nav.blade.php:8`; `dashboard.blade.php:38-41`; `routes/web.php:31-32` | Envolver el enlace y la tarjeta en `@can('view-student-helpers')`. |
| U3 | **Los valores de los indicadores se pierden.** En `/indicadores` el usuario añade filas y escribe valores, pero solo puede «Exportar CSV» desde el navegador. Nada se envía a `/valores`. Al recargar la página, todo desaparece. | `resources/views/indicadores.blade.php:192,744-776` (no hay ninguna llamada a `valores.store`) | Botón «Guardar valores» que envíe a un endpoint por indicador, entidad y curso (ver B5 y F1), con aviso de cambios sin guardar. |
| U4 | **El plan de estudio engaña al usuario.** Añadir o quitar disciplinas y asignaturas solo cambia la pantalla («no guardada en BD»), y «Enviar borrador» no está implementado. También hay HTML roto: `<>` y `</buon>`. | `resources/views/tables/planestudio.blade.php:1315,1395,1447,1455,1484`; `:1061,1082` | Guardar contra la API, que ya tiene `POST /api/disciplinas` y `/api/asignaturas`, o desactivar los botones con un «Próximamente». Corregir el HTML. |
| U5 | **El panel de inicio no muestra datos.** `DashboardController` calcula totales, promedio, bajas y una serie para un gráfico, y la vista no usa nada de eso: es una portada estática. El nombre «Agenda Semestral» tampoco coincide con `APP_NAME` («Sistema de Indicadores Académicos»). | `app/Http/Controllers/DashboardController.php:17-30`; `resources/views/dashboard.blade.php:5,11-44`; `.env.example:1` | Mostrar tarjetas con cifras y un gráfico (Chart.js ya está en `package.json`), filtrados por el alcance del usuario, o quitar las consultas. Unificar el nombre. |
| U6 | **El selector de estudiante carga todos los alumnos en el HTML.** Es un `<datalist>` con cada estudiante (unos 3.200 con los datos de ejemplo), sin filtrar por grupo. Dos alumnos con el mismo nombre y sin correo no se distinguen. | `AlumnoAyudanteController.php:24-27`; `tables/alumnoayudante.blade.php:57-61` | Autocompletar contra `/api/alumnos?search=`, que ya está paginado (`DataController.php:309-326`), y mostrar el identificador institucional y el grupo. |
| U7 | **Los alcances se asignan escribiendo un ID numérico** («ID de la entidad»), sin selector. | `resources/views/roles.blade.php:62-63` | Selectores dependientes (tipo → entidad) que se carguen desde la API. |
| U8 | **Los mensajes de validación salen en inglés.** `APP_LOCALE=es`, pero no existe la carpeta `lang/`. Solo se traducen los mensajes que defines a mano en cada Form Request. | `.env.example:8`; no hay ningún archivo en `lang/` | `php artisan lang:publish` y el paquete `laravel-lang/lang`, o un `lang/es/validation.php`. |
| U9 | **La exportación «Excel» es un `.xml`.** Excel muestra un aviso al abrirlo. Además, no se puede filtrar por curso ni por entidad: exporta todos los indicadores. | `ReportExportController.php:61-63,81-90` | Generar `.xlsx` (por ejemplo con `openspout/openspout`) y pedir el curso y la entidad en un formulario. |
| U10 | **Tres estilos visuales distintos.** `academic.css` con el menú propio, Tailwind con CSS en línea en `planestudio` y el layout Flux del starter kit en `/importar`. Cada pantalla parece de una aplicación diferente. | `resources/views/imports/index.blade.php:1` (`<x-layouts.app>`); `planestudio.blade.php`; `partials/academic-nav.blade.php` | Un layout Blade común (`components/layouts/academic.blade.php`) con el menú, y eliminar el layout Flux si no se usa. |
| U11 | **Eliminar un indicador no avisa de lo que se pierde.** El `confirm()` no dice que se borran en cascada todos sus valores. La lista se maneja con `onclick` sobre `<div>`, así que no se puede usar con el teclado. | `indicadores.blade.php:394,797`; `2026_01_15_create_indicador_valores_table.php:16` (`cascadeOnDelete`) | Modal de confirmación con el número de valores afectados, *soft delete*, y usar `<button>` en vez de `div` con `onclick`. |
| U12 | **Ruido en la consola del navegador:** 14 `console.log` en `indicadores.blade.php` y 3 en `planestudio`. Además, hay `alert()` con emojis para dar feedback. | `indicadores.blade.php:413-437,678-714` | Quitar los `console.log` y usar el `mostrarToast` que ya tienes (`indicadores.blade.php:250`). |

---

## 2. Expansión de features

Las ideas están ordenadas por valor para un sistema de indicadores académicos. Varias ya tienen la base hecha.

| # | Prioridad | Feature | Por qué / cómo |
|---|---|---|---|
| F1 | **P1** | **Valores por indicador, entidad y curso** | Es el núcleo del sistema y hoy no existe (B5). Una tabla `valor_indicador(indicador_id, especificacion, entidad_id, curso_academico_id, valor, registrado_por, estado, timestamps)` con un `unique(indicador_id, especificacion, entidad_id, curso_academico_id)`. Autorizar cada escritura con el alcance del usuario sobre la entidad: `AcademicScopeService::canAccess` ya lo hace (`app/Services/AcademicScopeService.php:61-99`), en cuanto vuelva `User::hasRole`. |
| F2 | **P1** | **Recuperar asignaciones PPA, alcances y auditoría** | El código está escrito (`PpaController`, `AuditController`, `UserController::storeScope`, `AlumnoAyudanteService`, vistas `ppa/` y `audit/`), pero se quedó sin rutas ni métodos en `User` (B3). Recuperarlo desde `f2fd4d6` (rama `issues-todo`) es lo que más valor da con menos trabajo. |
| F3 | **P1** | **Flujo de validación de valores** | Estados `borrador → enviado → validado` (por el jefe o el decano) → `cerrado`. Bloquear la edición de cursos cerrados (ya estaba en `TODO.md` §5). |
| F4 | **P2** | **Indicadores calculados** | La tasa de bajas (`BajasController.php`) y el porcentaje de alumnos ayudantes (`AlumnoAyudanteController.php:107-157`) ya se calculan. Guárdalos como indicadores derivados por curso y entidad, en lugar de dejarlos solo en pantalla. |
| F5 | **P2** | **Metas y semáforo** | Una meta y unos umbrales por indicador (`valor_meta`, `umbral_alerta`), con color en la tabla y en el panel. |
| F6 | **P2** | **Panel comparativo entre cursos** | Series por indicador y entidad a lo largo de los cursos académicos, con Chart.js (ya en `package.json`), filtrado por el alcance del rol. Sustituye al panel estático actual (U5). |
| F7 | **P2** | **Informes oficiales por entidad y curso** | Excel/PDF filtrado por curso y entidad, con cabecera institucional y **auditado**. La auditoría ya existía en `ReportExportController::csv` (`:35-40`), que ahora no tiene ruta. |
| F8 | **P2** | **Plan de estudio persistente y solicitudes de modificación** | Guardar disciplinas y asignaturas (U4) y usar las tablas `version`, `modificacion` y `version_modificacion` para un flujo borrador → revisión → aprobación. |
| F9 | **P3** | **Importar valores de indicadores desde Excel** | Reutilizar `SpreadsheetReader` y la vista previa de `ImportController` para cargar valores por lotes. |
| F10 | **P3** | **Avisos de plazos de captura** | Un comando programado que avise por correo a cada responsable de los indicadores pendientes del curso. |
| F11 | **P3** | **Historial por indicador** | Mostrar quién cambió cada valor y cuándo, a partir de la tabla `auditoria`. |

---

## 3. Errores y mejoras de arquitectura

### 3.1 Bugs concretos

| # | Archivo:línea | Problema | Efecto | Recomendación |
|---|---|---|---|---|
| B1 | `app/Http/Controllers/UserController.php:20`; `resources/views/roles.blade.php:60,67` | **`/roles` da error 500.** Se precarga la relación `alcancesAcademicos`, que no existe en `User`. Además, la vista usa `route('roles.scopes.store')` y `route('roles.scopes.destroy')`, que no están definidas en `web.php`. | El administrador no puede ver, crear ni editar usuarios. | Restaurar las relaciones en `User` (B3) y las rutas `roles.scopes.*`, o quitar ese bloque de la vista. |
| B2 | `UserController.php:51-53,68-70` | **`$user->roles()` no existe.** El usuario se crea o se actualiza y **después** falla con un 500. No hay transacción. | Datos a medias: el usuario queda guardado aunque la pantalla muestre un error. | Recuperar `roles()` o quitar la tabla pivote y quedarse solo con la columna `role`. Envolverlo en `DB::transaction`. |
| B3 | `app/Models/User.php`; `routes/web.php`; `app/Http/Controllers/Api/DataController.php` | **Las fusiones de `indicadores` y `alumno-ayudante` borraron código de `issues-todo`.** La de `indicadores` (`9d35f4d`) quitó las rutas de PPA, alcances, auditoría, CSV e `indicadores.apply` (`git diff e2ea997 9d35f4d -- routes/web.php`) y la validación de `storeAnioAcademico`. La de `alumno-ayudante` (`f33a8ef`) sustituyó `User.php`, y desaparecieron `roles()`, `hasRole()`, `asignacionesPpa()` y `alcancesAcademicos()` (`git diff 9d35f4d f33a8ef -- app/Models/User.php`). | Queda código sin ruta y, en parte, roto. Fallarían `AcademicScopeService.php:46-114`, `AlumnoAyudanteService.php:22,55`, `PpaController.php:24,44`, `StoreIndicatorDefinitionRequest.php:18`, `UserController::storeScope` y `RoleSeeder`. Están sin uso `AuditController`, `ExportIndicatorsRequest`, `StoreIndicatorApplicationRequest`, `StorePpaAssignmentRequest` y las vistas `ppa/*` y `audit/*` (los gates `view-audit` y `create-indicators` ya no existen). | Decidir el modelo de roles (columna `role` o tabla `role_user`) y, o reintegrar ese código, o borrarlo. Lo que no se borre debe tener test. |
| B4 | `app/Http/Controllers/Api/DataController.php:265-275` | **`storeAnioAcademico` contradice el esquema.** Exige un `nombre` único global, cuando la BD ya no lo tiene (`2026_11_16_031024…:51`) y usa `unique(carrera_id, nivel)` (`…:73`). No valida que el nivel sea único por carrera ni que no supere `duracion_anios`. Esa validación existía en `issues-todo` (`git show f2fd4d6:…DataController.php:259-276`) y se perdió en la fusión de `indicadores` (`9d35f4d`). | Un nivel repetido llega a la BD y da 500 en vez de 422. «Primer año» no puede existir en dos carreras. `AcademicHierarchyTest.php:28-41` falla **(inferido)**. | Recuperar la versión de `f2fd4d6` (`Rule::unique('anio_academico','nivel')->where('carrera_id', …)` y la comprobación de duración). |
| B5 | `app/Http/Controllers/IndicadorController.php:46-50`; `2026_01_15_create_indicador_valores_table.php:14-19` | **Un solo valor global por indicador.** `updateOrCreate(['indicador_id' => …])`, y `indicador_valor` no tiene entidad ni curso. Las tablas `indicador_carrera`, `indicador_grupo`… ya no las escribe ningún código. Además, `/valores` exige que `valores.*` sea numérico (`IndicadorController.php:35`) y `indicador_valor.valor` es `decimal(10,2)`, así que los indicadores «Cualitativo» y «Booleano» nunca pueden guardar un valor. | No se puede registrar «tasa de bajas del grupo X en 2026-2027». Los informes y el panel promedian valores sin contexto. | Ver F1. Eliminar o reutilizar las tablas `indicador_*` huérfanas. |
| B6 | `app/Models/AlumnoAyudante.php:13`; `2026_11_16_031024…:103-108`; `AlumnoAyudanteController.php:62-66` | **Columnas nuevas que nunca se rellenan.** La migración añade `grupo_id`, `asignacion_ppa_id`, `fecha_designacion` y `estado`, pero el modelo no las tiene en `$fillable` y `store()` no las envía. | Las columnas siempre valen `null` o el valor por defecto, y el índice `(alumno_id, curso_academico_id, estado)` no sirve para nada. | Rellenarlas (el grupo se obtiene del alumno) o quitarlas. |
| B7 | `app/Http/Controllers/ReportExportController.php:81-90,13-46` | **`rows()` usa `valores->last()` sin ordenar**, así que el «último valor» depende del orden de la BD. `csv()` no tiene ruta. | Informes no deterministas y código muerto. | Ordenar por `updated_at` o, mejor, filtrar por curso (F1/F7). Borrar `csv()` o volver a enrutarlo. |
| B8 | `app/Console/Commands/AuditDatabase.php:21-92`; `SeedMissingData.php:20-107` | **Los comandos `db:audit` y `db:seed-missing` usan tablas en plural** (`grupos`, `alumnos`, `planes_estudio`…). | Fallan siempre con «relation does not exist». | Corregir los nombres o borrar los comandos, ahora que hay seeders idempotentes. |
| B9 | `app/Http/Controllers/AlumnoAyudanteController.php:57-69`; `routes/api.php:39` | **El endpoint de la API responde con una redirección** y la vista lo usa como un formulario HTML clásico. Un cliente JSON recibe un 302. | API incoherente: `PpaAndTeachingAssistantTest.php:37-46` espera un 201 y falla **(inferido)**. | Mover el `POST` a `web.php`, o devolver JSON cuando `wantsJson()`. |
| B10 | `tests/Feature/*` | **Tests que fallan en `main` (inferido; no ejecutados):** `AcademicAuthorizationTest` (`hasRole` no existe); `IndicatorExportAuditTest` (rutas `indicadores.store`, `indicadores.apply`, `indicadores.csv` y `audit.index` sin definir); `PpaAndTeachingAssistantTest` (contrato distinto y ruta `indicadores.apply`); `ApplicationSmokeTest` con `roles`; `SeededPagesRegressionTest` (`/roles`); `SecurityRegressionTest:71-81` (`roles()`); `AcademicHierarchyTest:28-41` (B4). | El CI de `main` está en rojo, o no se ha ejecutado tras la fusión. Los tests describían bien las reglas, pero el código ya no las cumple. | Ejecutar `php artisan test` **antes** de cada fusión. Arreglar el código, no los tests (sobre todo los de alcance PPA y secretaria). |
| B11 | `database/migrations/2026_11_16_220658_restructure_indicadores_and_academic_context.php:15-16,27` | **Migración destructiva.** Borra **todos** los indicadores y categorías (y en cascada sus valores) para poder añadir `especificacion` NOT NULL sin valor por defecto. | En una BD con datos reales, `migrate` los pierde sin aviso. En SQLite fallaría al añadir la columna **(inferido** de la gramática de SQLite; el proyecto declara PostgreSQL). | Migrar los datos (asignar `especificacion` a partir de las tablas `indicador_*`) en lugar de borrarlos, o al menos abortar si hay filas. |
| B12 | `public/api-test.php:4,13` | **Script roto y público.** Hace `require 'bootstrap/app.php'` relativo a `public/`. | Error fatal con la ruta del servidor si `display_errors` está activo. | Borrarlo, junto con `public/*-carreras.html` y `public/test-api.html`. |
| B13 | `app/Http/Controllers/TablesController.php`; `resources/views/indicadores/create.blade.php`, `show.blade.php`; `resources/views/livewire/auth/register.blade.php`, `livewire/settings/*`; `routes/web.php:48-49` | **Código muerto heredado** del starter kit y de versiones anteriores, sin ruta. Las rutas `categorias.*` siguen sin usarse en ninguna vista (ya era la #16 de la revisión anterior). | Confunde y amplía lo que hay que mantener. | Borrarlo. |
| B14 | `.gitignore:8-9`; `git ls-files storage/framework/views` (649 archivos); `server.log` | **Archivos generados en git.** Cada visita en local cambia vistas compiladas, y el commit `e6cddd7` solo contiene una de ellas. | Commits sin contenido real y conflictos constantes al fusionar. | `git rm -r --cached storage/framework/views/*.php server.log`. |

### 3.2 Arquitectura

**Controladores y servicios**

- `Api/DataController` ha bajado de unas 600 a 396 líneas y ya no tiene `try/catch` que filtraran mensajes. Pero sigue concentrando 29 acciones de 12 recursos distintos. Divídelo en `FacultadController`, `CarreraController`, `IndicadorController`, etc., y regístralos con `Route::apiResource`.
- Tienes una capa de servicios (`AcademicScopeService`, `AuditService`, `AlumnoAyudanteService`), pero **solo `AuditService` se usa**, y únicamente desde código roto (`UserController`). La lógica viva sigue en los controladores: `AlumnoAyudanteController::store` y `IndicadorController::storeValores`. Elige un camino: o los controladores llaman a los servicios, o borra los servicios.
- `ReportExportController` genera un PDF a mano (`:92-129`). Funciona, pero es frágil (sin acentos fuera de Windows-1252, sin tablas). `barryvdh/laravel-dompdf` con una vista Blade es más fácil de mantener.

**Form Requests**

- Es un buen avance: hay 11, con mensajes en español. Pero 5 no se usan (`ExportIndicatorsRequest`, `StoreIndicatorApplicationRequest`, `StoreIndicatorDefinitionRequest`, `StorePpaAssignmentRequest`, `StoreUserScopeRequest`), y casi todos los `store*` de `DataController` siguen validando en línea (`:36,50-53,76-79,119-125…`).
- `StoreAlumnoAyudanteRequest::authorize()` (`:11-14`) solo comprueba el rol, no el alcance (S3).

**Alcance por departamento**

- **Escritura:** `EnsureDepartmentScope` está bien hecho y se aplica a todos los `POST` de datos académicos (`api.php:9-32`). Pero sus ramas para `api/alumnoayudante` (`EnsureDepartmentScope.php:78-101`) son código muerto, porque esas rutas no llevan el middleware (`api.php:39-40`).
- **Lectura:** **no hay ningún filtro.** Todos los `GET` de la API, `/indicadores/bajas` y `/alumnoayudante` devuelven datos de toda la universidad a cualquier usuario autenticado (S2).
- **Indicadores:** la autorización es solo por **tipo** (`User::canManageIndicatorSpecification`, `User.php:91-103`), no por entidad. Encaja con el nuevo modelo de «indicador como definición global», pero entonces cualquier PPA puede renombrar o borrar un indicador de grupo que usan todos los grupos (S4).

**Migraciones: ¿funciona `migrate:fresh` en una BD nueva?**

- **En PostgreSQL, sí (inferido).** He recorrido las 39 migraciones en orden. Las 9 correcciones de la rama del profesor están (0.1). Las nuevas (`2026_11_14_*`, `2026_11_16_*`, `2026_11_17_*`) solo hacen `ALTER` sobre tablas que ya existen en ese punto, o rellenan datos en tablas vacías. Además, el CI ejecuta todas las migraciones contra PostgreSQL 17 con `RefreshDatabase` (`tests/Pest.php:14-16`).
- **En SQLite, no (inferido):** `2026_11_16_220658:27` añade una columna NOT NULL sin valor por defecto. Como el README exige PostgreSQL (`README.md:7-8`), es aceptable, pero dilo explícitamente.
- **Sobre una BD con datos:** `migrate` borra indicadores (B11), y `2026_11_16_031024:55-70` y `2026_11_16_220658:36-51` deducen `nivel` con una expresión regular sobre el nombre. Si un nombre no tiene dígito (o el nivel se repite en la carrera), `031024` lo deja en `null` y `220658:49-50` lanza una `RuntimeException`.
- **Pendiente:** `env('APP_ADMIN_EMAIL')` dentro de una migración (`2026_11_14_000001:17`). Las migraciones no deberían depender del `.env`, y además el seeder no usa esa variable (S1).

**Seeders**

- `AcademicCatalogSeeder` y `DemoAcademicDataSeeder` son idempotentes y separan bien los datos de referencia de los de demostración (`DatabaseSeeder.php:15-21`).
- Pero `RoleUserSeeder` se ejecuta **siempre**, también en producción (`DatabaseSeeder.php:14`), y un test lo exige (`ProductionSeedingRegressionTest.php:13-16`). Ver S1.
- `RoleSeeder` no se llama desde ningún sitio y usa `roles()` (`RoleSeeder.php:22`).

**Tests**

- Hay 80 casos de test, muchos de ellos buenos tests de regresión de seguridad (`SecurityRegressionTest`, `RoleAuthorizationTest`, `DepartmentScopeTest`). Es un salto enorme respecto a la primera versión.
- Pero al menos 7 archivos fallan en `main` **(inferido, B10)**. Añade una regla de equipo: **no se fusiona a `main` si `php artisan test` no pasa**. El workflow ya se lanza en cada `push` a `main` (`tests.yml:3-6`): revisa la pestaña *Actions* de GitHub.

**Documentación frente a realidad**

- `README.md:38-43` dice que las credenciales se cambian con `APP_ADMIN_EMAIL` y `APP_ADMIN_PASSWORD`, y que el usuario automático «solo se crea en `local` y `testing`». Las dos cosas son falsas: `LocalAdminSeeder.php:13-16` tiene los valores escritos en el código, y `RoleUserSeeder` crea 7 cuentas en cualquier entorno.
- `ESTRUCTURA_BD.md` sigue describiendo el esquema antiguo, con tablas en plural (#13).
- `AGENTS.md` (546 líneas) y `.cursor/rules` son guías genéricas de Laravel Boost, no del proyecto. Si las dejas, que no sustituyan a una sección real de arquitectura en el README.

**Proceso**

- Has mejorado: ahora trabajas con ramas. Pero los commits siguen llamándose «init» (`c7529cb`, `48f29d2`, `3461789`), y las tres fusiones se hicieron en 10 minutos sin revisión ni CI verde. Las ramas `indicadores` y `alumno-ayudante` partían de `3461789`, anterior a `issues-todo` (`alumno-ayudante` se creó encima de `indicadores`). Al fusionarlas, los conflictos se resolvieron a favor de su versión, y se perdieron rutas de `web.php`, la validación de `DataController` y los métodos de `User.php`. Para la próxima: rebase sobre `main`, PR, CI en verde y luego fusionar.

---

## 4. Seguridad

| Sev. | # | Hallazgo | Dónde | Recomendación |
|---|---|---|---|---|
| 🔴 | S1 | **Cuentas con contraseña conocida en producción.** `DatabaseSeeder` llama siempre a `RoleUserSeeder`, que crea 7 cuentas, entre ellas `administrador@sistema.test` / `Administrador123!`. Las contraseñas están en el código y en los tests, y hay un test que **exige** que existan en producción. Además, `.env.example` trae `APP_ENV=local`, y `composer run setup` ejecuta `migrate --seed --force`. Si alguien despliega copiando el ejemplo, también se crea `admin@example.com` / `password` y se activa `/tablas`. | `database/seeders/RoleUserSeeder.php:16-22`; `DatabaseSeeder.php:14,18-21`; `tests/Feature/ProductionSeedingRegressionTest.php:13-16`; `LocalAdminSeeder.php:13-16`; `.env.example:2,71-72`; `composer.json:47` | Mover `RoleUserSeeder` dentro del bloque `local`/`testing`. En producción, crear el primer administrador con un comando `php artisan app:create-admin` que pida la contraseña. `APP_ENV=production` en `.env.example`, o documentarlo en grande. |
| 🟠 | S2 | **Lecturas sin alcance (exposición de datos personales).** Cualquier usuario autenticado, incluido el rol por defecto `consulta` (`2026_11_14_000001:13`), puede listar todos los estudiantes con correo e identificador (`/api/alumnos`; el modelo `Alumno` no oculta `email`). También puede ver todas las bajas con nombre, grupo y causa (`/indicadores/bajas`); el controlador calcula además `alumno_email`, aunque la vista no lo muestre. Para el PPA, que sí puede entrar en `/alumnoayudante` (`can:view-student-helpers`), la página carga todos los estudiantes, no solo los de sus grupos. | `DataController.php:309-326`; `BajasController.php:119-129`; `routes/api.php:29`; `routes/web.php:36`; `AlumnoAyudanteController.php:24-27` | Aplicar el alcance también en lectura: un *scope* `visibleFor(User)` en `Alumno`, `Grupo` y `BajaEstudiante`. Y que `consulta` no vea datos personales. |
| 🟠 | S3 | **Un PPA puede designar alumnos ayudantes en cualquier grupo.** `StoreAlumnoAyudanteRequest` solo comprueba el rol. La regla de «solo en los grupos asignados» existía (`AlumnoAyudanteService.php:26-28,55-63`) y tiene test (`PpaAndTeachingAssistantTest.php:48-54`), pero ya no se usa. | `app/Http/Requests/StoreAlumnoAyudanteRequest.php:11-14`; `AlumnoAyudanteController.php:57-69`; `routes/api.php:39-40` | Recuperar `asignacion_ppa` y comprobar en `authorize()` que el grupo del alumno está asignado al PPA en ese curso. |
| 🟠 | S4 | **Autorización de indicadores solo por tipo.** Cualquier PPA puede renombrar o borrar cualquier indicador de tipo `grupo`, y fijar su único valor global. El jefe puede hacer lo mismo con todos los de `departamento`. Al borrar, se pierden los valores en cascada. No queda rastro (S6). | `DataController.php:376-395`; `UpdateIndicadorRequest.php:17-24`; `IndicadorController.php:27-51`; `2026_01_15_create_indicador_valores_table.php:16` | Que solo el administrador (o quien lo creó, con un `creado_por`) pueda editar y borrar definiciones. Los demás roles, solo escribir valores de **sus** entidades (F1). *Soft delete*. |
| 🟠 | S5 | **XSS almacenado en el DOM.** Los nombres de indicador, plan y asignatura se insertan con `innerHTML` sin escapar. Un PPA, un jefe, un decano o una importación pueden crear un nombre como `<img src=x onerror=…>` que se ejecuta en la sesión del administrador. Es incoherente: el mismo archivo usa `escaparHtml` en `:546`. En `planestudio` el nombre va además dentro de `onclick='…'`, escapando solo la comilla simple. | `resources/views/indicadores.blade.php:393-397`; `resources/views/tables/planestudio.blade.php:1071-1072,1080,1149`. En `:1306-1310` solo es *self-XSS*, porque el nombre del modal no se guarda (`:1315`). | Usar siempre `escaparHtml` o `textContent`, y `addEventListener` con `data-id` en vez de `onclick` con texto interpolado. Una CSP sin `unsafe-inline` lo cortaría de raíz. |
| 🟠 | S6 | **La auditoría no funciona.** `AuditService` solo se llama desde `UserController` (roto, B1/B2) y desde controladores sin ruta. No se registran el alta, edición o borrado de indicadores y valores, las designaciones, las importaciones, los borrados de categorías ni las exportaciones. La pantalla `/auditoria` no tiene ruta. La tabla solo es inmutable a nivel de modelo: un `DB::table('auditoria')->update()` se salta la protección. | `app/Services/AuditService.php`; `UserController.php:32,42,54,71`; `routes/web.php` (sin `/auditoria`); `app/Models/Auditoria.php:24-28` | Llamar a `AuditService` desde cada escritura, o con *observers* de modelo, y volver a enrutar `AuditController` con `can:manage-users`. En PostgreSQL, quitar `UPDATE`/`DELETE` sobre `auditoria` al usuario de la aplicación. |
| 🟡 | S7 | **Las exportaciones Excel/PDF ignoran el rol.** `rows()` devuelve todos los indicadores. Un PPA, que en el listado solo ve los de tipo `grupo` (`DataController.php:356-366`), exporta también los de facultad. Además, no se audita. | `ReportExportController.php:48-90`; `AppServiceProvider.php:41-43` | Filtrar con `whereIn('especificacion', $user->allowedIndicatorSpecifications())` y auditar. |
| 🟡 | S8 | **Archivos de diagnóstico públicos.** Se sirven sin autenticación `api-test.php`, `test-api.html`, `debug-carreras.html`, `diagnostic-carreras.html`, `test-carreras.html` y `verify-carreras.html`. Además hay 11 scripts PHP sueltos en la raíz y `server.log` versionado. | `public/`; raíz del repo | Borrarlos. Si algún script sirve, convertirlo en un comando Artisan. |
| 🟡 | S9 | **Importación:** no se audita. Guarda todas las filas en la sesión (tabla `sessions`, hasta 10 MB por petición). Un `updateOrCreate` por `cedula` o `codigo` sobrescribe en silencio datos existentes. El XLSX se descomprime entero en memoria. | `ImportController.php:28,45-56`; `app/Services/SpreadsheetReader.php:43-54` | Guardar el archivo en `storage/app/private` y en sesión solo su ruta. Mostrar en la vista previa qué filas **actualizan** registros existentes. Auditar el resultado. |
| 🟡 | S10 | **Migración que borra datos en producción** (B11). | `2026_11_16_220658…:15-16` | Ver B11. |
| 🟡 | S11 | **Gestión de cuentas mínima.** No se puede desactivar una cuenta ni forzar un cambio de contraseña. La política de contraseñas es `min:8` (no usa `Password::defaults()`). La exportación CSV del navegador no neutraliza fórmulas (`=`, `+`, `@`). | `SaveUserRequest.php:46`; `indicadores.blade.php:762` | Campo `activo` y comprobarlo en el login (`Fortify::authenticateUsing`). `Password::min(10)->mixedCase()->numbers()`. Anteponer `'` a las celdas que empiecen por `=+-@`. |
| ℹ️ | S12 | **Dependencias:** no he ejecutado `composer audit` ni `npm audit` (revisión de solo lectura). La rama del profesor actualizaba los *lock* para cerrar 58 avisos de Composer y 14 de npm, y los *lock* de `main` son distintos. No sé si esos avisos siguen abiertos. | `composer.lock`; `package-lock.json` | Ejecutar `composer audit` y `npm audit` y añadirlos al CI. |
| ✅ | — | **Verificado y correcto:** <br>• **Autenticación:** todas las rutas de negocio llevan `auth` (`web.php:20`; `api.php:7`). Registro desactivado (`fortify.php:146-148`). Login limitado a 5 intentos por minuto y por correo e IP (`FortifyServiceProvider.php:66-70`). <br>• **CSRF** en la API (grupo `web`, `api.php:7`). Las vistas envían `X-CSRF-TOKEN` (`indicadores.blade.php:225-230`). <br>• **CORS** restringido a `FRONTEND_URL` (`config/cors.php:6`). <br>• **Escritura de datos académicos** con alcance por departamento (`EnsureDepartmentScope`). <br>• **Errores:** las `QueryException` no filtran detalles (`bootstrap/app.php:27-35`) y `APP_DEBUG=false` por defecto. <br>• **Blade:** no hay `{!! !!}` con datos de usuario (solo el QR de Fortify en una vista muerta). El XML de Excel escapa (`ReportExportController.php:51`). <br>• **Mass assignment:** todos los modelos usan `$fillable` explícito y ninguno `$guarded = []`. `role` solo entra por `SaveUserRequest`. <br>• **Secretos:** `.env` no está versionado. Las credenciales de Flux en CI van por *secrets*. <br>• **`/tablas`** solo en `local` y sin `users` ni `sessions` (`web.php:51-57`). | | |

---

## 5. Conclusión y próximos pasos

Esta segunda versión demuestra que **sabes cerrar los problemas que se te señalan**. Las cinco incidencias 🔴 de la primera revisión están resueltas, las migraciones funcionan en una BD nueva y has construido una base seria de autorización, Form Requests, tests y CI. El problema ahora no es de conocimiento, sino de **integración**. Tres ramas desarrolladas en paralelo se fusionaron sin ejecutar los tests. El resultado es una gestión de usuarios rota, reglas de alcance perdidas y código huérfano. Además, la funcionalidad que da sentido al sistema, **guardar valores de indicadores por entidad y curso**, todavía no existe de extremo a extremo.

**Prioridad 1 (antes de la próxima entrega)**

1. **S1:** que `RoleUserSeeder` no corra en producción, un comando para crear el primer administrador y `APP_ENV=production` en el ejemplo. Corregir el README.
2. **B1-B3:** decidir el modelo de roles, restaurar en `User` lo que se perdió en la fusión (o borrar el código que lo usa) y dejar `/roles` funcionando.
3. **B10:** dejar `php artisan test` en verde en `main`, arreglando el código y no los tests (sobre todo `PpaAndTeachingAssistantTest`, `AcademicAuthorizationTest` y `AcademicHierarchyTest`).
4. **S3 / S2:** volver a aplicar el alcance del PPA en las designaciones y filtrar por alcance las lecturas de estudiantes y bajas.
5. **S5:** escapar todos los `innerHTML` de `indicadores.blade.php` y `planestudio.blade.php`.

**Prioridad 2**

6. **B5 / F1 / U3:** tabla de valores por indicador, entidad y curso, y botón «Guardar» en `/indicadores`.
7. **S6 / F2:** auditoría en todas las escrituras y pantalla `/auditoria` enrutada.
8. **S4 / S7:** permisos de edición y borrado de definiciones, *soft delete* y exportaciones filtradas por rol.
9. **B4, B6, B9, B11:** validación de `anio_academico`, columnas sin rellenar en `estudiante_ayudante`, contrato de la API de alumno ayudante y migración destructiva.
10. **U1, U2, U5:** menú según el rol y un panel con datos reales.
11. **Proceso:** PR con CI en verde antes de fusionar, rebase sobre `main` y commits descriptivos.

**Prioridad 3**

12. **Limpieza:** B8, B12-B14, S8 (scripts, archivos públicos de depuración, vistas compiladas, `server.log`, código muerto del starter kit) y actualizar `ESTRUCTURA_BD.md`.
13. **U4, U6-U12:** plan de estudio persistente, selector de alumnos con búsqueda, traducciones, `.xlsx` real, layout único y accesibilidad.
14. **F3-F11:** flujo de validación, indicadores calculados, metas, panel comparativo e informes oficiales.
15. **S9, S11, S12:** endurecer la importación y la gestión de cuentas, y añadir `composer audit` y `npm audit` al CI.
