<nav class="academic-nav" aria-label="Navegación principal">
    <div class="academic-nav-inner">
        <a class="academic-brand" href="{{ route('dashboard') }}"><span class="brand-mark" aria-hidden="true">A<span>·</span></span><span>Agenda Semestral<small>GESTIÓN ACADÉMICA</small></span></a>
        <div class="academic-links">
            <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Inicio</a>
            <a href="{{ route('indicadores') }}" @if(request()->routeIs('indicadores*')) aria-current="page" @endif>Indicadores</a>
            @if(auth()->user()->isAdministrator() || auth()->user()->hasRole('ppa'))<a href="{{ route('ppa.index') }}" @if(request()->routeIs('ppa.*')) aria-current="page" @endif>PPA</a><a href="{{ route('alumnoayudante') }}" @if(request()->routeIs('alumnoayudante')) aria-current="page" @endif>Alumnos Ayudantes</a>@endif
            @can('manage-users')<a href="{{ route('roles') }}" @if(request()->routeIs('roles*')) aria-current="page" @endif>Usuarios y roles</a>@endcan
            @can('view-audit')<a href="{{ route('audit.index') }}" @if(request()->routeIs('audit.*')) aria-current="page" @endif>Auditoría</a>@endcan
        </div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="academic-logout" type="submit">Salir <span aria-hidden="true">↗</span></button></form>
    </div>
</nav>
