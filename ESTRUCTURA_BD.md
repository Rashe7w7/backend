# Estructura de Base de Datos - Sistema de Indicadores Académicos

## Descripción General

El sistema implementa una estructura jerárquica completa para la gestión académica de una institución educativa, con soporte para múltiples facultades, departamentos, carreras y años académicos. La arquitectura permite evaluar indicadores a diferentes niveles de granularidad.

---

## 1. Estructura Jerárquica Principal

```
Facultad
├── Departamento (1 o más)
│   ├── Carrera (1 o más)
│   │   ├── Plan de Estudio (1 o más)
│   │   │   ├── Disciplina (1 o más)
│   │   │   │   └── Asignatura (1 o más)
│   │   │   │       └── Currículo (1 o más)
│   │   └── Año Académico (1 a 5 años)
│   │       └── Grupo (3 grupos por año)
│   │           └── Alumno (30-40 alumnos por grupo)
│   └── Profesor (1 o más miembros)
└── Indicador Facultad
```

---

## 2. Tablas de Estructura Académica

### `facultades`
- **Rol**: Nivel superior de la jerarquía organizacional
- **Campos**: id, nombre, created_at, updated_at
- **Relaciones**:
  - `hasMany` → `departamentos`
  - `hasMany` → `carreras`
  - `hasMany` → `indicador_facultad`

### `departamentos`
- **Rol**: Unidad de gestión dentro de una facultad
- **Campos**: id, nombre, facultad_id, created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `facultades`
  - `belongsToMany` → `profesores` (miembro_departamento)
  - `hasOne` → `jefe_departamento`
  - `belongsToMany` → `carreras` (carrera_departamento)
  - `hasMany` → `indicador_departamento`

### `carreras`
- **Rol**: Programa académico ofrecido por una facultad
- **Campos**: id, nombre, facultad_id, created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `facultades`
  - `hasMany` → `anios_academicos`
  - `hasMany` → `planes_estudio`
  - `belongsToMany` → `departamentos` (carrera_departamento)
  - `belongsToMany` → `planes_estudio` (carrera_plan)
  - `hasMany` → `indicador_carrera`

### `anios_academicos`
- **Rol**: Año específico dentro de una carrera (1º, 2º, 3º, 4º, 5º año)
- **Campos**: id, nombre, carrera_id, created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `carreras`
  - `hasMany` → `grupos`
  - `hasManyThrough` → `alumnos`
  - `hasManyThrough` → `bajas_estudiantes`
  - `hasMany` → `indicador_anno`

### `grupos`
- **Rol**: Agrupación de estudiantes dentro de un año académico
- **Campos**: id, nombre, anio_academico_id, created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `anios_academicos`
  - `hasMany` → `alumnos`
  - `hasMany` → `indicador_grupo`

### `planes_estudio`
- **Rol**: Conjunto de disciplinas y asignaturas de una carrera
- **Campos**: id, nombre, carrera_id, fecha_inicio, fecha_fin, vigente, created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `carreras`
  - `hasMany` → `disciplinas`
  - `belongsToMany` → `carreras` (carrera_plan)

### `disciplinas`
- **Rol**: Área temática que agrupa asignaturas relacionadas
- **Campos**: id, nombre, plan_estudio_id, created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `planes_estudio`
  - `hasMany` → `asignaturas`

### `asignaturas`
- **Rol**: Materia individual impartida en un programa académico
- **Campos**: id, nombre, disciplina_id, created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `disciplinas`
  - `hasMany` → `curriculos`
  - `hasMany` → `alumno_ayudante`

### `curriculos`
- **Rol**: Contenido específico de una asignatura (permite evolución en el tiempo)
- **Campos**: id, nombre, asignatura_id, created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `asignaturas`

---

## 3. Estructura de Estudiantes

### `alumnos`
- **Rol**: Registro de estudiantes matriculados
- **Campos**: id, nombre, email, grupo_id, created_at, updated_at, deleted_at (soft delete)
- **Relaciones**:
  - `belongsTo` → `grupos`
  - `hasMany` → `bajas_estudiantes` (máximo 1 baja por alumno)
  - `hasMany` → `alumno_ayudante`
  - `hasMany` → `evaluaciones_integrales`

