<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agenda Semestral | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .input-field {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            background-color: #f9fafb;
        }
        
        .input-field:focus {
            border-color: #f97316;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }
        
        .login-button {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            transition: all 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(249, 115, 22, 0.3);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm">

        <div class="bg-white rounded-2xl login-container p-10">
            
          
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    Agenda Semestral
                </h1>
                <p class="text-gray-500 text-sm">Ingresa a tu cuenta para continuar</p>
            </div>

            <!-- Formulario -->
            <form id="loginForm" class="space-y-5" method="POST" action="<?php echo e(route('login.post')); ?>">
                <?php echo csrf_field(); ?>

                <!-- Correo -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Correo Electrónico
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        class="input-field w-full px-4 py-3 rounded-lg text-gray-800 placeholder-gray-400 outline-none"
                        placeholder="correo@ejemplo.com"
                    >
                    <p id="emailError" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>

                <!-- Contraseña -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Contraseña
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="input-field w-full px-4 py-3 rounded-lg text-gray-800 placeholder-gray-400 outline-none"
                        placeholder="••••••••"
                    >
                    <p id="passwordError" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>

                <!-- Recuerda y Olvide -->
                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300" />
                        <span class="ml-2 text-gray-600">Recuérdame</span>
                    </label>
                    <a href="#" class="text-orange-500 hover:text-orange-600 font-medium">¿Olvidé contraseña?</a>
                </div>

                <!-- Botón Login -->
                <button
                    type="submit"
                    class="login-button w-full py-3 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg mt-6 hover:shadow-lg"
                >
                    Iniciar Sesión
                </button>
            </form>

            <!-- Divider -->
            <div class="my-6 flex items-center">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="px-3 text-gray-400 text-sm">o</span>
                <div class="flex-1 h-px bg-gray-200"></div>
            </div>

            <!-- Registro -->
            <p class="text-center text-gray-600 text-sm">
                ¿No tienes cuenta? 
                <a href="#" class="text-orange-500 hover:text-orange-600 font-semibold">Regístrate aquí</a>
            </p>
        </div>

        <!-- Footer -->
        <p class="text-center text-white text-xs mt-8 opacity-75">
            Sistema de Gestión Académica © 2026
        </p>
    </div>

    <!-- Validación -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');

            // Ocultar mensajes previos
            emailError.classList.add('hidden');
            passwordError.classList.add('hidden');

            let ok = true;

            // Validar email
            if (email.value.trim() === '') {
                emailError.textContent = 'El correo es obligatorio';
                emailError.classList.remove('hidden');
                ok = false;
            }

            // Validar contraseña
            if (password.value === '') {
                passwordError.textContent = 'La contraseña es obligatoria';
                passwordError.classList.remove('hidden');
                ok = false;
            }

            if (ok) {
                this.submit();
            }
        });
    </script>

</body>
</html>
<?php /**PATH D:\Ing Informatica\III AÑO\I Semestre\bdw\backend\resources\views/auth/login.blade.php ENDPATH**/ ?>