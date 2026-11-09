<x-admin-layout>
    <h1 class="text-2xl font-semibold mb-6">Admin Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500">Total Bookings</p>
            <p class="text-3xl font-bold">0</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500">Customers</p>
            <p class="text-3xl font-bold">{{ \App\Models\User::where('role', 'customer')->count() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500">Upcoming Events</p>
            <p class="text-3xl font-bold">0</p>
        </div>
    </div>
</x-admin-layout>