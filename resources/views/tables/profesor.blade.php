<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Profesores</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .list-item { display: flex; justify-content: space-between; margin: 0.3rem 0; }
        .list-item button { cursor: pointer; }
        .modal {
            display: none;
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center; align-items: center;
        }
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            width: 400px;
            max-width: 90%;
        }
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .saved-table {
            margin-top: 2rem;
            border-collapse: collapse;
            width: 100%;
        }
        .saved-table th, .saved-table td {
            border: 1px solid #ccc;
            padding: 0.5rem;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <h2>Sistema PPA</h2>
        <nav>
            <a href="{{ route('roles') }}">📁 Gestión de roles</a>
            <a href="{{ route('alumnoayudante') }}">📁 Alumno Ayudante</a>
            <a class="active" href="{{ route('profesor') }}">📁 Profesor</a>
        </nav>
    </aside>
    <main class="content">
        <h1>Gestión de Profesores</h1>
        <p class="subtitle">Administra la información de profesores y asignaciones</p>
        
        <input type="text" id="searchInput" placeholder="Ej: Juan García López" class="search-input">
        <div class="list" id="list"></div>
        
        <h2>Profesores Registrados</h2>
        <table class="saved-table" id="profesorTable">
            <thead>
                <tr>
                    <th>Profesor</th>
                    <th>Departamento</th>
                    <th>Categoría</th>
                    <th>Asignaturas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dinámico -->
            </tbody>
        </table>
    </main>
</div>

<!-- Modal -->
<div class="modal" id="modal">
    <div class="modal-content">
        <h3>Gestionar Profesor</h3>
        <p id="seleccionado" class="subtitle"></p>
        
        <label>
            Departamento
            <select id="departamento"><option value="">Seleccione</option></select>
        </label>
        
        <label>
            Categoría
            <select id="categoria"><option value="">Seleccione</option></select>
        </label>
        
        <label>
            Asignaturas
            <select id="asignatura" multiple><option value="">Seleccione</option></select>
        </label>
        
        <div class="modal-actions">
            <button class="btn cancel" onclick="cerrarModal()">Cancelar</button>
            <button class="btn secundario" id="btnProfesor" disabled>Guardar</button>
        </div>
    </div>
</div>

<script>
// JavaScript completo del sistema de gestión de profesores
const profesores = [
    "Juan García López",
    "María Rodríguez Martín", 
    "Pedro Sánchez González",
    "Rosa Fernández Díaz"
];

let selectedProfesor = null;
let profesorList = [];

const listDiv = document.getElementById('list');
const searchInput = document.getElementById('searchInput');
const modal = document.getElementById('modal');
const seleccionadoP = document.getElementById('seleccionado');
const profesorTableBody = document.querySelector('#profesorTable tbody');

function renderList(filtered) {
    listDiv.innerHTML = "";
    if (filtered.length === 0) {
        listDiv.innerHTML = "<p>No se encontraron coincidencias.</p>";
        return;
    }
    filtered.forEach(nombre => {
        const div = document.createElement('div');
        div.className = 'list-item';
        div.innerHTML = `
            <span>${nombre}</span>
            <button onclick="abrirModal('${nombre}')">Seleccionar</button>
        `;
        listDiv.appendChild(div);
    });
}

searchInput.addEventListener('input', () => {
    const value = searchInput.value.trim();
    const palabras = value.split(' ');
    if (palabras.length >= 3) {
        const filtered = profesores.filter(e => e.toLowerCase().includes(value.toLowerCase()));
        renderList(filtered);
    } else {
        listDiv.innerHTML = "<p>Debe escribir Nombre y 2 Apellidos.</p>";
    }
});

function abrirModal(nombre) {
    selectedProfesor = nombre;
    modal.style.display = 'flex';
    seleccionadoP.textContent = 'Profesor seleccionado: ' + nombre;
}

function cerrarModal() {
    modal.style.display = 'none';
    document.querySelectorAll('#departamento, #categoria, #asignatura').forEach(s => s.value = "");
    btn.disabled = true;
    btn.style.opacity = '0.5';
}

const selects = document.querySelectorAll('#departamento, #categoria, #asignatura');
const btn = document.getElementById('btnProfesor');
btn.disabled = true;
btn.style.opacity = '0.5';

selects.forEach(select => select.addEventListener('change', validar));

function validar() {
    const todosLlenos = [...selects].every(s => s.value !== "");
    btn.disabled = !todosLlenos;
    btn.style.opacity = todosLlenos ? '1' : '0.5';
}

btn.addEventListener('click', () => {
    if (!selectedProfesor) return;

    const record = {
        profesor: selectedProfesor,
        departamento: document.getElementById('departamento').value,
        categoria: document.getElementById('categoria').value,
        asignatura: document.getElementById('asignatura').value
    };

    profesorList.push(record);
    renderProfesorTable();
    cerrarModal();
});

function renderProfesorTable() {
    profesorTableBody.innerHTML = "";
    profesorList.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${r.profesor}</td>
            <td>${r.departamento}</td>
            <td>${r.categoria}</td>
            <td>${r.asignatura}</td>
            <td><button onclick="alert('Editar profesor')">Editar</button></td>
        `;
        profesorTableBody.appendChild(tr);
    });
}
</script>
</body>
</html>
