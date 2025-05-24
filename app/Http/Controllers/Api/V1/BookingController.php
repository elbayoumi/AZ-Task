<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    use ApiResponse;

    /**
     * Display a list of bookings for the authenticated user.
     */
    public function index()
    {
        $bookings = Booking::with(['room', 'user'])
            ->where('user_id', Auth::id())
            ->get();

        return $this->successResponse($bookings, 'Bookings retrieved successfully');
    }

    /**
     * Create a new booking for a room, checking availability.
     */
    public function store(BookingRequest $request)
    {
        $room = Room::findOrFail($request->room_id);

        if ($room->status !== 'available') {
            return $this->errorResponse('Room is not available', null, 400);
        }

        // Check if the room is available in the requested period
        $hasOverlap = Booking::overlapInPeriod(
            $room->id,
            $request->start_date,
            $request->end_date
        )->exists();

        if ($hasOverlap) {
            return $this->errorResponse('Room is already booked in the requested period.', null, 422);
        }

        // Calculate total price
        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));
        $total_price = $days * $room->price_per_night;

        // Create the booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'room_id' => $room->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $total_price,
        ]);

        return $this->successResponse($booking, 'Booking created successfully', 201);
    }

    /**
     * Show the details of a specific booking.
     */
    public function show(string $id)
    {
        $booking = Booking::with(['room', 'user'])->findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return $this->errorResponse('Unauthorized', null, 403);
        }

        return $this->successResponse($booking, 'Booking retrieved successfully');
    }

    /**
     * Update an existing booking for the authenticated user.
     */
    public function update(BookingRequest $request, string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return $this->errorResponse('Unauthorized', null, 403);
        }

        // Check if the new dates overlap with other bookings for the same room
        $hasOverlap = Booking::overlapInPeriod(
            $booking->room_id,
            $request->start_date,
            $request->end_date,
            $booking->id // exclude current booking
        )->exists();

        if ($hasOverlap) {
            return $this->errorResponse('Room is already booked in the requested period.', null, 422);
        }

        // Calculate the total price based on new dates
        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));
        $total_price = $days * $booking->room->price_per_night;

        $booking->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $total_price,
        ]);

        return $this->successResponse($booking, 'Booking updated successfully');
    }

    /**
     * Delete a booking for the authenticated user.
     */
    public function destroy(string $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return $this->errorResponse('Unauthorized', null, 403);
        }

        $booking->delete();

        return $this->successResponse(null, 'Booking deleted successfully');
    }
}
