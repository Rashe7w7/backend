<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Agenda Semestral</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-white">

    <!-- Navbar -->
    <nav class="bg-gray-100 border-b border-orange-500 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">

            <!-- Secciones del navbar -->
            <div class="flex space-x-6">
                <a href="{{ route('indicadores') }}" class="text-gray-600 hover:text-orange-500 font-semibold">Indicadores</a>
                <a href="{{ route('planestudio') }}" class="text-gray-600 hover:text-orange-500 font-semibold">Plan de Estudio</a>
                <a href="{{ route('roles') }}" class="text-gray-600 hover:text-orange-500 font-semibold">Gestión de Roles</a>
                <a href="#" class="text-gray-600 hover:text-orange-500 font-semibold">Acerca de</a>
            </div>

            

        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto p-6">

        <!-- Sección de tarjetas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Card 1 -->
            <div class="bg-white rounded-lg p-6 border border-orange-500 shadow-md">
                <h2 class="text-xl font-bold text-orange-500 mb-2">Indicadores</h2>
                <p class="text-gray-600 mb-4">Gestiona los indicadores académicos del semestre</p>
                <a href="{{ route('indicadores') }}" class="bg-orange-500 text-white px-4 py-2 rounded font-semibold hover:bg-orange-600">Ver más</a>
            </div>

            <!-- Card 2 -->
            <div class="bg-white rounded-lg p-6 border border-orange-500 shadow-md">
                <h2 class="text-xl font-bold text-orange-500 mb-2">Plan de Estudio</h2>
                <p class="text-gray-600 mb-4">Administra asignaturas y horarios del semestre</p>
                <a href="{{ route('planestudio') }}" class="bg-orange-500 text-white px-4 py-2 rounded font-semibold hover:bg-orange-600">Ver más</a>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-lg p-6 border border-orange-500 shadow-md">
                <h2 class="text-xl font-bold text-orange-500 mb-2">Gestión de Roles</h2>
                <p class="text-gray-600 mb-4">Visualiza y asigna roles a los usuarios</p>
                <a href="{{ route('roles') }}" class="bg-orange-500 text-white px-4 py-2 rounded font-semibold hover:bg-orange-600">Ver más</a>
            </div>

        </div>

      

    </main>

</body>
</html>
