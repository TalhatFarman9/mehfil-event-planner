<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmationMail;
use App\Mail\NewBookingAlertMail;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create()
    {
        return view('bookings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name'  => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'nullable|string|max:30',
            'event_type'   => 'required|string|max:255',
            'event_date'   => 'required|date|after_or_equal:today',
            'guests'       => 'required|integer|min:1',
            'notes'        => 'nullable|string',
        ]);

        $booking = Booking::create($validated);

        try {
            Mail::to($booking->client_email)->send(new BookingConfirmationMail($booking));
            Mail::to(config('mail.admin_address'))->send(new NewBookingAlertMail($booking));
        } catch (\Throwable $e) {
            Log::error('Booking email failed: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')
            ->with('success', 'Booking ho gayi! Confirmation email bhej di gayi hai.');
    }
}