<x-admin-layout>
    <div class="max-w-2xl">
        <h1 class="text-2xl font-semibold mb-6">Edit Booking #{{ $booking->id }}</h1>

        <form method="POST" action="{{ route('admin.bookings.update', $booking) }}"
              class="bg-white rounded-lg shadow p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="client_name" value="Client Name" />
                <x-text-input id="client_name" name="client_name" type="text" class="block mt-1 w-full"
                              :value="old('client_name', $booking->client_name)" required />
                <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="client_email" value="Client Email" />
                <x-text-input id="client_email" name="client_email" type="email" class="block mt-1 w-full"
                              :value="old('client_email', $booking->client_email)" required />
                <x-input-error :messages="$errors->get('client_email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="client_phone" value="Phone" />
                <x-text-input id="client_phone" name="client_phone" type="text" class="block mt-1 w-full"
                              :value="old('client_phone', $booking->client_phone)" />
                <x-input-error :messages="$errors->get('client_phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="event_type" value="Event Type" />
                <x-text-input id="event_type" name="event_type" type="text" class="block mt-1 w-full"
                              :value="old('event_type', $booking->event_type)" required />
                <x-input-error :messages="$errors->get('event_type')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="event_date" value="Event Date" />
                    <x-text-input id="event_date" name="event_date" type="date" class="block mt-1 w-full"
                                  :value="old('event_date', $booking->event_date->format('Y-m-d'))" required />
                    <x-input-error :messages="$errors->get('event_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="guests" value="Guests" />
                    <x-text-input id="guests" name="guests" type="number" min="1" class="block mt-1 w-full"
                                  :value="old('guests', $booking->guests)" required />
                    <x-input-error :messages="$errors->get('guests')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="status" value="Status" />
                <select id="status" name="status" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status', $booking->status) === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="notes" value="Notes" />
                <textarea id="notes" name="notes" rows="3"
                          class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notes', $booking->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button>Update Booking</x-primary-button>
                <a href="{{ route('admin.bookings.index') }}" class="text-gray-600 underline">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>