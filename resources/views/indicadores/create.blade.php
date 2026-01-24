<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Indicador</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-black">

    <!-- Navbar -->
    <nav class="bg-neutral-900 border-b border-orange-500 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold text-orange-500">Indicadores</h1>
            <a href="{{ route('indicadores') }}" class="text-gray-400 hover:text-orange-500">Volver</a>
        </div>
    </nav>

    <main class="max-w-2xl mx-auto p-6">
        <h2 class="text-2xl font-bold text-orange-500 mb-6">Crear Nuevo Indicador</h2>

        <div class="bg-neutral-900 rounded-lg p-6 border border-orange-500">
            <form action="{{ route('indicadores.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nombre -->
                <div>
                    <label class="text-white font-semibold mb-2 block">Nombre</label>
                    <input type="text" name="nombre" placeholder="Ej: Desempeño Académico"
                           class="w-full p-2 rounded bg-black border border-orange-500 text-white" required>
                </div>

                <!-- Tipo -->
                <div>
                    <label class="text-white font-semibold mb-2 block">Tipo</label>
                    <select name="tipo" class="w-full p-2 rounded bg-black border border-orange-500 text-white" required>
                        <option value="">-- Selecciona un tipo --</option>
                        <option value="grupo">Grupo</option>
                        <option value="ano_academico">Año Académico</option>
                        <option value="facultad">Facultad</option>
                    </select>
                </div>

                <!-- Categorías -->
                <div>
                    <label class="text-white font-semibold mb-2 block">Categorías</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto bg-black rounded border border-orange-500 p-3">
                        @forelse($categorias as $categoria)
                        <label class="flex items-center text-gray-300 hover:text-orange-500 cursor-pointer">
                            <input type="checkbox" name="categorias[]" value="{{ $categoria->id }}" class="mr-2">
                            {{ $categoria->nombre }}
                        </label>
                        @empty
                        <p class="text-gray-500 text-sm">Sin categorías disponibles</p>
                        @endforelse
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex space-x-4 mt-6">
                    <button type="submit" class="bg-orange-500 text-black px-6 py-2 rounded font-semibold hover:bg-orange-600">
                        Crear
                    </button>
                    <a href="{{ route('indicadores') }}" class="bg-gray-600 text-white px-6 py-2 rounded font-semibold hover:bg-gray-700">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>
