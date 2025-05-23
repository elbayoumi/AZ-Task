<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'room_id',
        'start_date',
        'end_date',
        'total_price',
    ];

    /**
     * A booking belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A booking belongs to a room.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Automatically calculate total_price before saving.
     */
    protected static function booted()
    {
        static::creating(function ($booking) {
            $days = Carbon::parse($booking->start_date)->diffInDays(Carbon::parse($booking->end_date));
            $booking->total_price = $days * $booking->room->price_per_night;
        });
    }
}
