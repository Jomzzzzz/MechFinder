<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MechFinder</title>

<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

@vite('resources/css/app.css')

<style>
body { font-family: 'Inter', sans-serif; }
.heading-font {
    font-family: 'Oswald', sans-serif;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
</style>
</head>

<body class="bg-[#0B0B0C] text-[#EEEEEE]">

<div class="flex min-h-screen">

    @include('components.sidebar')

    <div class="flex-1 flex flex-col">
        @yield('content')
    </div>

</div>

</body>
</html>