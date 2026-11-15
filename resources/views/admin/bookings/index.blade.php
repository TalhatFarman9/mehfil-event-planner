<x-admin-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Bookings</h1>
        <span class="text-sm text-gray-500">Total: {{ $bookings->total() }}</span>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-3">
            {{ session('status') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.bookings.index') }}"
          class="bg-white rounded-lg shadow p-4 mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Client name ya date (2026-11-20)"
               class="md:col-span-2 border-gray-300 rounded-md shadow-sm">

        <select name="status" class="border-gray-300 rounded-md shadow-sm">
            <option value="">Sab status</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>

        <input type="date" name="date" value="{{ request('date') }}"
               class="border-gray-300 rounded-md shadow-sm">

        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-md">Filter</button>
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 border rounded-md text-gray-700">Reset</a>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Client</th>
                    <th class="px-4 py-3">Event</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Guests</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($bookings as $booking)
                    <tr>
                        <td class="px-4 py-3">{{ $booking->id }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium">{{ $booking->client_name }}</div>
                            <div class="text-gray-500">{{ $booking->client_email }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $booking->event_type }}</td>
                        <td class="px-4 py-3">{{ $booking->event_date->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ $booking->guests }}</td>
                        <td class="px-4 py-3"><x-status-badge :status="$booking->status" /></td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="{{ route('admin.bookings.edit', $booking) }}"
                               class="text-indigo-600 hover:underline mr-3">Edit</a>

                            <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}"
                                  class="inline"
                                  onsubmit="return confirm('Kya aap waqai ye booking delete karna chahte hain?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">Koi booking nahi mili.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>
</x-admin-layout>