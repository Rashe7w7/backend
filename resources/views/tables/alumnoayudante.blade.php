


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema PPA - Alumnos Ayudantes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen flex">

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="w-60 bg-gray-100 p-6 min-h-screen border-r border-gray-300">
        <h2 class="text-2xl font-bold text-orange-500 mb-8">Sistema PPA</h2>
        <nav class="space-y-2">
            <a href="{{ route('roles') }}" class="block px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-200 hover:text-orange-500 transition-colors">
                Gestión de Roles
            </a>
            <a href="{{ route('alumnoayudante') }}" class="block px-4 py-3 rounded-lg bg-orange-500 text-white font-semibold transition-colors">
                Alumno Ayudante
            </a>
            <a href="{{ route('profesor') }}" class="block px-4 py-3 rounded-lg text-gray-600 hover:bg-gray-200 hover:text-orange-500 transition-colors">
                Profesor
            </a>
        </nav>
    </aside>

    <!-- ==================== CONTENIDO PRINCIPAL ==================== -->
    <main class="flex-1 p-8">

        <!-- Titulo -->
        <h1 class="text-3xl font-bold text-orange-500">Alumnos Ayudantes</h1>
        <p class="text-orange-400 mb-6">Gestion del sistema</p>

        <!-- Boton Designar -->
        <button id="btnDesignar" onclick="abrirFormulario()" class="px-5 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600">
            + Designar Nuevo
        </button>

        <!-- ==================== FORMULARIO DESIGNAR ==================== -->
        <div id="formulario" class="hidden mt-6 p-6 border-2 border-orange-300 rounded-xl bg-orange-50">
            <h2 class="text-xl font-bold text-orange-500 mb-4">Designar Alumno Ayudante</h2>

            <div class="grid grid-cols-1 gap-4 max-w-lg">
                <!-- Alumno Existente -->
                <div>
                    <label class="block text-orange-600 font-medium mb-1">Alumno *</label>
                    <select id="alumno_id" class="w-full px-3 py-2 border-2 border-orange-300 rounded-lg focus:outline-none focus:border-orange-500">
                        <option value="">Seleccione un alumno</option>
                        @foreach($alumnos as $alumno)
                            <option value="{{ $alumno->id }}">{{ $alumno->nombre }} ({{ $alumno->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Botones Formulario -->
            <div class="mt-6 flex gap-3">
                <button onclick="cerrarFormulario()" class="px-5 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600">
                    Cancelar
                </button>
                <button onclick="continuar()" class="px-5 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600">
                    Continuar
                </button>
            </div>
        </div>

        <!-- ==================== TABLA DE ASIGNADOS ==================== -->
        <h2 class="text-xl font-bold text-orange-500 mt-8 mb-4">Lista de Asignados</h2>

        <div class="border-2 border-orange-300 rounded-xl overflow-hidden">
            <table class="w-full">
                <thead class="bg-orange-500 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Estudiante</th>
                        <th class="px-4 py-3 text-left">Ano</th>
                        <th class="px-4 py-3 text-left">Grupo</th>
                        <th class="px-4 py-3 text-left">Asignatura</th>
                        <th class="px-4 py-3 text-left">Profesor</th>
                        <th class="px-4 py-3 text-left">Accion</th>
                    </tr>
                </thead>
                <tbody id="tablaAsignados">
                    @foreach($alumnosAyudantes as $aa)
                    <tr class="border-t border-orange-200 hover:bg-orange-50" data-id="{{ $aa->id }}">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-800">{{ $aa->alumno->nombre }}</div>
                            <div class="text-sm text-gray-500">{{ $aa->alumno->email }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $aa->cursoAcademico->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">-</td>
                        <td class="px-4 py-3 text-gray-700">{{ $aa->profesorAsignatura->asignatura->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $aa->profesorAsignatura->profesor->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <button onclick="eliminarFila(this)" class="text-red-500 hover:text-red-700 font-medium">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </main>

    <!-- ==================== MODAL CONFIRMAR DESIGNACION ==================== -->
    <div id="modalDesignar" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-xl w-96 border-2 border-orange-500 shadow-xl">
            <h3 class="text-xl font-bold text-orange-500 mb-2">Confirmar Designacion</h3>
            <p id="nombreEstudiante" class="text-gray-700 mb-4 font-medium"></p>

            <div class="space-y-4">
                <!-- Ano Academico -->
                <div>
                    <label class="block text-orange-600 font-medium mb-1">Ano Academico</label>
                    <select id="anno" class="w-full px-3 py-2 border-2 border-orange-300 rounded-lg">
                        @foreach($cursosAcademicos as $curso)
                            <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Asignatura -->
                <div>
                    <label class="block text-orange-600 font-medium mb-1">Asignatura</label>
                    <select id="asignatura" class="w-full px-3 py-2 border-2 border-orange-300 rounded-lg">
                        @foreach($asignaturas as $asignatura)
                            <option value="{{ $asignatura->id }}">{{ $asignatura->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Profesor -->
                <div>
                    <label class="block text-orange-600 font-medium mb-1">Profesor</label>
                    <select id="profesor" class="w-full px-3 py-2 border-2 border-orange-300 rounded-lg">
                        @foreach($profesores as $profesor)
                            <option value="{{ $profesor->id }}">{{ $profesor->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Grupo -->
                <div>
                    <label class="block text-orange-600 font-medium mb-1">Grupo</label>
                    <select id="grupo" class="w-full px-3 py-2 border-2 border-orange-300 rounded-lg">
                        <option value="">Seleccione un grupo</option>
                        @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Botones Modal -->
            <div class="mt-6 flex gap-3 justify-end">
                <button onclick="cerrarModal()" class="px-5 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600">
                    Cancelar
                </button>
                <button onclick="confirmarDesignacion()" class="px-5 py-2 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL ELIMINAR ==================== -->
    <div id="modalEliminar" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-xl w-80 border-2 border-orange-500 shadow-xl">
            <h3 class="text-xl font-bold text-orange-500 mb-4">Confirmar</h3>
            <p class="text-gray-700 mb-6">¿Desea eliminar esta asignacion?</p>
            <div class="flex gap-3 justify-end">
                <button onclick="cerrarModalEliminar()" class="px-5 py-2 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600">
                    No
                </button>
                <button onclick="confirmarEliminar()" class="px-5 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700">
                    Si, Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== JAVASCRIPT ==================== -->
    <script>
        // Variables globales
        var estudianteTemp = null;
        var filaAEliminar = null;

        // ===== FORMULARIO =====
        function abrirFormulario() {
            document.getElementById('formulario').classList.remove('hidden');
            document.getElementById('btnDesignar').classList.add('hidden');
        }

        function cerrarFormulario() {
            document.getElementById('formulario').classList.add('hidden');
            document.getElementById('btnDesignar').classList.remove('hidden');
            limpiarCampos();
        }

        function limpiarCampos() {
            document.getElementById('nombreCompleto').value = '';
            document.getElementById('email').value = '';
            document.getElementById('carrera').value = '';
        }

        // ===== CONTINUAR A MODAL =====
        function continuar() {
            var alumno_id = document.getElementById('alumno_id').value;

            // Validar campos obligatorios
            if (!alumno_id) {
                alert('Seleccione un alumno');
                return;
            }

            // Obtener nombre del alumno seleccionado
            var alumnoOption = document.querySelector('#alumno_id option[value="' + alumno_id + '"]');
            var alumnoNombre = alumnoOption.text.split(' (')[0];

            // Guardar datos temporales
            estudianteTemp = {
                alumno_id: alumno_id,
                nombre: alumnoNombre
            };

            // Mostrar nombre en modal
            document.getElementById('nombreEstudiante').textContent = alumnoNombre;

            // Abrir modal
            document.getElementById('modalDesignar').classList.remove('hidden');
        }

        // ===== MODAL DESIGNAR =====
        function cerrarModal() {
            document.getElementById('modalDesignar').classList.add('hidden');
        }

        async function confirmarDesignacion() {
            if (!estudianteTemp) return;

            var cursoAcademicoId = document.getElementById('anno').value;
            var asignaturaId = document.getElementById('asignatura').value;
            var profesorId = document.getElementById('profesor').value;
            var grupoId = document.getElementById('grupo').value;

            try {
                const response = await fetch('/api/alumnoayudante', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        alumno_id: parseInt(estudianteTemp.alumno_id),
                        asignatura_id: parseInt(asignaturaId),
                        profesor_id: parseInt(profesorId),
                        grupo_id: grupoId ? parseInt(grupoId) : null,
                        curso_academico_id: parseInt(cursoAcademicoId)
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    alert('❌ Error: ' + (data.error || 'Error al guardar la designación'));
                    return;
                }

                // Obtener nombres para mostrar
                var annoText = document.querySelector('#anno option[value="' + cursoAcademicoId + '"]').text;
                var asignaturaText = document.querySelector('#asignatura option[value="' + asignaturaId + '"]').text;
                var profesorText = document.querySelector('#profesor option[value="' + profesorId + '"]').text;
                var grupoText = grupoId ? document.querySelector('#grupo option[value="' + grupoId + '"]').text : '-';

                // Crear nueva fila
                var fila = document.createElement('tr');
                fila.className = 'border-t border-orange-200 hover:bg-orange-50';
                fila.dataset.id = data.id;
                fila.innerHTML = 
                    '<td class="px-4 py-3">' +
                        '<div class="font-medium text-gray-800">' + estudianteTemp.nombre + '</div>' +
                    '</td>' +
                    '<td class="px-4 py-3 text-gray-700">' + annoText + '</td>' +
                    '<td class="px-4 py-3 text-gray-700">' + grupoText + '</td>' +
                    '<td class="px-4 py-3 text-gray-700">' + asignaturaText + '</td>' +
                    '<td class="px-4 py-3 text-gray-700">' + profesorText + '</td>' +
                    '<td class="px-4 py-3">' +
                        '<button onclick="eliminarFila(this)" class="text-red-500 hover:text-red-700 font-medium">Eliminar</button>' +
                    '</td>';

                // Agregar a tabla
                document.getElementById('tablaAsignados').appendChild(fila);

                // Cerrar todo
                cerrarModal();
                cerrarFormulario();
            } catch (error) {
                alert('❌ Error al guardar la designación: ' + error.message);
            }
        }

        // ===== ELIMINAR =====
        function eliminarFila(btn) {
            filaAEliminar = btn.parentElement.parentElement;
            document.getElementById('modalEliminar').classList.remove('hidden');
        }

        function cerrarModalEliminar() {
            document.getElementById('modalEliminar').classList.add('hidden');
            filaAEliminar = null;
        }

        async function confirmarEliminar() {
            if (!filaAEliminar) return;

            const alumnoayudanteId = filaAEliminar.dataset.id;

            if (!alumnoayudanteId) {
                alert('❌ Error: No se pudo identificar el registro');
                cerrarModalEliminar();
                return;
            }

            try {
                const response = await fetch('/api/alumnoayudante/' + alumnoayudanteId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    filaAEliminar.remove();
                } else {
                    const data = await response.json();
                    alert('❌ Error al eliminar: ' + (data.error || 'Error desconocido'));
                }
            } catch (error) {
                alert('❌ Error al eliminar: ' + error.message);
            }

            cerrarModalEliminar();
        }
    </script>

</body>
</html>
