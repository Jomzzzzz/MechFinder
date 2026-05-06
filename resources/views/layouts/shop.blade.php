<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MechFinder</title>

    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.shopId = {{ Auth::check() && Auth::user()->shop_id ? (int) Auth::user()->shop_id : 'null' }};
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .heading-font {
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>

<body class="bg-[#0A0A0B] text-[#EEEEEE]">

    <div class="flex min-h-screen">

        @include('components.sidebar')

        <main class="flex-1 p-8 overflow-y-auto">
            @yield('content')
        </main>

    </div>

</body>

</html>
