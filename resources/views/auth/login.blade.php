<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - Gestion de Rendez-vous</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #F2F3F6 0%, #F4F3F6 100%);
        }
        .card-shadow {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        
        <!-- Login Card -->
        <div class="bg-white rounded-2xl card-shadow p-8">
            <!-- Logo / Branding -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center bg-white">
                <img src="{{ asset('images/logo_web_512x192.png') }}" alt="Logo" class="w-40 h-40">
            </div>
            {{-- <img src="{{ asset('images/logo_web_512x192.png') }}" alt="Logo" class="w-40 h-40"> --}}

            {{-- <h1 class="text-3xl font-bold text-gray-800 mb-2">Gestion de Rendez-vous</h1> --}}
            <p class="text-gray-600">Connectez-vous pour accéder à votre espace</p>
        </div>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <div class="text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <div class="text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('soumission') }}">
                @csrf

                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-700"></i>Adresse email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-700 focus:border-transparent transition duration-200"
                        placeholder="exemple@email.com"
                    >
                </div>

                <!-- Password Field -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-blue-700"></i>Mot de passe
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-700 focus:border-transparent transition duration-200 pr-12"
                            placeholder="••••••••"
                        >
                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="remember"
                            {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-700 border-gray-300 rounded focus:ring-blue-700"
                        >
                        <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-700 hover:text-blue-800 font-medium">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-blue-700 text-white py-3 rounded-lg font-semibold hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 transition duration-200 transform hover:scale-[1.02]"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                </button>
            </form>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} Gestion de Rendez-vous. Tous droits réservés.</p>
            </div>
        </div>

       
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