### `bajas_estudiantes`
- **Rol**: Registro de deserciones y abandonos estudiantiles
- **Campos**: id, alumno_id, causa, observaciones, created_at, updated_at
- **Causas permitidas**: insuficiencia_docente, sancion_disciplinaria, voluntaria, inasistencia, desercion, definitiva, otras_causas
- **Restricción**: Un alumno máximo una baja
- **Relaciones**:
  - `belongsTo` → `alumnos`

### `evaluaciones_integrales`
- **Rol**: Evaluación integral del desempeño estudiantil
- **Campos**: id, alumno_id, anio_academico_id, evaluacion_docencia, investigacion, comportamiento_social, actividades_extension, formacion_politica, tareas_impacto, created_at, updated_at
- **Calificaciones**: E (Excelente), B (Bien), MB (Muy Bien)
- **Relaciones**:
  - `belongsTo` → `alumnos`
  - `belongsTo` → `anios_academicos`

---

## 4. Estructura de Profesores

### `profesores`
- **Rol**: Registro de docentes del sistema
- **Campos**: id, nombre, email (único), cedula (único), created_at, updated_at
- **Relaciones**:
  - `hasMany` → `profesor_categoria_grado`
  - `belongsToMany` → `departamentos` (miembro_departamento)
  - `hasMany` → `jefe_departamento`

### `categorias_docentes`
- **Rol**: Clasificación del nivel docente (Asistente, Instructor, Profesor, etc.)
- **Campos**: id, nombre (único), created_at, updated_at
- **Relaciones**:
  - `hasMany` → `profesor_categoria_grado`

### `categorias_cientificas`
- **Rol**: Nivel de investigación científica (Novicio, Juntor, Senior, etc.)
- **Campos**: id, nombre (único), created_at, updated_at
- **Relaciones**:
  - `hasMany` → `profesor_categoria_grado`

### `grados_titulo`
- **Rol**: Nivel académico alcanzado (Licenciado, Master, Doctor, etc.)
- **Campos**: id, nombre (único), created_at, updated_at
- **Relaciones**:
  - `hasMany` → `profesor_categoria_grado`

### `profesor_categoria_grado`
- **Rol**: Tabla pivote que vincula profesor con sus categorías y grado (permite cambios en el tiempo)
- **Campos**: id, profesor_id, categoria_docente_id, categoria_cientifica_id, grado_titulo_id, created_at, updated_at
- **Restricción**: Única por combinación (profesor, categoria_docente, categoria_cientifica, grado)
- **Relaciones**:
  - `belongsTo` → `profesores`
  - `belongsTo` → `categorias_docentes`
  - `belongsTo` → `categorias_cientificas`
  - `belongsTo` → `grados_titulo`

### `miembro_departamento`
- **Rol**: Vinculación de un profesor con un departamento
- **Campos**: id, profesor_id, departamento_id, created_at, updated_at
- **Restricción**: Única por profesor-departamento
- **Relaciones**:
  - `belongsTo` → `profesores`
  - `belongsTo` → `departamentos`

### `jefe_departamento`
- **Rol**: Designación de un profesor como responsable de un departamento
- **Campos**: id, profesor_id, departamento_id, fecha_inicio, fecha_fin (nullable), created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `profesores`
  - `belongsTo` → `departamentos`

### `alumno_ayudante`
- **Rol**: Designación de alumno como asistente de una asignatura
- **Campos**: id, alumno_id, asignatura_id, fecha_inicio, fecha_fin (nullable), created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `alumnos`
  - `belongsTo` → `asignaturas`

---

## 5. Estructura de Indicadores

### `indicadores`
- **Rol**: Métrica o indicador de calidad académica
- **Campos**: id, nombre, fuente (baja_estudiante | evaluacion_integral), categoria_indicador_id, created_at, updated_at
- **Relaciones**:
  - `belongsTo` → `categorias_indicador`
  - `hasMany` → `indicador_facultad`
  - `hasMany` → `indicador_departamento`
  - `hasMany` → `indicador_carrera`
  - `hasMany` → `indicador_grupo`
  - `hasMany` → `indicador_anno`
  - `belongsToMany` → `categorias_indicador` (indicador_categoria)
  - `hasMany` → `indicador_curso`

### `categorias_indicador`
- **Rol**: Clasificación de indicadores por área (Docencia, Investigación, Extensión, etc.)
- **Campos**: id, nombre (único), created_at, updated_at
- **Relaciones**:
  - `hasMany` → `indicadores`
  - `belongsToMany` → `indicadores` (indicador_categoria)

