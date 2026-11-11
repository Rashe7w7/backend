# Scripts de desarrollo (diagnóstico local)

Esta carpeta contiene scripts auxiliares de diagnóstico y prueba usados durante el desarrollo local.

- No forman parte de la aplicación; no son rutas, controladores ni código de producción.
- Se ejecutan desde la raíz del repositorio, por ejemplo: `php scripts/dev/check_db.php`.
- Algunos asumen que el servidor está corriendo en `127.0.0.1:8000` o que la base de datos local tiene datos de prueba.
- Pueden modificar datos de la base de datos local (por ejemplo, `seed_data.php`): úsalos solo en entornos de desarrollo.
- **Nunca los despliegues a producción.**
