<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of the bookings.
     */
    public function index()
    {
        $bookings = Booking::with(['room', 'user'])->where('user_id', Auth::id())->get();
        return response()->json($bookings);
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room = Room::findOrFail($request->room_id);

        if ($room->status !== 'available') {
            return response()->json(['message' => 'Room is not available'], 400);
        }

        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));
        $total_price = $days * $room->price_per_night;

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $room->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $total_price,
        ]);

        return response()->json($booking, 201);
    }

    /**
     * Display the specified booking.
     */
    public function show(string $id)
    {
        $booking = Booking::with(['room', 'user'])->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($booking);
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));
        $total_price = $days * $booking->room->price_per_night;

        $booking->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $total_price,
        ]);

        return response()->json($booking);
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }
}
