<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - MechFinder</title>
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
    </style>
</head>

<body>
    <div class="min-h-screen grid lg:grid-cols-2">

        <!-- LEFT SIDE - IMAGE -->
        <div class="hidden lg:flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 p-12">
            <div class="max-w-sm w-full">
                <img src="{{ asset('images/register-image.png') }}" alt="Motorcycle Rider"
                     alt="Motorcycle Rider"
                     class="w-full h-auto object-contain filter brightness-105">
            </div>
        </div>

        <!-- RIGHT SIDE - SIGNUP FORM -->
        <div class="flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm">

                <!-- HEADER -->
                <div class="mb-10">
                    <a href="/" class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-lg bg-[#F7941D] flex items-center justify-center text-white font-black">
                            ⚙
                        </div>
                        <span class="text-xl font-bold text-gray-900">MechFinder</span>
                    </a>

                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Create account</h1>
                    <p class="text-gray-500">Join as a motorist</p>
                </div>

                <!-- MESSAGES -->
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
                <form method="POST" action="{{ route('signup.post') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full name</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               placeholder="Juan Dela Cruz"
                               class="input-smooth w-full px-4 py-3 rounded-lg text-base">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               placeholder="you@example.com"
                               class="input-smooth w-full px-4 py-3 rounded-lg text-base">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input id="password"
                                   type="password"
                                   name="password"
                                   required
                                   placeholder="••••••••"
                                   class="input-smooth w-full px-4 py-3 rounded-lg text-base pr-12">
                            <button type="button"
                                    onclick="togglePassword('password')"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                👁
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm password</label>
                        <div class="relative">
                            <input id="password_confirmation"
                                   type="password"
                                   name="password_confirmation"
                                   required
                                   placeholder="••••••••"
                                   class="input-smooth w-full px-4 py-3 rounded-lg text-base pr-12">
                            <button type="button"
                                    onclick="togglePassword('password_confirmation')"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                👁
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-smooth w-full bg-[#F7941D] hover:bg-orange-600 text-white font-semibold py-3 rounded-lg mt-6">
                        Create account
                    </button>
                </form>

                <!-- DIVIDER -->
                <div class="flex items-center gap-3 my-8">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-xs text-gray-500 font-medium">OR</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <!-- SOCIAL -->
                <div class="flex gap-3">
                    <button type="button" class="flex-1 flex items-center justify-center px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12c0 5.302 3.438 9.834 8.207 11.387.6.11.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v-3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                    </button>
                    <button type="button" class="flex-1 flex items-center justify-center px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.05 20.28c-.98.95-2.05.85-3.08.4-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.48-2.53 3.2l-.42-.07z"/></svg>
                    </button>
                    <a href="{{ route('auth.google.signup') }}" class="flex-1 flex items-center justify-center px-4 py-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition font-bold">
                        G
                    </a>
                </div>

                <!-- LOGIN LINK -->
                <p class="text-center mt-8 text-gray-600 text-sm">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-[#F7941D] hover:text-orange-600">
                        Sign in
                    </a>
                </p>

            </div>
        </div>

    </div>

    <script>
        function togglePassword(id) {
            const field = document.getElementById(id);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>
</html>
