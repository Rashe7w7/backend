<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Roles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilos personalizados adicionales */
        .sidebar a.active {
            background-color: #f97316;
            color: #ffffff;
        }
    </style>
</head>
<body class="bg-white text-gray-800 min-h-screen">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-gray-100 p-6 border-r border-gray-300">
        <h2 class="text-orange-500 text-xl font-semibold mb-8 tracking-wide">Sistema PPA</h2>
        <nav class="space-y-2">
            <a href="{{ route('roles') }}" class="block px-4 py-3 rounded-lg transition-colors active text-white bg-orange-500">
                Gestión de Roles
            </a>
            <a href="{{ route('alumnoayudante') }}" class="block text-gray-600 hover:bg-gray-200 hover:text-orange-500 px-4 py-3 rounded-lg transition-colors">
                Alumno Ayudante
            </a>
            <a href="{{ route('profesor') }}" class="block text-gray-600 hover:bg-gray-200 hover:text-orange-500 px-4 py-3 rounded-lg transition-colors">
                Profesor
            </a>
        </nav>
    </aside>

    <!-- Contenido principal -->
    <main class="flex-1 p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Gestión de Roles</h1>
        <p class="text-gray-600 mb-8 text-sm">Roles asignados – Año académico 2025</p>
        
        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
            <p class="text-gray-600">Contenido de gestión de roles</p>
        </div>
    </main>
</div>
</body>
</html>