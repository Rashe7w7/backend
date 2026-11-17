<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Indicadores | Sistema de Gestión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
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
<body class="academic-ui bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">

<?php echo $__env->make('partials.academic-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="max-w-6xl mx-auto px-6 pt-8"><p class="eyebrow">Seguimiento del semestre</p><h1 class="text-3xl font-semibold mb-2">Indicadores académicos</h1><p class="text-gray-500 text-sm">Organiza tus indicadores y consulta los resultados de cada período.</p></div>

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
                    <select id="especificacionIndicador" class="w-full p-3 border-2 border-gray-200 rounded-lg input-focus hover:border-primary transition-colors cursor-pointer bg-white">
                        <option value="">-- Selecciona especificación --</option>
                        <option value="Carrera">Carrera</option>
                        <option value="Facultad">Facultad</option>
                        <option value="Departamento">Departamento</option>
                        <option value="Grupo">Grupo</option>
                        <option value="Nivel académico">Nivel académico</option>
                    </select>
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
                        <option value="nivel_academico">Nivel académico</option>
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
        
        <div id="infoAnoAcademico" class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden space-y-3">
            <p class="text-sm text-blue-700"><span class="font-semibold">Período Académico:</span> <span id="annoAcademicoBadge" class="font-medium"></span></p>
            <div>
                <label id="instanciaLabel" for="instanciaSelect" class="block text-sm font-medium text-gray-700 mb-1">Instancia</label>
                <select id="instanciaSelect" class="w-full p-2 border border-gray-200 rounded-lg text-sm input-focus bg-white">
                    <option value="">-- Selecciona una instancia --</option>
                </select>
            </div>
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
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const jsonHeaders = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': csrfToken,
};
let indicadoresExistentes = [];
let contadorId = 1;
let categoriasIndicador = {};
let categoriasPorId = {};
let especificacionTabla = null; // Controla el tipo de especificación permitida en la tabla
let categoriaTabla = null;
let annoAcademicoTabla = null; // Almacena el año académico de la tabla

