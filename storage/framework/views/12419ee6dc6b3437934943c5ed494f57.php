<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Indicadores | Sistema de Gestión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                        'primary-dark': '#ea580c',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .input-focus:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.3);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">


<nav class="bg-white border-b-2 border-primary shadow-sm sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            
            <h1 class="text-2xl font-bold text-gray-800">Indicadores</h1>
        </div>
        
    </div>
</nav>

<main class="max-w-6xl mx-auto p-4 md:p-6 space-y-6"> 

    
    <section class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 mb-3">
          
            <h2 class="font-bold text-gray-800">Período Académico (Curso)</h2>
        
        </div>
        <select id="cursoSelect" class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus hover:border-primary transition-colors cursor-pointer bg-white">
            <option value="">-- Selecciona período académico --</option>
        </select>
    </section>

   
    <div class="grid md:grid-cols-2 gap-6">

       
        <section class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <h2 class="font-bold text-gray-800">Crear nuevo indicador</h2>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
                    <input type="text" id="nombreIndicador" placeholder="Aprobados con 2 arrastres" 
                           class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus hover:border-primary transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Categoría</label>
                    <div class="flex gap-2">
                        <select id="categoriaIndicador" class="flex-1 p-3 border-2 border-gray-200 rounded-lg input-focus hover:border-primary transition-colors cursor-pointer bg-white">
                            <option value="">-- Selecciona categoría --</option>
                        </select>
                        <button onclick="mostrarModalCategoria()" class="p-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors" title="Añadir nueva categoría">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg> 
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Especificación</label>
                    <select id="especificacionIndicador" onchange="actualizarCamposEspecificacion()" class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus hover:border-primary transition-colors cursor-pointer bg-white">
                        <option value="">-- Selecciona especificación --</option>
                        <option value="Carrera">Carrera</option>
                        <option value="Facultad">Facultad</option>
                        <option value="Departamento">Departamento</option>
                        <option value="Grupo">Grupo</option>
                        <option value="Nivel académico">Nivel académico (1, 2, 3...)</option>
                    </select>
                </div>

                <div id="camposEspecificacion" class="hidden space-y-3">
                    <div id="campoCarrera" class="hidden">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Carrera</label>
                        <select id="carreraCrear" class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus cursor-pointer bg-white">
                            <option value="">-- Selecciona carrera --</option>
                        </select>
                    </div>
                    <div id="campoFacultad" class="hidden">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Facultad</label>
                        <select id="facultadCrear" class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus cursor-pointer bg-white">
                            <option value="">-- Selecciona facultad --</option>
                        </select>
                    </div>
                    <div id="campoDepartamento" class="hidden">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Departamento</label>
                        <select id="departamentoCrear" class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus cursor-pointer bg-white">
                            <option value="">-- Selecciona departamento --</option>
                        </select>
                    </div>
                    <div id="campoGrupo" class="hidden">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Grupo</label>
                        <select id="grupoCrear" class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus cursor-pointer bg-white">
                            <option value="">-- Selecciona grupo --</option>
                        </select>
                    </div>
                    <div id="campoAnio" class="hidden">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Nivel Académico <span class="text-xs text-blue-600">(1, 2, 3...)</span></label>
                        <select id="anioCrear" class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus cursor-pointer bg-white">
                            <option value="">-- Selecciona nivel --</option>
                        </select>
                    </div>
                </div>
                
                <button onclick="crearIndicador()" 
                        class="w-full bg-emerald-500 text-white p-3 rounded-lg hover:bg-emerald-600 transition-colors font-semibold flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Crear Indicador
                </button>
            </div>
        </section>

        <section class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <h2 class="font-bold text-gray-800">Indicadores disponibles</h2>
                <span id="contadorIndicadoresDisponibles" class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">0</span>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Filtrar por Especificación</label>
                    <select id="filtroEspecificacion" onchange="filtrarIndicadores()" class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus hover:border-primary transition-colors cursor-pointer bg-white">
                        <option value="">-- Todas las especificaciones --</option>
                        <option value="carrera">Carrera</option>
                        <option value="facultad">Facultad</option>
                        <option value="departamento">Departamento</option>
                        <option value="grupo">Grupo</option>
                        <option value="anno">Año académico</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <select id="indicadorSelect" class="flex-1 p-3 border-2 border-gray-200 rounded-lg input-focus hover:border-primary transition-colors cursor-pointer bg-white">
                        <option value="">-- Selecciona un indicador --</option>
                    </select>
                    <button onclick="agregarIndicadorVista()" 
                            class="px-5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-semibold">
                        Añadir
                    </button>
                </div>
            </div>

       
            <div class="border-2 border-gray-100 rounded-lg max-h-48 overflow-y-auto mt-4">
                <div id="listaIndicadores" class="divide-y divide-gray-100">
                 
                </div>
            </div>
        </section>
    </div>

   
    <section class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                <h2 class="font-bold text-gray-800">Vista de indicadores</h2>
                <span id="contadorIndicadores" class="bg-primary text-white text-xs px-2 py-1 rounded-full">0</span>
            </div>
            <button onclick="limpiarVista()" class="text-sm text-gray-500 hover:text-red-500 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Limpiar todo
            </button>
        </div>
        
        <div id="infoAnoAcademico" class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
            <p class="text-sm text-blue-700"><span class="font-semibold">Período Académico:</span> <span id="annoAcademicoBadge" class="font-medium"></span></p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    
                    <tr class="bg-gradient-to-r from-primary to-orange-400 text-white">
                        <th class="p-3 text-left rounded-tl-lg">Indicador</th>
                        <th class="p-3 text-left">Categoría</th>
                        <th class="p-3 text-left">Especificación</th>
                        <th class="p-3 text-left">Valor</th>
                        <th class="p-3 text-center rounded-tr-lg w-20">Acción</th>
                    </tr>
                </thead>
                <tbody id="vistaBody">
                    <tr id="filaVacia">
                        <td colspan="5" class="p-8 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            No hay indicadores en la vista.<br>
                            <span class="text-sm">Añade indicadores usando el formulario superior.</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 flex flex-wrap gap-3 justify-end">
            <button onclick="exportarCSV()" 
                    class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar CSV
            </button>
            <button onclick="guardarVista()" 
                    class="bg-emerald-500 text-white px-6 py-2 rounded-lg hover:bg-emerald-600 transition-colors font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Guardar Vista
            </button>
        </div>
    </section>
