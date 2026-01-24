<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda Semestral | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-black flex items-center justify-center p-4">

    <div class="w-full max-w-sm">

        <!-- Título -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-orange-500 mb-2">
                Agenda Semestral
            </h1>
            <p class="text-gray-400">Iniciar sesión</p>
        </div>

        <!-- Tarjeta de login -->
        <div class="bg-neutral-900 rounded-lg p-8">

            <form id="loginForm" class="space-y-4">
                @csrf

                <!-- Correo -->
                <div>
                    <label class="block text-sm text-white mb-1">Correo</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="w-full px-4 py-2 bg-black border border-orange-500 text-white rounded focus:outline-none"
                        placeholder="ejemplo@correo.com"
                    >
                    <p id="emailError" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>

                <!-- Contraseña -->
                <div>
                    <label class="block text-sm text-white mb-1">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full px-4 py-2 bg-black border border-orange-500 text-white rounded focus:outline-none"
                        placeholder="******"
                    >
                    <p id="passwordError" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>

                <!-- Botón -->
                <button
                    type="submit"
                    class="w-full mt-4 py-2 bg-orange-500 text-black font-semibold rounded hover:bg-orange-600"
                >
                    Entrar
                </button>
            </form>
        </div>
    </div>

    <!-- Validación básica -->
    <script>
        const form = document.getElementById('loginForm');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');

            // Ocultar errores
            emailError.classList.add('hidden');
            passwordError.classList.add('hidden');

            let valido = true;

            // Validar correo
            if (email.value === '') {
                emailError.textContent = 'El correo es obligatorio';
                emailError.classList.remove('hidden');
                valido = false;
            }

            // Validar contraseña
            if (password.value === '') {
                passwordError.textContent = 'La contraseña es obligatoria';
                passwordError.classList.remove('hidden');
                valido = false;
            }

            // Si todo está bien
            if (valido) {
                console.log('Login correcto');
                // form.submit(); // Se usa cuando el backend esté listo
            }
        });
    </script>

</body>
</html>