document.addEventListener('DOMContentLoaded', function() {
    cargarCursos();
    // Cargar categorías y luego indicadores para que los nombres de categoría estén listos
    cargarCategorias().then(() => {
        cargarIndicadores();
    });
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
        headers: jsonHeaders,
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
    
    const especificaciones = {
        'Carrera': 'carrera',
        'Facultad': 'facultad',
        'Departamento': 'departamento',
        'Grupo': 'grupo',
        'Nivel académico': 'nivel_academico',
    };
    const especificacionKey = especificaciones[especificacion];
    
    const payload = { 
        nombre, 
        categoria_indicador_id: categoriaId,
        especificacion: especificacionKey
    };

    fetch(`${API_BASE}/indicadores`, {
        method: 'POST',
        headers: jsonHeaders,
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
        indicadoresExistentes.push({
            id: data.id,
            nombre: data.nombre,
            categoria: categoriaNombre,
            categoria_id: data.categoria_indicador_id,
            espec: data.especificacion,
        });
        contadorId = Math.max(contadorId, data.id + 1);
        actualizarListaIndicadores();
        actualizarSelectIndicadores();
        document.getElementById('nombreIndicador').value = '';
        document.getElementById('categoriaIndicador').value = '';
        document.getElementById('especificacionIndicador').value = '';
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
        agregarFilaVista(indicador);
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
    
    agregarFilaVista(indicador);
}

function agregarFilaVista(indicador) {
    const tbody = document.getElementById('vistaBody');
    const cursoSelect = document.getElementById('cursoSelect');
    const cursoId = cursoSelect.value;

    if (!cursoId) {
        mostrarToast('Selecciona un período académico antes de formar la vista', 'error');
        return;
    }
    
    // Verificar si el indicador ya existe en la tabla
    const filasExistentes = tbody.querySelectorAll('tr:not(#filaVacia)');
    for (let fila of filasExistentes) {
        const nombreEnFila = fila.cells[0].textContent.trim();
        if (nombreEnFila === indicador.nombre) {
            mostrarToast(`El indicador "${indicador.nombre}" ya está en la tabla`, 'error');
            return;
        }
    }
    
    // Validar que todos los indicadores sean del mismo tipo de especificación
    if (especificacionTabla === null) {
        especificacionTabla = indicador.espec;
        categoriaTabla = indicador.categoria_id;
        annoAcademicoTabla = cursoSelect.options[cursoSelect.selectedIndex].text;
        cursoSelect.disabled = true;
        mostrarContextoVista();
    } else if (especificacionTabla !== indicador.espec) {
        mostrarToast(`La tabla solo puede contener indicadores con especificación "${etiquetaEspecificacion(especificacionTabla)}"`, 'error');
        return;
    } else if (categoriaTabla !== indicador.categoria_id) {
        mostrarToast(`La tabla solo puede contener indicadores del tipo de valor "${indicadoresExistentes.find(item => item.categoria_id === categoriaTabla)?.categoria || ''}"`, 'error');
        return;
    }
    
    const vacia = document.getElementById('filaVacia');
    if (vacia) vacia.remove();
    
    const fila = document.createElement('tr');
    fila.className = 'border-t border-gray-100 hover:bg-orange-50/50 transition-colors fade-in';
    
    let inputValor = '';
    if (indicador.categoria === 'Numérico' || indicador.categoria === 'Porcentual') {
        inputValor = `<input type="number" step="0.01" class="w-full p-2 border-2 border-gray-200 rounded-lg input-focus text-sm" placeholder="${indicador.categoria === 'Porcentual' ? '0-100%' : 'Valor'}">`;
    } else if (indicador.categoria === 'Cualitativo') {
        inputValor = `<select class="w-full p-2 border-2 border-gray-200 rounded-lg input-focus text-sm cursor-pointer bg-white">
            <option value="">Seleccionar</option>
            <option value="Excelente">Excelente</option>
            <option value="Bueno">Bueno</option>
            <option value="Regular">Regular</option>
            <option value="Deficiente">Deficiente</option>
        </select>`;
    } else if (indicador.categoria === 'Booleano') {
        inputValor = `<select class="w-full p-2 border-2 border-gray-200 rounded-lg input-focus text-sm cursor-pointer bg-white">
            <option value="">Seleccionar</option>
            <option value="Sí">Sí</option>
            <option value="No">No</option>
        </select>`;
    } else {
        inputValor = `<input type="text" class="w-full p-2 border-2 border-gray-200 rounded-lg input-focus text-sm" placeholder="Valor">`;
    }
    
    fila.innerHTML = `
        <td class="p-3 font-medium text-gray-800">${escaparHtml(indicador.nombre)}</td>
        <td class="p-3"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">${escaparHtml(indicador.categoria)}</span></td>
        <td class="p-3 text-gray-600">${escaparHtml(etiquetaEspecificacion(indicador.espec))}</td>
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
    mostrarToast(`"${indicador.nombre}" añadido a la vista`);
}

function mostrarContextoVista() {
    const infoDiv = document.getElementById('infoAnoAcademico');
    const badge = document.getElementById('annoAcademicoBadge');
    badge.textContent = annoAcademicoTabla;
    infoDiv.classList.remove('hidden');
    cargarInstancias(especificacionTabla);
}

function cargarInstancias(especificacion) {
    const configuracion = {
        carrera: { etiqueta: 'Carrera', endpoint: 'carreras' },
        facultad: { etiqueta: 'Facultad', endpoint: 'facultades' },
        departamento: { etiqueta: 'Departamento', endpoint: 'departamentos' },
        grupo: { etiqueta: 'Grupo', endpoint: 'grupos' },
        nivel_academico: { etiqueta: 'Nivel académico', endpoint: 'niveles-academicos' },
    }[especificacion];
    const select = document.getElementById('instanciaSelect');

    document.getElementById('instanciaLabel').textContent = configuracion.etiqueta;
    select.innerHTML = `<option value="">-- Selecciona ${configuracion.etiqueta.toLowerCase()} --</option>`;
    select.disabled = true;

    fetch(`${API_BASE}/${configuracion.endpoint}`)
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'No se pudieron cargar las instancias');
            }

            data.forEach(item => {
                const label = item.contexto ? `${item.nombre} — ${item.contexto}` : item.nombre;
                select.add(new Option(label, item.id));
            });
            select.disabled = false;
        })
        .catch(error => mostrarToast(error.message, 'error'));
}

function etiquetaEspecificacion(especificacion) {
    return {
        carrera: 'Carrera',
        facultad: 'Facultad',
        departamento: 'Departamento',
        grupo: 'Grupo',
        nivel_academico: 'Nivel académico',
    }[especificacion] || especificacion;
}

function escaparHtml(valor) {
    const elemento = document.createElement('div');
    elemento.textContent = valor;

    return elemento.innerHTML;
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
        categoriaTabla = null;
        annoAcademicoTabla = null;
        document.getElementById('infoAnoAcademico').classList.add('hidden');
        document.getElementById('instanciaSelect').innerHTML = '<option value="">-- Selecciona una instancia --</option>';
        document.getElementById('cursoSelect').disabled = false;
    }
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
    categoriaTabla = null;
    annoAcademicoTabla = null; // Reiniciar el año académico
    document.getElementById('infoAnoAcademico').classList.add('hidden'); // Ocultar la información del año académico
    document.getElementById('instanciaSelect').innerHTML = '<option value="">-- Selecciona una instancia --</option>';
    document.getElementById('cursoSelect').disabled = false;
    actualizarContador();
    mostrarToast('Vista limpiada');
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

function exportarCSV() {
    const tbody = document.getElementById('vistaBody');
    const filas = tbody.querySelectorAll('tr:not(#filaVacia)');
    
    if (filas.length === 0) {
        mostrarToast('No hay datos para exportar', 'error');
        return;
    }

    const instanciaSelect = document.getElementById('instanciaSelect');
    if (!instanciaSelect.value) {
        mostrarToast('Selecciona una instancia antes de exportar', 'error');
        return;
    }

    const periodo = annoAcademicoTabla;
    const instancia = instanciaSelect.options[instanciaSelect.selectedIndex].text;
    const csvValue = value => `"${String(value).replaceAll('"', '""')}"`;
    let csv = 'Período,Instancia,Indicador,Categoría,Especificación,Valor\n';
    
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        const nombre = celdas[0].textContent.trim();
        const categoria = celdas[1].textContent.trim();
        const espec = celdas[2].textContent.trim();
        const valorInput = celdas[3].querySelector('input, select');
        const valor = valorInput ? valorInput.value : '';
        csv += [periodo, instancia, nombre, categoria, espec, valor].map(csvValue).join(',') + '\n';
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
    fetch(`${API_BASE}/indicadores/${id}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
    }).then(res => {
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



<?php /**PATH D:\Trabajo\rachel1\backend\resources\views\indicadores.blade.php ENDPATH**/ ?>