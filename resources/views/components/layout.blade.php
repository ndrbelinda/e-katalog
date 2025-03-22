<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    @vite('resources/css/app.css')
    <title>Produk</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex flex-col">

    {{-- Navigation Bar --}}
    <x-navbar />

    {{-- Content --}}
    <div class="flex-grow">
        <main class="w-full py-4 px-12">
            {{ $slot }}
        </main>
    </div>

    {{-- Footer --}}
    <x-footer />
</body>
</html>