</main>


<div id="modalCategoria" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4 fade-in">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Añadir nueva categoría</h3>
        <input type="text" id="nuevaCategoria" placeholder="Nombre de la categoría" 
               class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus mb-4">
        <div class="flex gap-3">
            <button onclick="cerrarModalCategoria()" class="flex-1 p-3 border-2 border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                Cancelar
            </button>
            <button onclick="agregarCategoria()" class="flex-1 p-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-semibold">
                Añadir
            </button>
        </div>
    </div>
</div>


<div id="toast" class="fixed bottom-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-20 opacity-0 transition-all duration-300 z-50">
    <span id="toastMessage"></span>
</div>

<script>
const API_BASE = '/api';
let indicadoresExistentes = [];
let contadorId = 1;
let categoriasIndicador = {};
let categoriasPorId = {};
let especificacionTabla = null; // Controla el tipo de especificación permitida en la tabla
let annoAcademicoTabla = null; // Almacena el año académico de la tabla


document.addEventListener('DOMContentLoaded', function() {
    cargarCursos();
    cargarCarreras();
    cargarFacultades();
    cargarDepartamentos();
    cargarGrupos();
    cargarAnios();
    // Cargar categorías y luego indicadores para que los nombres de categoría estén listos
    cargarCategorias().then(() => {
        cargarIndicadores();
    });
    actualizarAvisoEspecificacion();
});

function mostrarToast(mensaje, tipo = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    toastMessage.textContent = mensaje;
    toast.className = `fixed bottom-4 right-4 text-white px-6 py-3 rounded-lg shadow-lg transition-all duration-300 z-50 ${tipo === 'success' ? 'bg-emerald-500' : tipo === 'error' ? 'bg-red-500' : 'bg-gray-800'}`;
    toast.style.transform = 'translateY(0)';
    toast.style.opacity = '1';
    setTimeout(() => {
        toast.style.transform = 'translateY(20px)';
        toast.style.opacity = '0';
    }, 3000);
}

// Modal Categoría
function mostrarModalCategoria() {
    document.getElementById('modalCategoria').classList.remove('hidden');
    document.getElementById('nuevaCategoria').focus();
}

