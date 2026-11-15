<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim($request->input('q'));

                $query->where(function ($q) use ($term) {
                    $q->where('client_name', 'like', "%{$term}%")
                      ->orWhere('event_date', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('event_date', $request->input('date')))
            ->orderBy('event_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'statuses' => Booking::STATUSES,
        ]);
    }

    public function edit(Booking $booking): View
    {
        return view('admin.bookings.edit', [
            'booking' => $booking,
            'statuses' => Booking::STATUSES,
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:30'],
            'event_type' => ['required', 'string', 'max:100'],
            'event_date' => ['required', 'date'],
            'guests' => ['required', 'integer', 'min:1', 'max:5000'],
            'status' => ['required', Rule::in(Booking::STATUSES)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking->update($data);

        return redirect()
            ->route('admin.bookings.index')
            ->with('status', 'Booking update ho gayi.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()
            ->route('admin.bookings.index')
            ->with('status', 'Booking delete ho gayi.');
    }
}