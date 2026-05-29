<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MechFinder | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        .input-smooth {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e5e7eb;
            background: #ffffff;
        }

        .input-smooth:focus {
            border-color: #F7941D;
            box-shadow: 0 0 0 3px rgba(247, 148, 29, 0.1);
            outline: none;
        }

        .btn-smooth {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-smooth:active {
            transform: scale(0.98);
        }

        .logo-accent {
            color: #F7941D;
        }
    </style>
</head>

<body>
    <div class="min-h-screen grid lg:grid-cols-2">

        <!-- LEFT SIDE - IMAGE -->
        <div class="hidden lg:flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-300 p-12">
            <div class="max-w-sm w-full">
                <img src="{{ asset('images/login-image.jpg') }}" alt="Motorcycle Rider" alt="Motorcycle"
                    class="w-full h-auto object-contain filter brightness-110">
            </div>
        </div>

        <!-- RIGHT SIDE - LOGIN FORM -->
        <div class="flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm">

                <!-- HEADER -->
                <div class="mb-12">
                    <a href="/" class="flex items-center gap-3 mb-8">
                        <div
                            class="w-10 h-10 rounded-lg bg-[#F7941D] flex items-center justify-center text-white font-black">
                            ⚙
                        </div>
                        <span class="text-xl font-bold text-gray-900">MechFinder</span>
                    </a>

                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h1>
                    <p class="text-gray-500">Sign in to your account</p>
                </div>

                <!-- MESSAGES -->
                @if (session('error'))
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                        <p class="text-red-700 text-sm">{{ session('error') }}</p>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200">
                        <p class="text-green-700 text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="text-red-700 text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM -->
                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="you@example.com" class="input-smooth w-full px-4 py-3 rounded-lg text-base">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required placeholder="••••••••"
                                class="input-smooth w-full px-4 py-3 rounded-lg text-base pr-12">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 border-gray-300 rounded accent-[#F7941D]">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-[#F7941D] hover:text-orange-600">
                            Forgot password?
                        </a>
                    </div>

                    <button type="submit"
                        class="btn-smooth w-full bg-[#F7941D] hover:bg-orange-600 text-white font-semibold py-3 rounded-lg mt-6">
                        Sign in
                    </button>
                </form>

                <!-- SIGNUP LINK -->
                <p class="text-center mt-8 text-gray-600 text-sm">
                    Don't have an account?
                    <a href="{{ route('signup') }}" class="font-semibold text-[#F7941D] hover:text-orange-600">
                        Create one
                    </a>
                </p>

            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (password.type === 'password') {
                password.type = 'text';
            } else {
                password.type = 'password';
            }
        }
    </script>

</body>

</html>