function cerrarModalCategoria() {
    document.getElementById('modalCategoria').classList.add('hidden');
    document.getElementById('nuevaCategoria').value = '';
}

function agregarCategoria() {
    const nuevaCategoria = document.getElementById('nuevaCategoria').value.trim();
    if (!nuevaCategoria) {
        mostrarToast('Ingresa un nombre para la categoría', 'error');
        return;
    }
    
    const select = document.getElementById('categoriaIndicador');
    
    // Verificar si ya existe
    for (let option of select.options) {
        if (option.text.toLowerCase() === nuevaCategoria.toLowerCase()) {
            mostrarToast('Esta categoría ya existe', 'error');
            return;
        }
    }
    
    // Enviar la categoría al servidor
    fetch(`${API_BASE}/categorias-indicador`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre: nuevaCategoria })
    }).then(async res => {
        const data = await res.json();
        if (!res.ok) {
            const errorMsg = data.error || data.message || data.errors?.nombre?.[0] || 'Error desconocido';
            throw new Error(errorMsg);
        }
        
        // Añadir a la lista local
        categoriasIndicador[data.nombre] = data.id;
        categoriasPorId[data.id] = data.nombre;
        const option = new Option(data.nombre, data.id);
        select.add(option);
        select.value = data.id;
        
        cerrarModalCategoria();
        mostrarToast(`Categoría "${nuevaCategoria}" añadida correctamente`);
    }).catch(err => {
        console.error('Error adding category:', err);
        mostrarToast(`Error: ${err.message}`, 'error');
    });
}

