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
            background: #0A0E14;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            color: #E6EDF3;
        }

        .input-smooth {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #1f2937;
            background: #071017;
            color: #E6EDF3;
        }

        .input-smooth:focus {
            border-color: #FF8A00;
            box-shadow: 0 0 0 3px rgba(255,138,0, 0.08);
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
    <div class="min-h-screen flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">

                <!-- HEADER -->
                <div class="mb-6">
                    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-300 hover:text-white">
                        <span class="text-base">←</span>
                        Back
                    </a>
                </div>
                <div class="mb-10">
                    <a href="/" class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#262626] to-[#FF8A00] flex items-center justify-center text-white font-black">
                            ⚙
                        </div>
                        <span class="text-xl font-bold text-white"><span class="mf-charcoal">Mech</span><span class="mf-accent">Finder</span></span>
                    </a>

                    <h1 class="text-3xl font-bold text-white mb-2">Motorist sign up</h1>
                    <p class="text-slate-300">Create your motorist profile</p>
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

                    <button type="submit" class="btn-smooth w-full bg-[#FF8A00] hover:brightness-95 text-[#071017] font-semibold py-3 rounded-lg mt-6">
                        Register
                    </button>
                </form>

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
