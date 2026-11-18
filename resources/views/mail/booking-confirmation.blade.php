@extends('mail.layout')

@section('content')
  <h2 style="margin-top:0;">Shukriya, {{ $booking->client_name }}!</h2>
  <p>Aapki booking hamein mil gayi hai. Details ye hain:</p>

  <table width="100%" cellpadding="8" style="border-collapse:collapse;">
    <tr style="background:#f9fafb;"><td><strong>Booking ID</strong></td><td>#{{ $booking->id }}</td></tr>
    <tr><td><strong>Event</strong></td><td>{{ $booking->event_type }}</td></tr>
    <tr style="background:#f9fafb;"><td><strong>Date</strong></td><td>{{ \Carbon\Carbon::parse($booking->event_date)->format('d M Y') }}</td></tr>
    <tr><td><strong>Guests</strong></td><td>{{ $booking->guests }}</td></tr>
    <tr style="background:#f9fafb;"><td><strong>Status</strong></td><td>{{ ucfirst($booking->status) }}</td></tr>
  </table>

  <p style="margin-top:24px;">Hamari team jald aap se rabta karegi.</p>
@endsection