function crearIndicador() {
    const nombre = document.getElementById('nombreIndicador').value.trim();
    const categoriaIdRaw = document.getElementById('categoriaIndicador').value;
    const categoriaId = categoriaIdRaw ? parseInt(categoriaIdRaw, 10) : null;
    const especificacion = document.getElementById('especificacionIndicador').value;
    
    if (!nombre || !categoriaId || !especificacion) {
        mostrarToast('Ingresa nombre, categoría y especificación', 'error');
        return;
    }
    
    // Validar que el curso académico esté seleccionado para las especificaciones que lo requieren
    const especificacionesQueRequierenCurso = ['Carrera', 'Departamento', 'Grupo', 'Nivel académico'];
    if (especificacionesQueRequierenCurso.includes(especificacion)) {
        const curso = document.getElementById('cursoSelect').value;
        if (!curso) {
            mostrarToast('Selecciona un Período Académico primero en la sección superior', 'error');
            return;
        }
    }
    
    if (!categoriaId) {
        mostrarToast('Categoría no encontrada en la base de datos', 'error');
        return;
    }
    
    // Mapear especificación a minúsculas para el API
    let especificacionKey = '';
    switch(especificacion) {
        case 'Carrera': especificacionKey = 'carrera'; break;
        case 'Facultad': especificacionKey = 'facultad'; break;
        case 'Departamento': especificacionKey = 'departamento'; break;
        case 'Grupo': especificacionKey = 'grupo'; break;
        case 'Nivel académico': especificacionKey = 'anno'; break;
    }
    
    const payload = { 
        nombre, 
        categoria_indicador: categoriaId,
        especificacion: especificacionKey
    };
    
    // Recopilar los IDs según la especificación
    switch(especificacion) {
        case 'Carrera':
            const carreraId = document.getElementById('carreraCrear').value;
            if (!carreraId) {
                mostrarToast('Selecciona una carrera', 'error');
                return;
            }
            payload.carrera_id = parseInt(carreraId, 10);
            // La carrera requiere curso_academico_id
            const curso = document.getElementById('cursoSelect').value;
            if (!curso) {
                mostrarToast('Selecciona un curso académico primero', 'error');
                return;
            }
            payload.curso_academico_id = parseInt(curso, 10);
            break;
        
        case 'Facultad':
            const facultadId = document.getElementById('facultadCrear').value;
            if (!facultadId) {
                mostrarToast('Selecciona una facultad', 'error');
                return;
            }
            payload.facultad_id = parseInt(facultadId, 10);
            break;
        
        case 'Departamento':
            const departamentoId = document.getElementById('departamentoCrear').value;
            if (!departamentoId) {
                mostrarToast('Selecciona un departamento', 'error');
                return;
            }
            payload.departamento_id = parseInt(departamentoId, 10);
            const cursoDept = document.getElementById('cursoSelect').value;
            if (!cursoDept) {
                mostrarToast('Selecciona un curso académico primero', 'error');
                return;
            }
            payload.curso_academico_id = parseInt(cursoDept, 10);
            break;
        
        case 'Grupo':
            const grupoId = document.getElementById('grupoCrear').value;
            if (!grupoId) {
                mostrarToast('Selecciona un grupo', 'error');
                return;
            }
            payload.grupo_id = parseInt(grupoId, 10);
            const cursoGrupo = document.getElementById('cursoSelect').value;
            if (!cursoGrupo) {
                mostrarToast('Selecciona un curso académico primero', 'error');
                return;
            }
            payload.curso_academico_id = parseInt(cursoGrupo, 10);
            break;
        
        case 'Nivel académico':
            const anioId = document.getElementById('anioCrear').value;
            if (!anioId) {
                mostrarToast('Selecciona un nivel académico', 'error');
                return;
            }
            payload.anio_academico_id = parseInt(anioId, 10);
            const cursoAnio = document.getElementById('cursoSelect').value;
            if (!cursoAnio) {
                mostrarToast('Selecciona un período académico primero', 'error');
                return;
            }
            payload.curso_academico_id = parseInt(cursoAnio, 10);
            break;
    }

    fetch(`${API_BASE}/indicadores/create-and-assign`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    }).then(async res => {
        const data = await res.json();
        if (!res.ok) {
            const errorMsg = data.error || data.message || 'Error desconocido';
            throw new Error(errorMsg);
        }
        // Obtener el nombre de la categoría del select
        const selectCat = document.getElementById('categoriaIndicador');
        const categoriaNombre = selectCat.options[selectCat.selectedIndex].text;
        
        // Usar especificacionKey (valor del backend, ej: 'carrera') en lugar de especificacion (label, ej: 'Carrera')
        indicadoresExistentes.push({ id: data.id, nombre: data.nombre, categoria: categoriaNombre, espec: especificacionKey });
        contadorId = Math.max(contadorId, data.id + 1);
        actualizarListaIndicadores();
        actualizarSelectIndicadores();
        document.getElementById('nombreIndicador').value = '';
        document.getElementById('categoriaIndicador').value = '';
        document.getElementById('especificacionIndicador').value = '';
        document.getElementById('carreraCrear').value = '';
        document.getElementById('facultadCrear').value = '';
        document.getElementById('departamentoCrear').value = '';
        document.getElementById('grupoCrear').value = '';
        document.getElementById('anioCrear').value = '';
        actualizarCamposEspecificacion();
        mostrarToast(`Indicador "${nombre}" creado correctamente`);
        
        // Recargar indicadores desde el backend para asegurar que todo esté sincronizado
        setTimeout(() => cargarIndicadores(), 500);
    }).catch(err => {
        console.error('Error creating indicator:', err);
        mostrarToast(`Error: ${err.message}`, 'error');
    });
}

function actualizarListaIndicadores() {
    const lista = document.getElementById('listaIndicadores');
    const filtroEspec = document.getElementById('filtroEspecificacion')?.value || '';
    
    // Filtrar indicadores según el filtro de especificación
    const indicadoresFiltrados = filtroEspec 
        ? indicadoresExistentes.filter(ind => ind.espec === filtroEspec)
        : indicadoresExistentes;
    
    if (indicadoresFiltrados.length === 0) {
        lista.innerHTML = '<div class="p-4 text-center text-gray-400 text-sm">No hay indicadores disponibles</div>';
        return;
    }
    
    lista.innerHTML = indicadoresFiltrados.map(ind => `
        <div class="p-3 hover:bg-orange-50 cursor-pointer transition-colors flex justify-between items-center" onclick="seleccionarIndicador(${ind.id})">
            <div>
                <span class="font-medium text-gray-800">${ind.nombre}</span>
                <div class="text-xs text-gray-500">${ind.categoria} <span class="text-xs bg-gray-200 px-1 rounded">${ind.espec}</span></div>
            </div>
            <button onclick="event.stopPropagation(); eliminarIndicadorDB(${ind.id})" class="text-red-400 hover:text-red-600 p-1 hover:bg-red-50 rounded transition-colors" title="Eliminar indicador">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
            </button>
        </div>
    `).join('');
}

