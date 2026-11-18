@extends('mail.layout')

@section('content')
  <h2 style="margin-top:0;">Nayi booking aayi hai</h2>

  <table width="100%" cellpadding="8" style="border-collapse:collapse;">
    <tr style="background:#f9fafb;"><td><strong>Booking ID</strong></td><td>#{{ $booking->id }}</td></tr>
    <tr><td><strong>Client</strong></td><td>{{ $booking->client_name }} ({{ $booking->client_email }})</td></tr>
    <tr style="background:#f9fafb;"><td><strong>Phone</strong></td><td>{{ $booking->client_phone ?? '-' }}</td></tr>
    <tr><td><strong>Event</strong></td><td>{{ $booking->event_type }}</td></tr>
    <tr style="background:#f9fafb;"><td><strong>Date</strong></td><td>{{ \Carbon\Carbon::parse($booking->event_date)->format('d M Y') }}</td></tr>
    <tr><td><strong>Guests</strong></td><td>{{ $booking->guests }}</td></tr>
    @if ($booking->notes)
      <tr style="background:#f9fafb;"><td><strong>Notes</strong></td><td>{{ $booking->notes }}</td></tr>
    @endif
  </table>

  <p style="margin-top:24px;">
    <a href="{{ route('admin.bookings.index') }}" style="background:#7c3aed;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none;">Admin panel me dekhein</a>
  </p>
@endsection