<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'number',
        'type',
        'price_per_night',
        'status',
    ];
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Define relationship: A room has many bookings.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope to get only available rooms.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