function actualizarSelectIndicadores() {
    const select = document.getElementById('indicadorSelect');
    const filtroEspec = document.getElementById('filtroEspecificacion')?.value || '';
    select.innerHTML = '<option value="">-- Selecciona un indicador --</option>';
    
    console.log('Actualizando select. Filtro especificación:', filtroEspec);
    console.log('Indicadores disponibles:', indicadoresExistentes);
    
    let contador = 0;
    indicadoresExistentes.forEach(ind => {
        // Filtrar por especificación manual del usuario (si hay)
        if (filtroEspec && ind.espec !== filtroEspec) {
            console.log(`Descartando ${ind.nombre} porque espec (${ind.espec}) != filtro (${filtroEspec})`);
            return;
        }
        console.log(`Añadiendo ${ind.nombre} con espec=${ind.espec}, categoria=${ind.categoria}`);
        const option = new Option(`${ind.nombre} (${ind.categoria || 'Sin categoría'})`, ind.id);
        option.dataset.categoria = ind.categoria;
        option.dataset.espec = ind.espec;
        select.add(option);
        contador++;
    });
    
    console.log('Contador final:', contador);
    
    // Actualizar contador visual
    const contadorEl = document.getElementById('contadorIndicadoresDisponibles');
    if (contadorEl) {
        contadorEl.textContent = contador;
        console.log('Contador actualizado a:', contador);
    }
}

// Seleccionar indicador desde la lista
function seleccionarIndicador(id) {
    document.getElementById('indicadorSelect').value = id;
}

// Filtrar indicadores cuando el usuario cambia el filtro
function filtrarIndicadores() {
    console.log('filtrarIndicadores llamada');
    actualizarSelectIndicadores();
    actualizarListaIndicadores();
}

// Seleccionar indicador desde la lista

// Añadir indicador directo desde la lista
function agregarIndicadorDirecto(id) {
    const indicador = indicadoresExistentes.find(ind => ind.id === id);
    if (indicador) {
        const especVista = obtenerEspecificacionVista();
        if (especVista && indicador.espec !== especVista) {
            mostrarToast(`Solo puedes añadir indicadores con especificación "${especVista}"`, 'error');
            return;
        }
        const cursoId = document.getElementById('cursoSelect').value;
        agregarFilaVista(indicador.nombre, indicador.categoria, indicador.espec, cursoId);
    }
}

// Añadir indicador a la vista desde select
function agregarIndicadorVista() {
    const select = document.getElementById('indicadorSelect');
    const option = select.options[select.selectedIndex];
    
    if (!option.value) {
        mostrarToast('Selecciona un indicador', 'error');
        return;
    }
    
    const indicador = indicadoresExistentes.find(ind => ind.id == option.value);
    
    if (!indicador) {
        mostrarToast('Indicador no encontrado', 'error');
        return;
    }
    
    const cursoId = document.getElementById('cursoSelect').value;
    agregarFilaVista(indicador.nombre, indicador.categoria, indicador.espec, cursoId);
}

