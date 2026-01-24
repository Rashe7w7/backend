<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Solicitud de Modificación - Sistema Académico</title>
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --color-primario: #f97316;
            --color-primario-oscuro: #ea580c;
            --color-primario-claro: #fb923c;
            
            --color-secundario: #E8DED5;
            --color-terciario: #f97316;
            
            --color-fondo: #FAF7F4;
            --color-blanco: #FFFFFF;
            --color-texto: #3A3A3A;
            --color-texto-secundario: #6B6B6B;
            
            --color-borde: #E5DED6;
            --color-borde-oscuro: #CFC4B8;
            
            --color-exito: #6B8E6B;
            --color-advertencia: #D4A651;
            --color-error: #B5655C;
            --color-estado-borrador: #9B9B9B;
            --color-estado-enviada: #7D9CB8;
            --color-estado-revision: #D4A651;
            --color-estado-devuelta: #B5655C;
            --color-estado-rechazada: #8B4545;
            --color-estado-aprobada: #6B8E6B;
            --espacio-xs: 0.5rem;
            --espacio-sm: 1rem;
            --espacio-md: 1.5rem;
            --espacio-lg: 2rem;
            --espacio-xl: 3rem;
            --espacio-xxl: 4rem;
            --radio-borde: 8px;
            --radio-borde-sm: 4px;
            --radio-borde-lg: 12px;
            --sombra-sm: 0 2px 4px rgba(0, 0, 0, 0.06);
            --sombra-md: 0 4px 8px rgba(0, 0, 0, 0.08);
            --sombra-lg: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--color-fondo);
            color: var(--color-texto);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        /* =========================================
           HEADER DEL SISTEMA
           ========================================= */
        header {
            color: var(--color-blanco);
            box-shadow: var(--sombra-md);
        }

        header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        header p {
            margin: var(--espacio-xs) 0 0;
            font-size: 1rem;
            opacity: 0.95;
            font-weight: 300;
        }

     
        nav {
            color: var(--color-texto-secundario);
        }

        nav span {
            color: var(--color-primario);
            margin: 0 var(--espacio-xs);
        }

        /* =========================================
           SECCIONES
           ========================================= */
        section {
            margin-bottom: var(--espacio-lg);
        }

        section h2 {
            font-size: 1.875rem;
            color: var(--color-primario);
            margin-bottom: var(--espacio-sm);
            font-weight: 600;
        }

        section > p {
            font-size: 1.05rem;
            color: var(--color-texto-secundario);
            max-width: 800px;
        }

        section h3 {
            font-size: 1.75rem;
            color: var(--color-primario-oscuro);
            margin: var(--espacio-xxl) 0 var(--espacio-lg);
            font-weight: 600;
        }

        /* =========================================
           TARJETAS
           ========================================= */
        .bg-white {
            background: var(--color-blanco);
            border-radius: var(--radio-borde);
        }

        .rounded-xl {
            border-radius: var(--radio-borde-lg);
        }

        .shadow-sm {
            box-shadow: var(--sombra-sm);
        }

        .border {
            border: 1px solid var(--color-borde);
        }

        /* =========================================
           FORMULARIOS
           ========================================= */
        label {
            display: block;
            font-weight: 600;
        }

        select, input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--color-borde);
            border-radius: var(--radio-borde-sm);
            font-size: 1rem;
            font-family: inherit;
            transition: var(--transicion);
            background-color: var(--color-blanco);
        }

        select:focus, input:focus {
            outline: none;
            border-color: var(--color-primario);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        select:hover, input:hover {
            border-color: var(--color-primario-claro);
        }

        .input-focus:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.3);
        }

        /* =========================================
           TABLAS
           ========================================= */
        .tabla-wrapper {
            overflow-x: auto;
            border-radius: var(--radio-borde-sm);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--color-blanco);
        }

        table thead {
            background-color: var(--color-primario);
            color: var(--color-blanco);
        }

        table thead th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--color-borde);
            color: var(--color-texto);
        }

        table tbody tr:hover {
            background-color: rgba(249, 115, 22, 0.03);
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        .col-acciones {
            width: 120px;
            text-align: center;
        }

        .texto-centrado {
            text-align: center;
        }

        /* =========================================
           ESTADOS Y BADGES
           ========================================= */
        .inline-block {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--color-blanco);
        }

        /* =========================================
           BOTONES
           ========================================= */
        .btn {
            padding: 0.75rem 1.75rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: var(--radio-borde-sm);
            align-items: center;
            gap: var(--espacio-xs);
            text-decoration: none;
        }

        .btn-primario {
            background-color: var(--color-primario);
            color: var(--color-blanco);
            box-shadow: var(--sombra-sm);
        }

        .btn-primario:hover {
            background-color: var(--color-primario-oscuro);
            box-shadow: var(--sombra-md);
            transform: translateY(-2px);
        }

        .btn-secundario {
            background-color: var(--color-borde);
            color: var(--color-texto);
        }

        .btn-secundario:hover {
            background-color: var(--color-borde-oscuro);
        }

        .btn-terciario {
            background-color: var(--color-terciario);
            color: var(--color-blanco);
        }

        .btn-terciario:hover {
            background-color: var(--color-primario-oscuro);
            transform: translateY(-2px);
        }

        /* Botones de colores estándar */
        .btn-success {
            background-color: #10b981;
            color: var(--color-blanco);
            box-shadow: var(--sombra-sm);
        }

        .btn-success:hover {
            background-color: #059669;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: #ef4444;
            color: var(--color-blanco);
            box-shadow: var(--sombra-sm);
        }

        .btn-danger:hover {
            background-color: #dc2626;
            box-shadow: var(--sombra-md);
            transform: translateY(-2px);
        }

        .btn-warning {
            background-color: #f59e0b;
            color: var(--color-blanco);
            box-shadow: var(--sombra-sm);
        }

        .btn-warning:hover {
            background-color: #d97706;
            box-shadow: var(--sombra-md);
            transform: translateY(-2px);
        }

        .btn-info {
            background-color: #3b82f6;
            color: var(--color-blanco);
            box-shadow: var(--sombra-sm);
        }

        .btn-info:hover {
            background-color: #1d4ed8;
            box-shadow: var(--sombra-md);
            transform: translateY(-2px);
        }

        .btn-grande {
            padding: 1rem 2.5rem;
            font-size: 1.05rem;
        }

        /* Botones pequeños de iconos */
        .btn-icono-pequeno {
            background: transparent;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            transition: var(--transicion);
            margin: 0 0.25rem;
            text-decoration: none;
        }

        .btn-icono-pequeno:hover {
            transform: scale(1.2);
        }

        .grid {
            display: grid;
            gap: var(--espacio-md);
        }

        .grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .flex {
            display: flex;
        }

        .flex-col {
            flex-direction: column;
        }

        .justify-between {
            justify-content: space-between;
        }

        .justify-center {
            justify-content: center;
        }

        .items-center {
            align-items: center;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-4 {
            gap: 1rem;
        }

        .gap-6 {
            gap: 1.5rem;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .p-6 {
            padding: 1.5rem;
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .pb-4 {
            padding-bottom: 1rem;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .rounded-xl {
            border-radius: var(--radio-borde-lg);
        }

        .shadow-sm {
            box-shadow: var(--sombra-sm);
        }

        .border {
            border: 1px solid var(--color-borde);
        }

        .border-b-2 {
            border-bottom: 2px solid;
        }

        .bg-white {
            background: var(--color-blanco);
        }

        .bg-gray-50 {
            background-color: var(--color-fondo);
        }

        .subtitulo-seccion {
            font-size: 1rem;
            color: var(--color-texto-secundario);
        }

        /* =========================================
           FLUJO DE ESTADOS
           ========================================= */
        .flujo-estados {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: var(--espacio-xl) 0;
            overflow-x: auto;
        }

        .estado-paso {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--espacio-sm);
            flex: 1;
            min-width: 100px;
        }

        .estado-icono {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--color-borde);
            color: var(--color-texto-secundario);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.3rem;
            transition: var(--transicion);
        }

        .estado-paso.activo .estado-icono {
            background-color: var(--color-primario);
            color: var(--color-blanco);
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.2);
            transform: scale(1.1);
        }

        .estado-nombre {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--color-texto);
        }

        .estado-paso.activo .estado-nombre {
            color: var(--color-primario);
        }

        .estado-linea {
            flex: 1;
            height: 3px;
            background-color: var(--color-borde);
            margin: 0 var(--espacio-xs);
            align-self: flex-start;
            margin-top: 25px;
            min-width: 20px;
        }

        /* =========================================
           MODAL DE CONFIRMACIÓN
           ========================================= */
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            background-color: rgba(0, 0, 0, 0.6);
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.flex {
            display: flex;
        }

        .modal-eliminar {
            background-color: var(--color-blanco);
            border-radius: var(--radio-borde-lg);
            box-shadow: var(--sombra-lg);
            max-width: 500px;
            width: 90%;
            border-top: 5px solid var(--color-error);
        }

        .modal-header-eliminar {
            padding: var(--espacio-lg);
            text-align: center;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1) 0%, rgba(249, 115, 22, 0.05) 100%);
            border-bottom: 2px solid var(--color-fondo);
        }

        .modal-header-eliminar h3 {
            color: var(--color-primario);
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .modal-body-eliminar {
            padding: var(--espacio-xl) var(--espacio-lg);
            text-align: center;
        }

        .modal-footer-eliminar {
            padding: var(--espacio-md) var(--espacio-lg) var(--espacio-lg);
            display: flex;
            justify-content: center;
            gap: var(--espacio-md);
        }

        .btn-eliminar {
            background-color: var(--color-error);
            color: var(--color-blanco);
            padding: 0.85rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: var(--radio-borde-sm);
            cursor: pointer;
            transition: var(--transicion);
        }

        .btn-eliminar:hover {
            background-color: #9A5148;
            transform: translateY(-2px);
        }

        /* =========================================
           FOOTER
           ========================================= */
        footer {
            background-color: var(--color-texto);
            color: var(--color-blanco);
            padding: var(--espacio-lg) 0;
            text-align: center;
        }

        footer p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* =========================================
           RESPONSIVE
           ========================================= */
        @media (max-width: 768px) {
            section h2 {
                font-size: 1.75rem;
            }

            section h3 {
                font-size: 1.4rem;
            }

            .grid-3 {
                grid-template-columns: 1fr;
            }

            .btn-grande {
                width: 100%;
                justify-content: center;
            }

            .flujo-estados {
                flex-wrap: wrap;
                gap: var(--espacio-lg);
            }

            .estado-linea {
                display: none;
            }
        }

        @media (max-width: 480px) {
            section h2 {
                font-size: 1.5rem;
            }

            .modal-footer-eliminar {
                flex-direction: column-reverse;
            }

            .modal-footer-eliminar .btn {
                width: 100%;
            }
        }

        [x-cloak] { display: none !important; }
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    
    <!-- ====================================
         ENCABEZADO PRINCIPAL DEL SISTEMA
         ==================================== -->
    <header style="background: linear-gradient(135deg, var(--color-primario) 0%, var(--color-primario-oscuro) 100%); color: var(--color-blanco); box-shadow: var(--sombra-md); position: sticky; top: 0; z-index: 50;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
            <div style="padding: 2rem 0;">
                <h1 style="font-size: 2rem; font-weight: 600; margin: 0; letter-spacing: -0.5px;">Planes de Estudio</h1>
            </div>
        </div>
    </header>

    <!-- ====================================
         CONTENEDOR PRINCIPAL
         ==================================== -->
    <main style="max-width: 1400px; margin: 0 auto; padding: 1.5rem; flex: 1;">

            <!-- Breadcrumb / Navegación -->
            <nav style="padding: 1.5rem 0; font-size: 0.9rem; color: var(--color-texto-secundario); display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
                <span>Inicio</span>
                <span style="color: var(--color-primario);">›</span>
                <span>Planes de Estudio</span>
                <span style="color: var(--color-primario);">›</span>
                <span style="font-weight: 600; color: var(--color-primario);">Crear Solicitud de Modificación</span>
            </nav>

            <!-- ====================================
                 ENCABEZADO DE LA SOLICITUD
                 ==================================== -->
            <section style="margin-bottom: 2rem;">
                <div style="margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.875rem; color: var(--color-primario); margin: 0 0 0.5rem; font-weight: 600;">Seleccionar Plan de Estudio</h2>
                    <p style="font-size: 1.05rem; color: var(--color-texto-secundario); max-width: 800px; margin: 0;">
                        Selecciona una carrera y el plan de estudio para ver su estructura curricular
                    </p>
                </div>

                <form id="formCrearPlan" class="bg-white p-6 rounded-xl shadow-sm border" style="background: var(--color-blanco); padding: 1.5rem; border-radius: var(--radio-borde-lg); box-shadow: var(--sombra-sm); border: 1px solid var(--color-borde);">
                    <!-- ID (Autogenerado - Oculto) -->
                    <input type="hidden" id="id" name="id">

                    <!-- Carrera -->
                    <div style="margin-bottom: 1.5rem;">
                        <label for="carrera" style="display: block; font-weight: 600; color: var(--color-texto); margin-bottom: 0.5rem; font-size: 0.95rem;">
                            Carrera <span style="color: red;">*</span>
                        </label>
                        <select id="carrera" name="carrera" required style="width: 100%; padding: 0.75rem 1rem; border: 2px solid var(--color-borde); border-radius: var(--radio-borde-sm); font-size: 1rem; font-family: inherit; transition: var(--transicion); background-color: var(--color-blanco);">
                            <option value="" disabled selected>Seleccione una carrera...</option>
                        </select>
                        <p style="font-size: 0.75rem; color: var(--color-texto-secundario); margin-top: 0.5rem;">Seleccione la carrera para ver sus planes de estudio</p>
                    </div>

                </form>
            </section>


            <!-- ====================================
                 SECCIÓN: PLANES DE ESTUDIO
                 ==================================== -->
            <section style="margin-bottom: 2rem;">
                <div class="bg-white p-6 rounded-xl shadow-sm border mb-6" style="background: var(--color-blanco); padding: 1.5rem; border-radius: var(--radio-borde-lg); box-shadow: var(--sombra-sm); border: 1px solid var(--color-borde); margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.3rem; color: var(--color-primario-oscuro); font-weight: 600; margin: 0 0 1rem 0;">Planes de Estudio de la Carrera</h3>
                    <div id="contenedorPlanes" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                        <!-- Se llenará dinámicamente con los planes de la carrera seleccionada -->
                        <div style="padding: var(--espacio-xxl) var(--espacio-lg); text-align: center; grid-column: 1 / -1;">
                            <span style="font-size: 3rem; display: block; margin-bottom: var(--espacio-md); opacity: 0.5;">📋</span>
                            <p style="font-size: 1.1rem; color: var(--color-texto-secundario); margin-bottom: var(--espacio-xs);">Seleccione una carrera para ver sus planes de estudio</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ====================================
                 SECCIÓN: ESTRUCTURA CURRICULAR
                 ==================================== -->
            <section>
                <h3>Estructura Curricular</h3>
                <p class="subtitulo-seccion">Gestione las disciplinas y asignaturas del plan de estudio</p>

                <!-- ==================
                     DISCIPLINAS
                     ================== -->
                <div class="bg-white p-6 rounded-xl shadow-sm border mb-6" style="background: var(--color-blanco); padding: 1.5rem; border-radius: var(--radio-borde-lg); box-shadow: var(--sombra-sm); border: 1px solid var(--color-borde); margin-bottom: 1.5rem;">
                    <div class="flex justify-between items-center mb-4 pb-4 border-b-2" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 2px solid var(--color-fondo);">
                        <h4 style="font-size: 1.3rem; color: var(--color-primario-oscuro); font-weight: 600; margin: 0;">Gestión de Disciplinas por Carrera</h4>
                        <div class="flex gap-2" style="display: flex; gap: 0.5rem;">
                            <button onclick="abrirModalAgregarDisciplina()" style="padding: 0.75rem 1.75rem; font-size: 1rem; font-weight: 600; border: none; border-radius: var(--radio-borde-sm); cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem; background-color: #f97316; color: white; box-shadow: var(--sombra-sm);" onmouseover="this.style.backgroundColor='#ea580c'; this.style.boxShadow='var(--sombra-md)';" onmouseout="this.style.backgroundColor='#f97316'; this.style.boxShadow='var(--sombra-sm)';">
                                <span>+</span>
                                Agregar Disciplina
                            </button>
                        </div>
                    </div>
                    <div>
                        <!-- Contenedor de Disciplinas por Carrera -->
                        <div id="contenedorDisciplinas">
                            <!-- Se llenará dinámicamente con la estructura por carrera -->
                            <div style="padding: var(--espacio-xxl) var(--espacio-lg); text-align: center;">
                                <span style="font-size: 3rem; display: block; margin-bottom: var(--espacio-md); opacity: 0.5;">📚</span>
                                <p style="font-size: 1.1rem; color: var(--color-texto-secundario); margin-bottom: var(--espacio-xs);">No hay disciplinas agregadas</p>
                                <small style="font-size: 0.9rem; color: var(--color-texto-secundario); opacity: 0.7;">Haga clic en "Agregar Disciplina" para comenzar</small>
                            </div>
                        </div>
                    </div>
                </div>

              
                <div class="bg-white p-6 rounded-xl shadow-sm border mb-6" style="background: var(--color-blanco); padding: 1.5rem; border-radius: var(--radio-borde-lg); box-shadow: var(--sombra-sm); border: 1px solid var(--color-borde); margin-bottom: 1.5rem;">
                    <div class="flex justify-between items-center mb-4 pb-4 border-b-2" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 2px solid var(--color-fondo);">
                        <h4 style="font-size: 1.3rem; color: var(--color-primario-oscuro); font-weight: 600; margin: 0;">Gestión de Asignaturas</h4>
                        <div class="flex gap-2" style="display: flex; gap: 0.5rem;">
                            <button onclick="abrirModalAgregarAsignatura()" style="padding: 0.75rem 1.75rem; font-size: 1rem; font-weight: 600; border: none; border-radius: var(--radio-borde-sm); cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 0.5rem; background-color: #f97316; color: white; box-shadow: var(--sombra-sm);" onmouseover="this.style.backgroundColor='#ea580c'; this.style.boxShadow='var(--sombra-md)';" onmouseout="this.style.backgroundColor='#f97316'; this.style.boxShadow='var(--sombra-sm)';">
                                <span>+</span>
                                Agregar Asignatura
                            </button>
                        </div>
                    </div>
                    <div>
                        <!-- Contenedor de Asignaturas por Carrera y Año -->
                        <div id="contenedorAsignaturas">
                            <!-- Se llenará dinámicamente con la estructura por carrera/año -->
                            <div style="padding: var(--espacio-xxl) var(--espacio-lg); text-align: center;">
                                <span style="font-size: 3rem; display: block; margin-bottom: var(--espacio-md); opacity: 0.5;">📖</span>
                                <p style="font-size: 1.1rem; color: var(--color-texto-secundario); margin-bottom: var(--espacio-xs);">No hay asignaturas agregadas</p>
                                <small style="font-size: 0.9rem; color: var(--color-texto-secundario); opacity: 0.7;">Haga clic en "Agregar Asignatura" para comenzar</small>
                            </div>
                        </div>
                    </div>
                </div>


            </section>

            <!-- ====================================
                 SECCIÓN: ESTADO Y SEGUIMIENTO
                 ==================================== -->
            <section style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.75rem; color: var(--color-primario-oscuro); margin: 3rem 0 1.5rem; font-weight: 600;">Estado y Seguimiento de la Solicitud</h3>

                <div class="bg-white p-6 rounded-xl shadow-sm border" style="background: var(--color-blanco); padding: 1.5rem; border-radius: var(--radio-borde-lg); box-shadow: var(--sombra-sm); border: 1px solid var(--color-borde);">
                    <!-- Flujo de Estados Visualizado -->
                    <div class="flujo-estados">
                        <!-- LÓGICA: El estado activo cambiará según el flujo de validación -->
                        <div class="estado-paso activo">
                            <div class="estado-icono">1</div>
                            <div class="estado-info">
                                <span class="estado-nombre">Borrador</span>
                                <small style="display: block; font-size: 0.7rem; color: var(--color-texto-secundario); margin-top: var(--espacio-xs);">--</small>
                            </div>
                        </div>
                        <div class="estado-linea"></div>
                        
                        <div class="estado-paso">
                            <div class="estado-icono">2</div>
                            <div class="estado-info">
                                <span class="estado-nombre">Enviada</span>
                                <small style="display: block; font-size: 0.7rem; color: var(--color-texto-secundario); margin-top: var(--espacio-xs);">--</small>
                            </div>
                        </div>
                        <div class="estado-linea"></div>
                        
                        <div class="estado-paso">
                            <div class="estado-icono">3</div>
                            <div class="estado-info">
                                <span class="estado-nombre">En Revisión</span>
                                <small style="display: block; font-size: 0.7rem; color: var(--color-texto-secundario); margin-top: var(--espacio-xs);">--</small>
                            </div>
                        </div>
                        <div class="estado-linea"></div>
                        
                        <div class="estado-paso">
                            <div class="estado-icono">4</div>
                            <div class="estado-info">
                                <span class="estado-nombre">Devuelta</span>
                                <small style="display: block; font-size: 0.7rem; color: var(--color-texto-secundario); margin-top: var(--espacio-xs);">--</small>
                            </div>
                        </div>
                        <div class="estado-linea"></div>
                        
                        <div class="estado-paso">
                            <div class="estado-icono">5</div>
                            <div class="estado-info">
                                <span class="estado-nombre">Rechazada</span>
                                <small style="display: block; font-size: 0.7rem; color: var(--color-texto-secundario); margin-top: var(--espacio-xs);">--</small>
                            </div>
                        </div>
                        <div class="estado-linea"></div>
                        
                        <div class="estado-paso">
                            <div class="estado-icono">6</div>
                            <div class="estado-info">
                                <span class="estado-nombre">Aprobada</span>
                                <small style="display: block; font-size: 0.7rem; color: var(--color-texto-secundario); margin-top: var(--espacio-xs);">--</small>
                            </div>
                        </div>
                    </div>

                    <!-- Botón Enviar Borrador -->
                    <div style="margin-top: var(--espacio-lg); display: flex; gap: 1rem;">
                        <button onclick="enviarBorrador()" style="padding: 0.75rem 2rem; font-size: 1rem; font-weight: 600; border: none; border-radius: var(--radio-borde-sm); cursor: pointer; transition: all 0.3s ease; background-color: #10b981; color: white; box-shadow: var(--sombra-sm);" onmouseover="this.style.backgroundColor='#059669'; this.style.boxShadow='var(--sombra-md)';" onmouseout="this.style.backgroundColor='#10b981'; this.style.boxShadow='var(--sombra-sm)';">
                            Enviar Borrador
                        </button>
                    </div>

                    <div style="margin-top: var(--espacio-lg);">
                        <label style="display: block; font-weight: 600; color: var(--color-texto); margin-bottom: var(--espacio-xs); font-size: 0.95rem;">Comentarios de Revisión</label>
                        <div style="border: 2px solid var(--color-borde); border-radius: var(--radio-borde-sm); padding: var(--espacio-md); background-color: var(--color-blanco); min-height: 150px;">
                            <div style="text-align: center; padding: var(--espacio-xl); color: var(--color-texto-secundario);">
                                <p style="margin: 0;">No hay comentarios de revisión</p>
                            </div>
                            <div style="display: none; padding: var(--espacio-md); border-left: 4px solid var(--color-secundario); background-color: var(--color-fondo); border-radius: var(--radio-borde-sm); margin-bottom: var(--espacio-md);">
                                <div style="display: flex; justify-content: space-between; margin-bottom: var(--espacio-sm); font-size: 0.9rem;">
                                    <strong>Revisor:</strong> <span>Nombre del Revisor</span>
                                    <small style="color: var(--color-texto-secundario);">Fecha</small>
                                </div>
                                <div style="color: var(--color-texto); line-height: 1.6;">
                                    Texto del comentario de revisión...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </main>

   
    <!-- MODAL: AGREGAR DISCIPLINA -->
    <div class="modal-overlay" id="modalAgregarDisciplina">
        <div class="modal-eliminar" style="width: 90%; max-width: 500px;">
            <div class="modal-header-eliminar">
                <h3>Agregar Nueva Disciplina</h3>
            </div>
            <div class="modal-body-eliminar">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; color: var(--color-texto); margin-bottom: 0.5rem;">Nombre de la Disciplina *</label>
                    <input type="text" id="inputNombreDisciplina" placeholder="Ej: Cálculo, Álgebra, Programación..." style="width: 100%; padding: 0.75rem; border: 2px solid var(--color-borde); border-radius: var(--radio-borde-sm); font-size: 1rem;">
                </div>
            </div>
            <div class="modal-footer-eliminar">
                <button class="btn btn-secundario" onclick="cerrarModalAgregarDisciplina()">
                    Cancelar
                </button>
                <button class="btn btn-eliminar" onclick="confirmAgregarDisciplina()" style="background-color: #10b981;">
                    Agregar
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: AGREGAR ASIGNATURA -->
    <div class="modal-overlay" id="modalAgregarAsignatura">
        <div class="modal-eliminar" style="width: 90%; max-width: 500px;">
            <div class="modal-header-eliminar">
                <h3>Agregar Nueva Asignatura</h3>
            </div>
            <div class="modal-body-eliminar">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; color: var(--color-texto); margin-bottom: 0.5rem;">Disciplina *</label>
                    <select id="selectDisciplinaAsignatura" style="width: 100%; padding: 0.75rem; border: 2px solid var(--color-borde); border-radius: var(--radio-borde-sm); font-size: 1rem;">
                        <option value="">Seleccione una disciplina...</option>
                    </select>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; color: var(--color-texto); margin-bottom: 0.5rem;">Nombre de la Asignatura *</label>
                    <input type="text" id="inputNombreAsignatura" placeholder="Ej: Cálculo I, Programación..." style="width: 100%; padding: 0.75rem; border: 2px solid var(--color-borde); border-radius: var(--radio-borde-sm); font-size: 1rem;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; color: var(--color-texto); margin-bottom: 0.5rem;">Código (opcional)</label>
                    <input type="text" id="inputCodigoAsignatura" placeholder="Ej: INF-101" style="width: 100%; padding: 0.75rem; border: 2px solid var(--color-borde); border-radius: var(--radio-borde-sm); font-size: 1rem;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; color: var(--color-texto); margin-bottom: 0.5rem;">Horas (opcional)</label>
                    <input type="number" id="inputHorasAsignatura" placeholder="Ej: 40" style="width: 100%; padding: 0.75rem; border: 2px solid var(--color-borde); border-radius: var(--radio-borde-sm); font-size: 1rem;">
                </div>
            </div>
            <div class="modal-footer-eliminar">
                <button class="btn btn-secundario" onclick="cerrarModalAgregarAsignatura()">
                    Cancelar
                </button>
                <button class="btn btn-eliminar" onclick="confirmAgregarAsignatura()" style="background-color: #10b981;">
                    Agregar
                </button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modalEliminar">
        <div class="modal-eliminar">
            <div class="modal-header-eliminar">
                <h3>Confirmar Eliminación</h3>
            </div>
            <div class="modal-body-eliminar">
                <p style="font-size: 1.2rem; color: var(--color-texto); margin-bottom: var(--espacio-sm); font-weight: 600;">¿Está seguro que desea eliminar este elemento?</p>
                <p style="font-size: 0.95rem; color: var(--color-texto-secundario); margin-bottom: var(--espacio-lg);">Esta acción no se puede deshacer.</p>
              <div style="padding: var(--espacio-md); background-color: var(--color-fondo); border-radius: var(--radio-borde-sm); border-left: 4px solid var(--color-error); text-align: left;">
                    <strong style="display: block; font-size: 0.85rem; color: var(--color-texto-secundario); margin-bottom: var(--espacio-xs);">Elemento:</strong>
                    <span id="nombreElemento" style="font-size: 1.1rem; color: var(--color-texto); font-weight: 600;">--</span>
                </div>
            </div>
            <div class="modal-footer-eliminar">
                <button class="btn btn-secundario" onclick="cerrarModalEliminar()">
                    No, Cancelar
                </button>
                <button class="btn btn-eliminar">
                    Sí, Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Script básico para el modal de eliminar -->
    <script>
        // Función para mostrar el modal de eliminar
        function mostrarModalEliminar(nombreElemento) {
            document.getElementById('modalEliminar').style.display = 'flex';
            document.getElementById('nombreElemento').textContent = nombreElemento;
        }

        // Función para cerrar el modal
        function cerrarModalEliminar() {
            document.getElementById('modalEliminar').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera de él
        document.getElementById('modalEliminar').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalEliminar();
            }
        });

        document.querySelectorAll('.btn-icono-pequeno.eliminar').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                // Obtener el nombre del elemento de la fila
                const fila = this.closest('tr');
                const nombre = fila.querySelector('td:nth-child(2)').textContent;
                mostrarModalEliminar(nombre);
            });
        });


        document.addEventListener('DOMContentLoaded', async function() {
            const carreraSelect = document.getElementById('carrera');
            const contenedorPlanes = document.getElementById('contenedorPlanes');

            // Cargar carreras desde el API
            try {
                const response = await fetch('/api/carreras');
                const carreras = await response.json();
                
                console.log('Carreras cargadas desde API:', carreras);
                
                // Cargar carreras en el select
                if (!carreras || carreras.length === 0) {
                    carreraSelect.innerHTML = '<option value="">No hay carreras disponibles</option>';
                } else {
                    carreraSelect.innerHTML = '<option value="" disabled selected>Seleccione una carrera...</option>';
                    carreras.forEach(carrera => {
                        const option = document.createElement('option');
                        option.value = carrera.id;
                        option.textContent = carrera.nombre;
                        carreraSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error cargando carreras desde API:', error);
                carreraSelect.innerHTML = '<option value="">Error al cargar carreras</option>';
            }

            // Mostrar mensaje inicial - no cargar hasta que se seleccione un plan
            document.getElementById('contenedorAsignaturas').innerHTML = `
                <div style="padding: var(--espacio-xxl) var(--espacio-lg); text-align: center;">
                    <p style="font-size: 1.1rem; color: var(--color-texto-secundario); margin-bottom: var(--espacio-xs);">Seleccione un plan de estudio para ver las asignaturas</p>
                </div>
            `;
            document.getElementById('contenedorDisciplinas').innerHTML = `
                <div style="padding: var(--espacio-xxl) var(--espacio-lg); text-align: center;">
                    <p style="font-size: 1.1rem; color: var(--color-texto-secundario); margin-bottom: var(--espacio-xs);">Seleccione un plan de estudio para ver las disciplinas</p>
                </div>
            `;

            // Event listener cuando se cambia la carrera
            carreraSelect.addEventListener('change', async function() {
                const carreraId = this.value;
                
                if (!carreraId) {
                    contenedorPlanes.innerHTML = `
                        <div style="padding: var(--espacio-xxl) var(--espacio-lg); text-align: center; grid-column: 1 / -1;">
                            <p style="font-size: 1.1rem; color: var(--color-texto-secundario); margin-bottom: var(--espacio-xs);">Seleccione una carrera para ver sus planes de estudio</p>
                        </div>
                    `;
                    return;
                }

                // Cargar planes para esta carrera
                try {
                    const response = await fetch(`/api/planes-by-carrera/${carreraId}`);
                    const planes = await response.json();
                    
                    console.log('Planes recibidos:', planes);
                    
                    if (!planes || planes.length === 0) {
                        contenedorPlanes.innerHTML = `
                            <div style="padding: var(--espacio-xxl) var(--espacio-lg); text-align: center; grid-column: 1 / -1;">
                                <span style="font-size: 3rem; display: block; margin-bottom: var(--espacio-md); opacity: 0.5;">📋</span>
                                <>
                        `;
                        return;
                    }

                    // Generar tarjetas para los planes
                    let html = '';
                    planes.forEach(plan => {
                        const anioVigencia = plan.anio_vigencia || plan.fecha_inicio || 'N/A';
                        html += `
                            <div class="plan-card" style="background: var(--color-blanco); border: 2px solid var(--color-borde); border-radius: var(--radio-borde-sm); padding: 1.5rem; cursor: pointer; transition: all 0.3s ease;" onclick="seleccionarPlan(${plan.id}, '${plan.nombre.replace(/'/g, "\\'")}', ${carreraId}, '${anioVigencia}')">
                                <h4 style="font-size: 1.1rem; color: var(--color-primario); font-weight: 600; margin: 0 0 0.5rem 0;">${plan.nombre}</h4>
                                <div style="display: flex; gap: 1rem; font-size: 0.9rem; color: var(--color-texto-secundario);">
                                    <div>
                                        <span style="font-weight: 600;">Año de Vigencia:</span>
                                        <span style="color: var(--color-primario); font-weight: 600;">${anioVigencia}</span>
                                    </div>
                                </div>
                                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-borde);">
                                    <button type="button" style="width: 100%; padding: 0.75rem 1rem; background-color: #10b981; color: white; border: none; border-radius: var(--radio-borde-sm); font-weight: 600; cursor: pointer; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#059669'; this.style.boxShadow='var(--sombra-md)';" onmouseout="this.style.backgroundColor='#10b981'; this.style.boxShadow='var(--sombra-sm)';" onclick="seleccionarPlan(${plan.id}, '${plan.nombre.replace(/'/g, "\\'")}', ${carreraId}, '${anioVigencia}'); event.stopPropagation();">
                                        ✅ Seleccionar
                                    </buon>
                                </div>
                            </div>
                        `;
                    });

                    contenedorPlanes.innerHTML = html;
                    
                    // Añadir estilos hover
                    document.querySelectorAll('.plan-card').forEach(card => {
                        card.addEventListener('mouseenter', function() {
                            this.style.borderColor = 'var(--color-primario)';
                            this.style.boxShadow = 'var(--sombra-md)';
                        });
                        card.addEventListener('mouseleave', function() {
                            this.style.borderColor = 'var(--color-borde)';
                            this.style.boxShadow = 'none';
                        });
                    });
                } catch (error) {
                    console.error('Error cargando planes:', error);
                    contenedorPlanes.innerHTML = `<p style="color: red; grid-column: 1 / -1;">Error al cargar los planes de estudio: ${error.message}</p>`;
                }
            });
        });

        // Función para seleccionar un plan
        function seleccionarPlan(planId, planNombre, carreraId, anioVigencia) {
            console.log('Plan seleccionado:', { id: planId, nombre: planNombre, carrera: carreraId, anio: anioVigencia });
            
            // Guardar en localStorage para acceso global
            window.planSeleccionado = {
                id: planId,
                nombre: planNombre,
                carreraId: carreraId,
                anioVigencia: anioVigencia
            };
            
            // Cargar asignaturas y disciplinas del plan seleccionado
            cargarAsignaturasPorPlan(planId);
            cargarDisciplinasPorPlan(planId);
        }

        // Función para cargar asignaturas de un plan específico
        async function cargarAsignaturasPorPlan(planId) {
            const contenedorAsignaturas = document.getElementById('contenedorAsignaturas');

            try {
                const response = await fetch(`/api/asignaturas?plan_estudio_id=${planId}`);
                const asignaturas = await response.json();

                if (!asignaturas || asignaturas.length === 0) {
                    contenedorAsignaturas.innerHTML = `
                        <div style="padding: var(--espacio-xxl) var(--espacio-lg); text-align: center;">
                            <p style="font-size: 1.1rem; color: var(--color-texto-secundario); margin-bottom: var(--espacio-xs);">No hay asignaturas en este plan de estudio</p>
                        </div>
                    `;
                    return;
                }

                // Agrupar asignaturas por disciplina
                const asignaturasAgrupadas = agruparAsignaturasPorDisciplina(asignaturas);

                let html = '';
                
                for (const [disciplinaId, disciplinaData] of Object.entries(asignaturasAgrupadas)) {
                    html += `<div data-discipline-subjects-id="${disciplinaId}" style="margin-left: 1rem; margin-bottom: 1rem;">`;
                    html += `<div style="font-size: 0.95rem; color: var(--color-texto-secundario); font-weight: 500; margin-bottom: 0.5rem;">Disciplina: <strong style="color: var(--color-texto);">${disciplinaData.nombre}</strong></div>`;
                    html += `<div class="tabla-wrapper" style="border-radius: var(--radio-borde-sm);">`;
                    html += `<table class="tabla" style="font-size: 0.95rem;">`;
                    html += `<thead style="background-color: rgba(249, 115, 22, 0.9);">`;
                    html += `<tr><th>Código</th><th>Nombre de la Asignatura</th><th style="width: 100px; text-align: center;">Acciones</th></tr>`;
                    html += `</thead><tbody>`;
                    
                    disciplinaData.asignaturas.forEach(asignatura => {
                        html += `<tr data-subject-id="${asignatura.id}" data-discipline-id="${disciplinaId}">
                                    <td>${asignatura.codigo || 'N/A'}</td>
                                    <td>${asignatura.nombre}</td>
                                    <td class="col-acciones" style="text-align: center;">
                                        <button class="btn-icono-pequeno eliminar" onclick="abrirConfirmarEliminarAsignatura('${asignatura.nombre}', ${asignatura.id})" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #ef4444; font-weight: bold;">−</button>
                                    </td>
                                </tr>`;
                    });
                    
                    html += `</tbody></table></div></div>`;
                }

                contenedorAsignaturas.innerHTML = html;
            } catch (error) {
                console.error('Error cargando asignaturas:', error);
                contenedorAsignaturas.innerHTML = `<p style="color: red;">Error al cargar las asignaturas</p>`;
            }
        }

        // Función para agrupar asignaturas por disciplina
        function agruparAsignaturasPorDisciplina(asignaturas) {
            const agrupado = {};

            asignaturas.forEach(asignatura => {
                const disciplinaId = asignatura.disciplina?.id || 'sin-id';
                const disciplinaNombre = asignatura.disciplina?.nombre || 'Sin Disciplina';

                if (!agrupado[disciplinaId]) {
                    agrupado[disciplinaId] = {
                        nombre: disciplinaNombre,
                        asignaturas: []
                    };
                }

                agrupado[disciplinaId].asignaturas.push(asignatura);
            });

            return agrupado;
        }

        // Función para cargar disciplinas de un plan específico
        async function cargarDisciplinasPorPlan(planId) {
            const contenedorDisciplinas = document.getElementById('contenedorDisciplinas');

            try {
                const response = await fetch(`/api/disciplinas?plan_estudio_id=${planId}`);
                const disciplinas = await response.json();

                if (!disciplinas || disciplinas.length === 0) {
                    contenedorDisciplinas.innerHTML = `
                        <div style="padding: var(--espacio-xxl) var(--espacio-lg); text-align: center;">
                            <p style="font-size: 1.1rem; color: var(--color-texto-secundario); margin-bottom: var(--espacio-xs);">No hay disciplinas en este plan de estudio</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                html += `<div class="tabla-wrapper" style="border-radius: var(--radio-borde-sm);">`;
                html += `<table class="tabla" style="font-size: 0.95rem;">`;
                html += `<thead style="background-color: rgba(249, 115, 22, 0.9);">`;
                html += `<tr><th>Código</th><th>Nombre de la Disciplina</th><th style="width: 100px; text-align: center;">Acciones</th></tr>`;
                html += `</thead><tbody>`;
                
                disciplinas.forEach(disciplina => {
                    html += `<tr data-discipline-id="${disciplina.id}">
                                <td>${disciplina.codigo || 'N/A'}</td>
                                <td>${disciplina.nombre}</td>
                                <td class="col-acciones" style="text-align: center;">
                                    <button class="btn-icono-pequeno eliminar" onclick="abrirConfirmarEliminarDisciplina('${disciplina.nombre}', ${disciplina.id}, this)" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #ef4444; font-weight: bold;">−</button>
                                </td>
                            </tr>`;
                });
                
                html += `</tbody></table></div>`;

                contenedorDisciplinas.innerHTML = html;
            } catch (error) {
                console.error('Error cargando disciplinas:', error);
                contenedorDisciplinas.innerHTML = `<p style="color: red;">Error al cargar las disciplinas</p>`;
            }
        }

        // ============ FUNCIONES DE MODALES PARA AGREGAR ============
        
        function abrirModalAgregarDisciplina() {
            if (!window.planSeleccionado) {
                alert('Por favor seleccione un plan de estudio primero');
                return;
            }
            document.getElementById('inputNombreDisciplina').value = '';
            document.getElementById('modalAgregarDisciplina').style.display = 'flex';
        }

        function cerrarModalAgregarDisciplina() {
            document.getElementById('modalAgregarDisciplina').style.display = 'none';
        }

        function abrirModalAgregarAsignatura() {
            if (!window.planSeleccionado) {
                alert('Por favor seleccione un plan de estudio primero');
                return;
            }
            
            // Poblar el selector de disciplinas
            const select = document.getElementById('selectDisciplinaAsignatura');
            const disciplinaRows = document.querySelectorAll('#contenedorDisciplinas table tbody tr');
            
            select.innerHTML = '<option value="">Seleccione una disciplina...</option>';
            disciplinaRows.forEach(row => {
                const id = row.getAttribute('data-discipline-id');
                const nombre = row.querySelector('td:nth-child(2)').textContent;
                const option = document.createElement('option');
                option.value = id;
                option.textContent = nombre;
                select.appendChild(option);
            });
            
            // Limpiar campos
            document.getElementById('inputNombreAsignatura').value = '';
            document.getElementById('inputCodigoAsignatura').value = '';
            document.getElementById('inputHorasAsignatura').value = '';
            document.getElementById('modalAgregarAsignatura').style.display = 'flex';
        }

        function cerrarModalAgregarAsignatura() {
            document.getElementById('modalAgregarAsignatura').style.display = 'none';
        }

        function confirmAgregarDisciplina() {
            const nombre = document.getElementById('inputNombreDisciplina').value.trim();

            if (!nombre) {
                alert('Por favor ingrese el nombre de la disciplina');
                return;
            }

            // Crear nueva disciplina temporal (solo en interfaz)
            const newDisciplina = {
                id: Date.now(),
                nombre: nombre,
                codigo: 'N/A'
            };

            // Agregar a la tabla sin guardar en BD
            const tabla = document.querySelector('#contenedorDisciplinas table tbody');
            const newRow = document.createElement('tr');
            newRow.setAttribute('data-discipline-id', newDisciplina.id);
            newRow.innerHTML = `
                <td>${newDisciplina.codigo}</td>
                <td>${newDisciplina.nombre}</td>
                <td class="col-acciones" style="text-align: center;">
                    <button class="btn-icono-pequeno eliminar" onclick="abrirConfirmarEliminarDisciplina('${newDisciplina.nombre}', ${newDisciplina.id}, this)" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #ef4444; font-weight: bold;">−</button>
                </td>
            `;
            tabla.appendChild(newRow);

            cerrarModalAgregarDisciplina();
            alert('Disciplina agregada a la interfaz (no guardada en BD)');
        }

        function confirmAgregarAsignatura() {
            const disciplinaId = document.getElementById('selectDisciplinaAsignatura').value;
            const nombre = document.getElementById('inputNombreAsignatura').value.trim();
            const codigo = document.getElementById('inputCodigoAsignatura').value.trim();
            const horas = document.getElementById('inputHorasAsignatura').value.trim();

            if (!disciplinaId) {
                alert('⚠️ Por favor seleccione una disciplina');
                return;
            }

            if (!nombre) {
                alert('⚠️ Por favor ingrese el nombre de la asignatura');
                return;
            }

            // Obtener nombre de la disciplina
            const disciplinaRow = document.querySelector(`tr[data-discipline-id="${disciplinaId}"]`);
            const disciplinaNombre = disciplinaRow ? disciplinaRow.querySelector('td:nth-child(2)').textContent : 'Sin Disciplina';

            // Crear nueva asignatura temporal (solo en interfaz)
            const newAsignatura = {
                id: Date.now(),
                nombre: nombre,
                codigo: codigo || 'N/A',
                horas: horas || '--',
                disciplinaId: disciplinaId,
                disciplinaNombre: disciplinaNombre
            };

            // Buscar o crear tabla de la disciplina en asignaturas
            let tablaAsignaturasDiv = document.querySelector(`[data-discipline-subjects-id="${disciplinaId}"]`);
            
            if (!tablaAsignaturasDiv) {
                // Crear nueva sección para esta disciplina si no existe
                const contenedor = document.getElementById('contenedorAsignaturas');
                tablaAsignaturasDiv = document.createElement('div');
                tablaAsignaturasDiv.setAttribute('data-discipline-subjects-id', disciplinaId);
                tablaAsignaturasDiv.style.marginLeft = '1rem';
                tablaAsignaturasDiv.style.marginBottom = '1rem';
                
                tablaAsignaturasDiv.innerHTML = `
                    <div style="font-size: 0.95rem; color: var(--color-texto-secundario); font-weight: 500; margin-bottom: 0.5rem;">
                        Disciplina: <strong style="color: var(--color-texto);">${disciplinaNombre}</strong>
                    </div>
                    <div class="tabla-wrapper" style="border-radius: var(--radio-borde-sm);">
                        <table class="tabla" style="font-size: 0.95rem;">
                            <thead style="background-color: rgba(249, 115, 22, 0.9);">
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre de la Asignatura</th>
                                    <th style="width: 100px; text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                `;
                
                contenedor.appendChild(tablaAsignaturasDiv);
            }

            // Agregar a la tabla de asignaturas
            const tabla = tablaAsignaturasDiv.querySelector('table tbody');
            const newRow = document.createElement('tr');
            newRow.setAttribute('data-subject-id', newAsignatura.id);
            newRow.setAttribute('data-discipline-id', disciplinaId);
            newRow.innerHTML = `
                <td>${newAsignatura.codigo}</td>
                <td>${newAsignatura.nombre}</td>
                <td class="col-acciones" style="text-align: center;">
                    <button class="btn-icono-pequeno eliminar" onclick="abrirConfirmarEliminarAsignatura('${newAsignatura.nombre}', ${newAsignatura.id})" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;">🗑️</button>
                </td>
            `;
            tabla.appendChild(newRow);

            cerrarModalAgregarAsignatura();
            alert('✅ Asignatura agregada a la interfaz (no guardada en BD)');
        }

        function abrirConfirmarEliminarDisciplina(nombre, id, btn) {
            window.elementoAEliminar = {
                id: id,
                nombre: nombre,
                tipo: 'disciplina',
                btn: btn
            };
            document.getElementById('nombreElemento').textContent = nombre;
            document.getElementById('modalEliminar').style.display = 'flex';
        }

        function abrirConfirmarEliminarAsignatura(nombre, id) {
            window.elementoAEliminar = {
                id: id,
                nombre: nombre,
                tipo: 'asignatura'
            };
            document.getElementById('nombreElemento').textContent = nombre;
            document.getElementById('modalEliminar').style.display = 'flex';
        }

        // Evento para el botón de confirmar eliminación
        document.addEventListener('DOMContentLoaded', function() {
            const btnEliminar = document.querySelector('#modalEliminar .btn-eliminar');
            if (btnEliminar) {
                btnEliminar.onclick = function() {
                    if (window.elementoAEliminar) {
                        if (window.elementoAEliminar.tipo === 'disciplina') {
                            // Eliminar disciplina y todas sus asignaturas
                            const disciplinaId = window.elementoAEliminar.id;
                            
                            // Eliminar la fila de disciplina
                            const filaDisciplina = document.querySelector(`tr[data-discipline-id="${disciplinaId}"]`);
                            if (filaDisciplina) {
                                filaDisciplina.remove();
                            }
                            
                            // Eliminar todas las asignaturas asociadas
                            const asignaturasAsociadas = document.querySelectorAll(`tr[data-discipline-id="${disciplinaId}"][data-subject-id]`);
                            asignaturasAsociadas.forEach(fila => {
                                fila.remove();
                            });
                            
                            // Eliminar la sección de disciplina en asignaturas
                            const seccionAsignaturas = document.querySelector(`[data-discipline-subjects-id="${disciplinaId}"]`);
                            if (seccionAsignaturas) {
                                seccionAsignaturas.remove();
                            }
                            
                            alert('✅ Disciplina y sus asignaturas eliminadas de la interfaz\n(no se afectó la BD)');
                        } else if (window.elementoAEliminar.tipo === 'asignatura') {
                            // Eliminar solo la asignatura
                            const asignaturaId = window.elementoAEliminar.id;
                            const filaAsignatura = document.querySelector(`tr[data-subject-id="${asignaturaId}"]`);
                            if (filaAsignatura) {
                                filaAsignatura.remove();
                            }
                            alert('✅ Asignatura eliminada de la interfaz (no se afectó la BD)');
                        }
                    }
                    cerrarModalEliminar();
                };
            }

            // Cerrar modales al hacer clic fuera
            document.getElementById('modalEliminar').addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModalEliminar();
                }
            });

            document.getElementById('modalAgregarDisciplina').addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModalAgregarDisciplina();
                }
            });

            document.getElementById('modalAgregarAsignatura').addEventListener('click', function(e) {
                if (e.target === this) {
                    cerrarModalAgregarAsignatura();
                }
            });
        });

        // Función para enviar borrador (solo visual, no hace nada)
        function enviarBorrador() {
            alert('Función "Enviar Borrador" aún no disponible');
        }
    </script>

</body>
</html>