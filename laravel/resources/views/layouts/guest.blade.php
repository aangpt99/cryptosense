<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900">

    <div class="min-h-screen flex flex-col items-center justify-center px-4">

        <!-- HEADER -->
        <div class="text-center mb-8">

            <!-- Logo -->
            <a href="/" class="flex justify-center mb-5">
                <img 
                    src="{{ asset('images/lolo.jpeg') }}"
                    alt="Logo"
                    style="width: 120px; height: 120px; object-fit: contain;"
                >
            </a>

            <!-- Title -->
            <h1 class="text-4xl font-bold text-gray-900 tracking-tight">
                Welcome Back
            </h1>

            <!-- Subtitle -->
            <p class="mt-3 text-sm text-gray-500">
                Sign in to continue to CRYPTO ANALYSIS SENTIMENT
            </p>

        </div>

                <!-- CARD -->
        <div 
            class="mx-auto w-full"
            style="max-width: 430px;"
        >
            
            <div
                class="bg-white/85 backdrop-blur-xl rounded-3xl shadow-[0_10px_40px_rgba(15,23,42,0.08)] px-8 py-8"
            >

                {{ $slot }}

            </div>

        </div>

        <!-- Footer -->
        <p class="mt-6 text-xs text-gray-400">
        © {{ date('Y') }} CRYPTO ANALYSIS SENTIMENT
        </p>
    </div>

</body>
</html>