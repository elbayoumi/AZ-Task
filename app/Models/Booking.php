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
     * Scope to check if a booking overlaps with the given date range.
     */

    public function scopeOverlapInPeriod($query, $roomId, $startDate, $endDate, $excludeId = null)
    {
        return $query->where('room_id', $roomId)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                    });
            });
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
