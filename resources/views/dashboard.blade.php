<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio | Agenda Semestral</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="academic-ui min-h-screen">
    @include('partials.academic-nav')
    <main class="dashboard-shell">
        <header class="dashboard-hero">
            <div>
                <p class="eyebrow">Tu espacio de trabajo</p>
                <h1>La gestión académica,<br>en un mismo lugar.</h1>
                <p>Bienvenido, {{ auth()->user()->name }}. Organiza el semestre y da seguimiento a la actividad académica de tu institución.</p>
            </div>
            <span class="workspace-label">Agenda Semestral</span>
        </header>
        <section class="dashboard-feature" aria-labelledby="feature-title">
            <div>
                <p class="eyebrow">Una mirada al semestre</p>
                <h2 id="feature-title">Cada indicador cuenta.</h2>
                <p>Consulta los períodos académicos, organiza tus indicadores y mantén la información lista para tomar decisiones.</p>
            </div>
            <a href="{{ route('indicadores') }}" class="feature-link">Explorar indicadores <span aria-hidden="true">↗</span></a>
        </section>
        <div class="module-heading"><h2>Herramientas de gestión</h2><p>Acceso a tus módulos</p></div>
        <div class="module-grid">
            <a class="module-card" href="{{ route('indicadores') }}">
                <span class="module-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 20h16M7 16v-5m5 5V4m5 12V8" stroke-linecap="round"/></svg></span>
                <h3>Indicadores</h3><p>Consulta y organiza los indicadores académicos de cada período.</p><span class="module-action">Ver indicadores <span aria-hidden="true">↗</span></span>
            </a>
            <a class="module-card" href="{{ route('planestudio') }}">
                <span class="module-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 5v15M3 4c4-1 6 0 9 1 3-1 5-2 9-1v14c-4-1-6 0-9 2-3-2-5-3-9-2Z" stroke-linejoin="round"/></svg></span>
                <h3>Plan de estudio</h3><p>Explora las disciplinas y asignaturas que dan forma al semestre.</p><span class="module-action">Consultar plan <span aria-hidden="true">↗</span></span>
            </a>
            <a class="module-card" href="{{ route('alumnoayudante') }}">
                <span class="module-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="9" cy="8" r="3"/><path d="M3 20v-2a6 6 0 0 1 12 0v2M16 5a3 3 0 0 1 0 6m2 4a5 5 0 0 1 3 5" stroke-linecap="round"/></svg></span>
                <h3>Alumnos ayudantes</h3><p>Consulta las designaciones y analiza su distribución académica.</p><span class="module-action">Gestionar alumnos ayudantes <span aria-hidden="true">↗</span></span>
            </a>
        </div>
        <p class="dashboard-footer">Agenda Semestral · Sistema de gestión académica</p>
    </main>
</body>
</html>
