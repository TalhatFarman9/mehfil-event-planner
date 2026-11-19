<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Naya Event Book Karein</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Naam</label>
                        <input type="text" name="client_name" value="{{ old('client_name', auth()->user()->name) }}" class="w-full border-gray-300 rounded" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <input type="email" name="client_email" value="{{ old('client_email', auth()->user()->email) }}" class="w-full border-gray-300 rounded" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Phone</label>
                        <input type="text" name="client_phone" value="{{ old('client_phone') }}" class="w-full border-gray-300 rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Event ki qism</label>
                        <select name="event_type" class="w-full border-gray-300 rounded" required>
                            @foreach (['Wedding', 'Mehndi', 'Birthday', 'Corporate', 'Other'] as $type)
                                <option value="{{ $type }}" @selected(old('event_type') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Event ki tareekh</label>
                            <input type="date" name="event_date" value="{{ old('event_date') }}" class="w-full border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Mehmano ki tadad</label>
                            <input type="number" name="guests" min="1" value="{{ old('guests', 1) }}" class="w-full border-gray-300 rounded" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Notes (optional)</label>
                        <textarea name="notes" rows="3" class="w-full border-gray-300 rounded">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="bg-purple-600 text-white px-5 py-2 rounded hover:bg-purple-700">Booking Karein</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>