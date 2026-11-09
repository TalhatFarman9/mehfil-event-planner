<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
<div class="min-h-screen flex">
    <aside class="w-64 bg-gray-900 text-gray-100 flex flex-col">
        <div class="px-6 py-5 text-xl font-bold border-b border-gray-700">
            Mehfil Admin
        </div>
        <nav class="flex-1 px-4 py-4 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="block px-3 py-2 rounded hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">Dashboard</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-gray-700">Bookings</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-gray-700">Events</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-gray-700">Customers</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="p-4 border-t border-gray-700">
            @csrf
            <button class="w-full text-left px-3 py-2 rounded hover:bg-gray-700">Logout</button>
        </form>
    </aside>
    <main class="flex-1 p-8">
        {{ $slot }}
    </main>
</div>
</body>
</html>