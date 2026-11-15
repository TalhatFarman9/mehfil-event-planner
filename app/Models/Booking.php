<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'confirmed', 'cancelled'];

    protected $fillable = [
        'client_name',
        'client_email',
        'client_phone',
        'event_type',
        'event_date',
        'guests',
        'status',
        'notes',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];
}