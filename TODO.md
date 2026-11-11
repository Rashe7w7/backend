# TODO: mejoras propuestas

Hoja de ruta de mejoras para el Sistema de Indicadores Académicos. Los **bugs y riesgos concretos** ya detectados están en [`ISSUES.md`](ISSUES.md) y se citan aquí por su número (`#n`). Este documento recoge **mejoras de diseño, calidad y funcionalidad**.

Prioridad: **P1** = antes de usar con datos reales · **P2** = siguiente iteración · **P3** = cuando haya tiempo.

---

## 1. Seguridad y acceso

- [ ] **P1** Autenticación real con Fortify, que ya está instalado: login, logout y recuperación de contraseña. Hay que sustituir el `POST /login` ficticio (ISSUES #1, #5).
- [ ] **P1** Proteger `routes/web.php` con el middleware `auth` y la API con `auth:sanctum`, o con sesión si solo la usan las vistas propias.
- [ ] **P1** Roles y permisos. Ya existe una página `/roles` sin lógica detrás. Propuesta: tabla `role` y `user_role`, o el paquete `spatie/laravel-permission`, con perfiles como *administrador*, *jefe de departamento*, *profesor* y *consulta*. Usar Policies/Gates para limitar quién crea o borra indicadores, categorías y alumnos ayudantes.
- [ ] **P1** Restringir CORS a los orígenes reales y añadir `throttle:api` (ISSUES #2).
- [ ] **P1** Quitar o limitar a `local` las rutas `/tablas`, `/api/debug/*` y `/api/indicadores-debug` (ISSUES #3, #6).
- [ ] **P2** No devolver `$e->getMessage()` al cliente: usar un manejador de excepciones centralizado en `bootstrap/app.php` (ISSUES #7).
- [ ] **P2** Pedir confirmación, o usar *soft delete*, en los borrados en cascada: borrar una categoría elimina todos sus indicadores.
- [ ] **P3** Auditoría: registrar quién creó, modificó o borró cada indicador o valor, por ejemplo con `owen-it/laravel-auditing` o una tabla propia `actividad`.

## 2. Arquitectura del backend

- [ ] **P2** **Dividir `Api/DataController`** (unas 600 líneas, más de 30 acciones) en controladores por recurso: `FacultadController`, `CarreraController`, `IndicadorController`… Registrarlos con `Route::apiResource()`.
- [ ] **P2** **Form Requests** (`app/Http/Requests`, que todavía no existe): sacar la validación de los controladores para reutilizarla y probarla mejor.
- [ ] **P2** **API Resources** (`JsonResource`) para controlar los campos que se exponen, en vez de devolver modelos completos.
- [ ] **P2** **Capa de servicio** para la lógica de indicadores: crear un indicador y asignarlo a facultad, departamento, carrera, año o grupo se repite en `IndicadorController::store` y en `DataController::createAndAssignIndicador`. Hacerlo en un único `IndicadorService` dentro de `DB::transaction` (ISSUES #9).
- [ ] **P2** **Paginación y filtros:** 11 endpoints usan `Modelo::all()` y ninguno pagina. `/api/alumnos` devuelve unos 3200 registros con los datos de ejemplo. Usar `->paginate()` y filtros por query string (`?carrera_id=&grupo_id=`).
- [ ] **P2** Revisar las consultas N+1 (activar `Model::preventLazyLoading()` en local) y añadir `with()` donde haga falta.
- [ ] **P2** Eliminar el código muerto: `TablesController`, el modelo `Plan` duplicado, las vistas sin ruta y las rutas `/alumnoayudante` duplicadas (ISSUES #10, #14-#17).
- [ ] **P3** Unificar los nombres: el código mezcla *alumno/estudiante*, *anio/anno/año* y nombres de tabla en singular con alias en plural. Elegir una convención y documentarla.
- [ ] **P3** Versionar la API (`/api/v1/...`) si va a tener otros clientes, como una app móvil o un frontend separado.
- [ ] **P3** Documentar la API con OpenAPI/Swagger, por ejemplo con `dedoc/scramble`, que la genera desde el código.

## 3. Base de datos

- [ ] **P2** **Consolidar las migraciones:** hay unas 30 migraciones incrementales que renombran y reestructuran tablas varias veces, con un rollback que no funciona y un orden frágil (ISSUES #11, #12). Antes de producción conviene generar un esquema limpio (`php artisan schema:dump --prune`) o reescribir migraciones base equivalentes.
- [ ] **P2** Probar las migraciones en el motor de producción, MySQL o PostgreSQL (ISSUES #22).
- [ ] **P2** Revisar los índices de las columnas por las que se filtra con frecuencia (`curso_academico_id`, `carrera_id`, `grupo_id` en las tablas `indicador_*` y `estudiante`) y las restricciones únicas compuestas, por ejemplo un valor por indicador, entidad y curso.
- [ ] **P2** Crear **factories** para todos los modelos (ahora solo existe `UserFactory`) y reescribir `FakeDataSeeder` (unas 670 líneas de `DB::table`) usándolas. Corregir de paso ISSUES #18.
- [ ] **P2** Separar los **datos de referencia reales** (facultades, carreras, categorías docentes y científicas, grados) de los **datos falsos**: un `ReferenceDataSeeder` para producción y `FakeDataSeeder` solo en local.
- [ ] **P3** Actualizar `ESTRUCTURA_BD.md` con los nombres reales y un diagrama entidad-relación, por ejemplo en Mermaid (ISSUES #13).
- [ ] **P3** Pasar a comandos Artisan los scripts de `scripts/dev/` que sigan siendo útiles, como ya se hizo con `db:audit` y `planes:verify`, y borrar el resto.

## 4. Frontend

- [ ] **P2** **Dejar de usar el CDN de Tailwind** y cargar los assets con `@vite(...)`. El build ya funciona; así se gana rendimiento, funcionamiento sin Internet y un CSS purgado (ISSUES #20).
- [ ] **P2** **Sacar el JavaScript de las vistas Blade:** `indicadores.blade.php` tiene unas 760 líneas de JS en línea y `planestudio.blade.php` unas 530. Moverlo a módulos en `resources/js/` (`indicadores.js`, `api.js`…), que Vite ya compila.
- [ ] **P2** **Un único cliente HTTP:** hay 17 llamadas `fetch()` repartidas por las vistas, cada una con su propio manejo de errores. Usar `axios`, que ya está configurado en `resources/js/app.js`, con el token CSRF y un manejador común de errores 422 y 500.
- [ ] **P2** **Layout común:** cada vista repite `<head>`, el CDN y la cabecera. Crear un `components/layouts/app.blade.php` con navegación entre secciones.
- [ ] **P3** Decidir si se usa **Livewire/Flux**, que ya está instalado y es del starter kit, o se elimina. Livewire encaja bien con formularios dependientes, como carrera → plan → asignatura, y evitaría gran parte del JS manual.
- [ ] **P3** Accesibilidad: etiquetas en los formularios, foco visible, contraste de los naranjas y navegación con teclado en las tablas.
- [ ] **P3** Diseño adaptable a móvil en las tablas grandes (`/indicadores`, `/alumnoayudante`).

## 5. Funcionalidad

- [ ] **P2** **Dashboard con datos reales:** gráficas por indicador, curso y carrera (Chart.js) y comparación entre cursos académicos.
- [ ] **P2** **Vista de bajas:** el controlador ya calcula las estadísticas por causa, año y carrera, pero falta la vista `indicadores/bajas.blade.php` (ISSUES #4).
- [ ] **P2** **Exportar** los informes de indicadores a Excel o PDF (`maatwebsite/excel`, `barryvdh/laravel-dompdf`).
- [ ] **P2** **Importar** estudiantes, profesores y asignaturas desde CSV/Excel para la carga inicial de cada curso.
- [ ] **P3** Fórmulas de indicador: calcular automáticamente los indicadores derivables de los datos (por ejemplo, la tasa de bajas por grupo) en vez de introducirlos a mano.
- [ ] **P3** Flujo de **solicitudes de modificación de plan de estudio**: la vista `/planestudio` ya se titula "Crear Solicitud de Modificación" y existen las tablas `version`, `modificacion` y `version_modificacion`, pero falta el flujo de borrador → revisión → aprobación.
- [ ] **P3** Cierre de curso académico: bloquear la edición de los valores de cursos cerrados.

## 6. Calidad y pruebas

- [ ] **P2** Ampliar los tests más allá de las pruebas de humo actuales (26 tests):
  - [ ] Tests de cada endpoint de escritura, incluidos los de `IndicadorController` y `AlumnoAyudanteController`.
  - [ ] Tests de `BajasController` (estadísticas) cuando exista la vista.
  - [ ] Tests de autorización cuando haya roles.
- [ ] **P2** Ejecutar en CI los tests contra **MySQL**, además de SQLite en memoria (servicio `mysql` en GitHub Actions).
- [ ] **P2** El workflow `lint.yml` ejecuta `pint` sin `--test`, así que nunca falla. Usar `vendor/bin/pint --test` y aplicar Pint una vez a todo el código.
- [ ] **P3** Análisis estático con **Larastan** (PHPStan) a nivel 5 o superior.
- [ ] **P3** Cobertura mínima en CI (`pest --coverage --min=60`); el workflow ya instala xdebug.
- [ ] **P3** Dependabot o Renovate para mantener actualizadas las dependencias de Composer y npm, y `composer audit` / `npm audit` en CI.

## 7. Configuración y despliegue

- [ ] **P2** `.env.example`: `APP_LOCALE=es`, `APP_FAKER_LOCALE=es_ES` y `APP_NAME` con el nombre del sistema. Añadir las traducciones al español de los mensajes de validación (`php artisan lang:publish` y el paquete `laravel-lang/lang`), porque ahora salen en inglés ("The nombre has already been taken").
- [ ] **P2** Documentar la configuración de producción: `APP_ENV=production`, `APP_DEBUG=false`, `php artisan optimize`, colas con supervisor y HTTPS.
- [ ] **P3** **Docker / Laravel Sail** (ya está en `require-dev`): un `compose.yaml` con PHP 8.4 y MySQL para tener el mismo entorno en todos los equipos. Habría evitado el problema de PHP 7.1 de XAMPP.
- [ ] **P3** Quitar de `package.json` las `optionalDependencies` fijadas solo para Linux (ISSUES #21).
- [ ] **P3** Logs: canal `daily` con rotación y quitar los `\Log::info` de depuración de los controladores (por ejemplo `getCarreras` y `storeFromForm`).
- [ ] **P3** Copias de seguridad automáticas de la base de datos (`spatie/laravel-backup`).

## 8. Proceso de equipo

- [ ] **P2** Usar ramas y Pull Requests con revisión antes de fusionar en `main`. El historial actual tiene commits como "prueba8" y "Arreglos" directamente en `main`.
- [ ] **P2** Mensajes de commit descriptivos, por ejemplo con Conventional Commits (`fix:`, `feat:`).
- [ ] **P3** Plantillas de issue y de PR en `.github/` y un `CONTRIBUTING.md` breve.