### `indicador_facultad`
- **Rol**: Evaluación de un indicador a nivel de facultad
- **Campos**: id, indicador_id, facultad_id, curso_academico_id (nullable), created_at, updated_at
- **Restricción**: Única por indicador-facultad-curso
- **Relaciones**:
  - `belongsTo` → `indicadores`
  - `belongsTo` → `facultades`
  - `belongsTo` → `curso_academico`

### `indicador_departamento`
- **Rol**: Evaluación de un indicador a nivel de departamento
- **Campos**: id, indicador_id, departamento_id, curso_academico_id, created_at, updated_at
- **Restricción**: Única por indicador-departamento-curso
- **Relaciones**:
  - `belongsTo` → `indicadores`
  - `belongsTo` → `departamentos`
  - `belongsTo` → `curso_academico`

### `indicador_carrera`
- **Rol**: Evaluación de un indicador a nivel de carrera
- **Campos**: id, indicador_id, carrera_id, curso_academico_id, created_at, updated_at
- **Restricción**: Única por indicador-carrera-curso
- **Relaciones**:
  - `belongsTo` → `indicadores`
  - `belongsTo` → `carreras`
  - `belongsTo` → `curso_academico`

### `indicador_grupo`
- **Rol**: Evaluación de un indicador a nivel de grupo
- **Campos**: id, indicador_id, grupo_id, curso_academico_id, created_at, updated_at
- **Restricción**: Única por indicador-grupo-curso
- **Relaciones**:
  - `belongsTo` → `indicadores`
  - `belongsTo` → `grupos`
  - `belongsTo` → `curso_academico`

### `indicador_anno`
- **Rol**: Evaluación de un indicador a nivel de año académico
- **Campos**: id, indicador_id, anio_academico_id, curso_academico_id, created_at, updated_at
- **Restricción**: Única por indicador-año-curso
- **Relaciones**:
  - `belongsTo` → `indicadores`
  - `belongsTo` → `anios_academicos`
  - `belongsTo` → `curso_academico`

### `indicador_categoria`
- **Rol**: Tabla pivote para vincular indicadores con categorías (relación muchos a muchos)
- **Campos**: id, indicador_id, categoria_indicador_id, created_at, updated_at
- **Restricción**: Única por indicador-categoria
- **Relaciones**:
  - `belongsTo` → `indicadores`
  - `belongsTo` → `categorias_indicador`

### `indicador_curso`
- **Rol**: Evaluación de un indicador en un curso académico específico
- **Campos**: id, indicador_id, curso_academico_id, created_at, updated_at
- **Restricción**: Única por indicador-curso
- **Relaciones**:
  - `belongsTo` → `indicadores`
  - `belongsTo` → `curso_academico`

---

## 6. Estructura de Control de Versiones

### `curso_academico`
- **Rol**: Período académico principal (ej: 2025-2026)
- **Campos**: id, nombre (único), fecha_inicio, fecha_fin, activo, created_at, updated_at
- **Relaciones**:
  - `hasMany` → `indicador_facultad`
  - `hasMany` → `indicador_departamento`
  - `hasMany` → `indicador_carrera`
  - `hasMany` → `indicador_grupo`
  - `hasMany` → `indicador_anno`
  - `hasMany` → `indicador_curso`

### `versiones`
- **Rol**: Versión del sistema o punto de revisión de datos
- **Campos**: id, nombre, descripcion (nullable), creada_por (nullable), created_at, updated_at
- **Relaciones**:
  - `belongsToMany` → `modificaciones` (version_modificaciones)

### `modificaciones`
- **Rol**: Registro de cambios realizados en la base de datos
- **Campos**: id, tabla, registro_id, tipo (create|update|delete), datos_anteriores (JSON), datos_nuevos (JSON), usuario_id (nullable), created_at, updated_at
- **Relaciones**:
  - `belongsToMany` → `versiones` (version_modificaciones)

### `version_modificaciones`
- **Rol**: Tabla pivote que vincula versiones con modificaciones
- **Campos**: id, version_id, modificacion_id, created_at, updated_at
- **Restricción**: Única por version-modificacion
- **Relaciones**:
  - `belongsTo` → `versiones`
  - `belongsTo` → `modificaciones`