function agregarFilaVista(nombre, categoria, espec, cursoId = null) {
    const tbody = document.getElementById('vistaBody');
    
    // Verificar si el indicador ya existe en la tabla
    const filasExistentes = tbody.querySelectorAll('tr:not(#filaVacia)');
    for (let fila of filasExistentes) {
        const nombreEnFila = fila.cells[0].textContent.trim();
        if (nombreEnFila === nombre) {
            mostrarToast(`El indicador "${nombre}" ya está en la tabla`, 'error');
            return;
        }
    }
    
    // Validar que todos los indicadores sean del mismo tipo de especificación
    if (especificacionTabla === null) {
        // Es el primer indicador, establecer el tipo de especificación
        especificacionTabla = espec;
        
        // Obtener y mostrar el año académico si está disponible
        if (cursoId) {
            const select = document.getElementById('cursoSelect');
            const option = select.options[select.selectedIndex];
            annoAcademicoTabla = option.text;
            mostrarAnoAcademico();
        }
    } else if (especificacionTabla !== espec) {
        // El tipo de especificación es diferente
        mostrarToast(`La tabla solo puede contener indicadores de tipo "${especificacionTabla}". No se pueden mezclar con "${espec}"`, 'error');
        return;
    }
    
    const vacia = document.getElementById('filaVacia');
    if (vacia) vacia.remove();
    
    const fila = document.createElement('tr');
    fila.className = 'border-t border-gray-100 hover:bg-orange-50/50 transition-colors fade-in';
    
    let inputValor = '';
    if (categoria === 'Numérico' || categoria === 'Porcentual') {
        inputValor = `<input type="number" step="0.01" class="w-full p-2 border-2 border-gray-200 rounded-lg input-focus text-sm" placeholder="${categoria === 'Porcentual' ? '0-100%' : 'Valor'}">`;
    } else if (categoria === 'Cualitativo') {
        inputValor = `<select class="w-full p-2 border-2 border-gray-200 rounded-lg input-focus text-sm cursor-pointer bg-white">
            <option value="">Seleccionar</option>
            <option value="Excelente">Excelente</option>
            <option value="Bueno">Bueno</option>
            <option value="Regular">Regular</option>
            <option value="Deficiente">Deficiente</option>
        </select>`;
    } else if (categoria === 'Booleano') {
        inputValor = `<select class="w-full p-2 border-2 border-gray-200 rounded-lg input-focus text-sm cursor-pointer bg-white">
            <option value="">Seleccionar</option>
            <option value="Sí">Sí</option>
            <option value="No">No</option>
        </select>`;
    } else {
        inputValor = `<input type="text" class="w-full p-2 border-2 border-gray-200 rounded-lg input-focus text-sm" placeholder="Valor">`;
    }
    
    fila.innerHTML = `
        <td class="p-3 font-medium text-gray-800">${nombre}</td>
        <td class="p-3"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">${categoria}</span></td>
        <td class="p-3 text-gray-600">${espec}</td>
        <td class="p-3">${inputValor}</td>
        <td class="p-3 text-center">
            <button onclick="eliminarFila(this)" class="text-red-400 hover:text-red-600 transition-colors p-1 hover:bg-red-50 rounded" title="Eliminar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </td>
    `;
    tbody.appendChild(fila);
    actualizarContador();
    actualizarAvisoEspecificacion();
    mostrarToast(`"${nombre}" añadido a la vista`);
}

// Mostrar el año académico en la tabla
function mostrarAnoAcademico() {
    const infoDiv = document.getElementById('infoAnoAcademico');
    const badge = document.getElementById('annoAcademicoBadge');
    if (annoAcademicoTabla) {
        badge.textContent = annoAcademicoTabla;
        infoDiv.classList.remove('hidden');
    }
}

// Eliminar fila
function eliminarFila(btn) {
    btn.closest('tr').remove();
    actualizarContador();
    
    const tbody = document.getElementById('vistaBody');
    if (tbody.children.length === 0) {
        tbody.innerHTML = `
            <tr id="filaVacia">
                <td colspan="5" class="p-8 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    No hay indicadores en la vista.<br>
                    <span class="text-sm">Añade indicadores usando el formulario superior.</span>
                </td>
            </tr>
        `;
        // Reiniciar la especificación cuando la tabla queda vacía
        especificacionTabla = null;
        annoAcademicoTabla = null;
        document.getElementById('infoAnoAcademico').classList.add('hidden');
    }
    actualizarAvisoEspecificacion();
}

// Actualizar contador
function actualizarContador() {
    const tbody = document.getElementById('vistaBody');
    const filas = tbody.querySelectorAll('tr:not(#filaVacia)').length;
    document.getElementById('contadorIndicadores').textContent = filas;
}

// Limpiar vista
function limpiarVista() {
    const tbody = document.getElementById('vistaBody');
    tbody.innerHTML = `
        <tr id="filaVacia">
            <td colspan="5" class="p-8 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                No hay indicadores en la vista.<br>
                <span class="text-sm">Añade indicadores usando el formulario superior.</span>
            </td>
        </tr>
    `;
    especificacionTabla = null; // Reiniciar la especificación
    annoAcademicoTabla = null; // Reiniciar el año académico
    document.getElementById('infoAnoAcademico').classList.add('hidden'); // Ocultar la información del año académico
    actualizarContador();
    actualizarAvisoEspecificacion();
    mostrarToast('Vista limpiada');
}

