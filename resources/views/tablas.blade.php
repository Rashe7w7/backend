<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de Datos - Tablas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Base de Datos</h1>
                <p class="text-lg text-gray-600">Visualizador de Tablas y Registros</p>
            </div>

            <!-- Tablas -->
            @if(empty($tablesData))
                <div class="card p-8 text-center">
                    <p class="text-gray-500 text-lg">No hay tablas para mostrar</p>
                </div>
            @else
                <div class="grid gap-8">
                    @foreach($tablesData as $table)
                        <div class="card overflow-hidden">
                            <!-- Header de la Tabla -->
                            <div class="card-header flex justify-between items-center">
                                <h2 class="text-2xl font-bold uppercase tracking-wide">{{ $table['name'] }}</h2>
                                <span class="bg-white bg-opacity-30 px-4 py-2 rounded-full text-white font-semibold">
                                    {{ $table['count'] }} registros
                                </span>
                            </div>

                            <!-- Contenido de la Tabla -->
                            <div class="p-6">
                                <!-- Columnas -->
                                <div class="mb-6 pb-6 border-b border-gray-200">
                                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">
                                        Columnas ({{ count($table['columns']) }})
                                    </h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($table['columns'] as $column)
                                            <span class="badge">{{ $column['name'] }}</span>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Datos de la Tabla -->
                                @if(count($table['data']) > 0)
                                    <div class="overflow-x-auto mb-6">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="bg-gray-100 border-b border-gray-200">
                                                    @foreach($table['columns'] as $column)
                                                        <th class="px-4 py-3 text-left font-semibold text-gray-700 uppercase text-xs tracking-wider">
                                                            {{ $column['name'] }}
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($table['data'] as $row)
                                                    <tr class="hover:bg-gray-50 transition-colors">
                                                        @foreach((array)$row as $value)
                                                            <td class="px-4 py-3 text-gray-700">
                                                                @if(is_null($value))
                                                                    <span class="text-gray-400 italic">NULL</span>
                                                                @elseif(is_bool($value))
                                                                    <span class="@if($value) text-green-600 font-semibold @else text-red-600 font-semibold @endif">
                                                                        {{ $value ? '✓ Sí' : '✗ No' }}
                                                                    </span>
                                                                @else
                                                                    {{ substr((string)$value, 0, 100) }}
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Estadísticas -->
                                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-gray-200">
                                        <div class="text-center">
                                            <p class="text-gray-600 text-sm uppercase tracking-wider mb-1">Mostrados</p>
                                            <p class="text-2xl font-bold text-blue-600">{{ count($table['data']) }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-gray-600 text-sm uppercase tracking-wider mb-1">Total</p>
                                            <p class="text-2xl font-bold text-blue-600">{{ $table['count'] }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-gray-600 text-sm uppercase tracking-wider mb-1">Columnas</p>
                                            <p class="text-2xl font-bold text-blue-600">{{ count($table['columns']) }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <p class="text-gray-500">Esta tabla no contiene registros</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</body>
</html>
