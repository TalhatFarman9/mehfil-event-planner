<x-app-layout>

<a href="{{ route('bookings.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded">Naya Event Book Karein</a>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Bookings</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p class="mb-4">Khush amdeed, {{ auth()->user()->name }}!</p>
                <p class="text-gray-500">Abhi aap ki koi booking nahi ha. Booking ka feature agle task mein aayega.</p>
            </div>
        </div>
    </div>
</x-app-layout>