// Guardar vista
function guardarVista() {
    const curso = document.getElementById('cursoSelect').value;
    if (!curso) {
        mostrarToast('Selecciona un curso académico primero', 'error');
        return;
    }
    
    const tbody = document.getElementById('vistaBody');
    const filas = tbody.querySelectorAll('tr:not(#filaVacia)');
    
    if (filas.length === 0) {
        mostrarToast('No hay indicadores para guardar', 'error');
        return;
    }
    
    mostrarToast(`Vista guardada con ${filas.length} indicadores`);
}

function actualizarCamposEspecificacion() {
    const especificacion = document.getElementById('especificacionIndicador').value;
    const camposEspec = document.getElementById('camposEspecificacion');
    
    if (!especificacion) {
        camposEspec.classList.add('hidden');
        return;
    }
    
    camposEspec.classList.remove('hidden');
    
    // Ocultar todos los campos
    document.getElementById('campoCarrera').classList.add('hidden');
    document.getElementById('campoFacultad').classList.add('hidden');
    document.getElementById('campoDepartamento').classList.add('hidden');
    document.getElementById('campoGrupo').classList.add('hidden');
    document.getElementById('campoAnio').classList.add('hidden');
    
    // Mostrar el campo correspondiente
    switch(especificacion) {
        case 'Carrera':
            document.getElementById('campoCarrera').classList.remove('hidden');
            cargarCarreras();
            break;
        case 'Facultad':
            document.getElementById('campoFacultad').classList.remove('hidden');
            cargarFacultades();
            break;
        case 'Departamento':
            document.getElementById('campoDepartamento').classList.remove('hidden');
            cargarDepartamentos();
            break;
        case 'Grupo':
            document.getElementById('campoGrupo').classList.remove('hidden');
            cargarGrupos();
            break;
        case 'Año académico':
            document.getElementById('campoAnio').classList.remove('hidden');
            cargarAnios();
            break;
    }
}

function cargarCarreras() {
    const select = document.getElementById('carreraCrear');
    select.innerHTML = '<option value="">-- Selecciona carrera --</option>';
    fetch(`${API_BASE}/carreras`).then(async res => {
        const data = await res.json();
        (data || []).forEach(c => select.add(new Option(c.nombre, c.id)));
    }).catch(() => {});
}

function cargarFacultades() {
    const select = document.getElementById('facultadCrear');
    select.innerHTML = '<option value="">-- Selecciona facultad --</option>';
    fetch(`${API_BASE}/facultades`).then(async res => {
        const data = await res.json();
        (data || []).forEach(f => select.add(new Option(f.nombre, f.id)));
    }).catch(() => {});
}

function cargarDepartamentos() {
    const select = document.getElementById('departamentoCrear');
    select.innerHTML = '<option value="">-- Selecciona departamento --</option>';
    fetch(`${API_BASE}/departamentos`).then(async res => {
        const data = await res.json();
        (data || []).forEach(d => select.add(new Option(d.nombre, d.id)));
    }).catch(() => {});
}

function cargarGrupos() {
    const select = document.getElementById('grupoCrear');
    select.innerHTML = '<option value="">-- Selecciona grupo --</option>';
    fetch(`${API_BASE}/grupos`).then(async res => {
        const data = await res.json();
        (data || []).forEach(g => select.add(new Option(g.nombre, g.id)));
    }).catch(() => {});
}

function cargarIndicadores() {
    console.log('Iniciando cargarIndicadores...');
    fetch(`${API_BASE}/indicadores`)
        .then(res => {
            console.log('Respuesta recibida, status:', res.status);
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            console.log('Datos parseados:', data);
            console.log('Cantidad de indicadores:', data ? data.length : 0);
            

            if (data && data.error) {
                console.error('Error en el servidor:', data.error);
                mostrarToast(`Error al cargar indicadores: ${data.error}`, 'error');
                return;
            }
            
            indicadoresExistentes = (data || []).map(d => {
                // Obtener nombre de la categoría por id si existe
                const categoria = d.categoria_indicador_id ? (categoriasPorId[d.categoria_indicador_id] || 'Sin categoría') : 'Sin categoría';
                console.log(`Procesando indicador: ${d.nombre} (id: ${d.id}, espec: ${d.especificacion}, categoria: ${categoria})`);
                return {
                    id: d.id, 
                    nombre: d.nombre, 
                    categoria: categoria, 
                    espec: d.especificacion || 'general',
                    categoria_id: d.categoria_indicador_id
                };
            });
            console.log('indicadoresExistentes después de mapear:', indicadoresExistentes);
            contadorId = indicadoresExistentes.length ? Math.max(...indicadoresExistentes.map(i => i.id)) + 1 : 1;
            actualizarListaIndicadores();
            actualizarSelectIndicadores();
            console.log('cargarIndicadores completado');
        })
        .catch(err => {
            console.error('Error cargando indicadores:', err);
            mostrarToast('Error al cargar indicadores. Verifica la consola del navegador.', 'error');
        });
}