---

## 7. Relaciones Pivote Principales

| Tabla Pivote | Entidad 1 | Entidad 2 | Uso |
|---|---|---|---|
| `miembro_departamento` | Profesor | Departamento | Un profesor puede pertenecer a varios departamentos |
| `profesor_categoria_grado` | Profesor | Categoría+Científica+Grado | Historial de categorías y grados de profesor |
| `carrera_departamento` | Carrera | Departamento | Un departamento puede gestionar varias carreras |
| `carrera_plan` | Carrera | Plan de Estudio | Una carrera puede tener varios planes |
| `facultad_departamento` | Facultad | Departamento | Departamentos asociados a una facultad |
| `indicador_categoria` | Indicador | Categoría Indicador | Un indicador puede pertenecer a varias categorías |
| `version_modificaciones` | Version | Modificación | Una versión agrupa múltiples cambios |

---

## 8. Datos Poblados en el Seeder

El sistema actual contiene:

- **Facultades**: 2 (FMFC, Humanidades)
- **Departamentos**: 4
- **Carreras**: 7 (5 en FMFC, 2 en Humanidades)
- **Años Académicos**: 28 (4-5 por carrera)
- **Grupos**: 84 (3 por año académico)
- **Alumnos**: ~3,173
- **Bajas Estudiantes**: ~490 (10-20% de estudiantes)
- **Evaluaciones Integrales**: 1 por alumno
- **Profesores**: 30
- **Categorías Docentes**: 5
- **Categorías Científicas**: 4
- **Grados de Título**: 4
- **Cursos Académicos**: 4 (2023-2026)
- **Versiones**: 4
- **Modificaciones**: 50
- **Indicadores**: 12
- **Categorías de Indicadores**: 4

---

## 9. Restricciones de Integridad

### Unicidad
- `facultades.nombre`
- `categorias_docentes.nombre`
- `categorias_cientificas.nombre`
- `grados_titulo.nombre`
- `profesores.email`, `profesores.cedula`
- `profesores.nombre` (único combinado con fecha)
- `profesor_categoria_grado` (profesor_id, categoria_docente_id, categoria_cientifica_id, grado_titulo_id)
- `miembro_departamento` (profesor_id, departamento_id)
- `carrera_departamento` (carrera_id, departamento_id)
- `carrera_plan` (carrera_id, plan_estudio_id)
- `facultad_departamento` (facultad_id, departamento_id)
- `indicador_facultad` (indicador_id, facultad_id, curso_academico_id)
- `indicador_departamento` (indicador_id, departamento_id, curso_academico_id)
- `indicador_carrera` (indicador_id, carrera_id, curso_academico_id)
- `indicador_grupo` (indicador_id, grupo_id, curso_academico_id)
- `indicador_anno` (indicador_id, anio_academico_id, curso_academico_id)
- `indicador_categoria` (indicador_id, categoria_indicador_id)
- `indicador_curso` (indicador_id, curso_academico_id)
- `version_modificaciones` (version_id, modificacion_id)
- `bajas_estudiantes.alumno_id` (máximo una baja por alumno)

### Cascadas
- Todas las claves foráneas utilizar `cascadeOnDelete()`
- Los soft deletes aplicar en `alumnos`

---

## 10. Rutas Disponibles

El sistema incluye un endpoint para visualizar indicadores de bajas:

```
GET /indicadores/bajas
```

Con parámetros de filtro:
- `anio_academico_id`
- `facultad_id`
- `carrera_id`
- `grupo_id`

---

## Notas Adicionales

1. **Evaluaciones Integrales**: Cada alumno puede tener múltiples evaluaciones a lo largo de su trayectoria académica, una por año académico.

2. **Historial de Profesores**: La tabla `profesor_categoria_grado` permite registrar cambios en las categorías de profesores sin perder el historial.

3. **Análisis Multinivel**: Los indicadores pueden analizarse a 6 niveles diferentes de jerarquía, permitiendo comparaciones horizontales y verticales.

4. **Trazabilidad**: El sistema de versiones y modificaciones permite auditar todos los cambios realizados en datos críticos.

5. **Flexibilidad Académica**: El sistema soporta carreras de diferentes duraciones (4 o 5 años) y múltiples planes de estudio por carrera.

