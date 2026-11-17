<nav class="academic-nav" aria-label="Navegación principal">
    <div class="academic-nav-inner">
        <a class="academic-brand" href="{{ route('dashboard') }}"><span class="brand-mark" aria-hidden="true">A<span>·</span></span><span>Agenda Semestral<small>GESTIÓN ACADÉMICA</small></span></a>
        <div class="academic-links">
            <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Inicio</a>
            <a href="{{ route('indicadores') }}" @if(request()->routeIs('indicadores*')) aria-current="page" @endif>Indicadores</a>
            <a href="{{ route('planestudio') }}" @if(request()->routeIs('planestudio')) aria-current="page" @endif>Plan de estudio</a>
            <a href="{{ route('alumnoayudante') }}" @if(request()->routeIs('alumnoayudante*')) aria-current="page" @endif>Alumnos ayudantes</a>
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="academic-logout" type="submit">Salir <span aria-hidden="true">↗</span></button></form>
    </div>
</nav>