function cargarIndicadoresVista() {
    const tbody = document.getElementById('vistaBody');
    const especVista = obtenerEspecificacionVista();
    const cursoId = document.getElementById('cursoSelect').value;
    
    if (!especVista || !cursoId) return;
    
   
}

function cargarAnios() {
    const selectCrear = document.getElementById('anioCrear');
    const selectIndicador = document.getElementById('anioIndicador');
    
    fetch(`${API_BASE}/anios-academicos`).then(async res => {
        const data = await res.json();
        (data || []).forEach(a => {
            if (selectCrear) selectCrear.add(new Option(a.nombre || `Año ${a.id}`, a.id));
            if (selectIndicador) selectIndicador.add(new Option(a.nombre || `Año ${a.id}`, a.id));
        });
    }).catch(() => {});
}

function cargarCategorias() {
    const select = document.getElementById('categoriaIndicador');
    return fetch(`${API_BASE}/categorias-indicador`).then(async res => {
        const data = await res.json();
        (data || []).forEach(cat => {
            categoriasIndicador[cat.nombre] = cat.id;
            categoriasPorId[cat.id] = cat.nombre;
            select.add(new Option(cat.nombre, cat.id));
        });
    }).catch(err => {
        console.error('Error cargando categorías:', err);
    });
}

function cargarCursos() {
    const select = document.getElementById('cursoSelect');
    fetch(`${API_BASE}/cursos-academicos`).then(async res => {
        const data = await res.json();
        (data || []).forEach(c => select.add(new Option(c.nombre || `${c.id}`, c.id)));
    }).catch(() => {});
}

function obtenerEspecificacionVista() {
    const tbody = document.getElementById('vistaBody');
    const filas = tbody.querySelectorAll('tr:not(#filaVacia)');
    
    if (filas.length === 0) return null;
    
    const primeraCelda = filas[0].querySelectorAll('td')[2];
    return primeraCelda ? primeraCelda.textContent.trim() : null;
}



function exportarCSV() {
    const tbody = document.getElementById('vistaBody');
    const filas = tbody.querySelectorAll('tr:not(#filaVacia)');
    
    if (filas.length === 0) {
        mostrarToast('No hay datos para exportar', 'error');
        return;
    }
    
    let csv = 'Indicador,Categoría,Especificación,Valor\n';
    
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        const nombre = celdas[0].textContent.trim();
        const categoria = celdas[1].textContent.trim();
        const espec = celdas[2].textContent.trim();
        const valorInput = celdas[3].querySelector('input, select');
        const valor = valorInput ? valorInput.value : '';
        csv += `"${nombre}","${categoria}","${espec}","${valor}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'indicadores.csv';
    link.click();
    
    mostrarToast('CSV exportado correctamente');
}

// Cerrar modales con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalCategoria();
    }
});

// Cerrar modal al hacer clic fuera
document.getElementById('modalCategoria').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalCategoria();
});

function eliminarIndicadorDB(id) {
    const indicador = indicadoresExistentes.find(ind => ind.id === id);
    if (!indicador) return;
    if (!confirm(`¿Estás seguro de eliminar "${indicador.nombre}" de la base de datos?`)) return;
    fetch(`${API_BASE}/indicadores/${id}`, { method: 'DELETE' }).then(res => {
        if (!res.ok) throw new Error('Error al eliminar');
        indicadoresExistentes = indicadoresExistentes.filter(ind => ind.id !== id);
        actualizarListaIndicadores();
        actualizarSelectIndicadores();
        mostrarToast(`"${indicador.nombre}" eliminado de la base de datos`);
    }).catch(() => mostrarToast('No se pudo eliminar el indicador', 'error'));
}
</script>

</body>
</html>


<?php /**PATH D:\Ing Informatica\III AÑO\I Semestre\bdw\backend\resources\views/indicadores.blade.php ENDPATH**/ ?>