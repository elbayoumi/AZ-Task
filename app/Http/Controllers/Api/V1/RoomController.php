<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $rooms = Room::all();
        return $this->successResponse($rooms, 'Rooms retrieved successfully');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return $this->errorResponse('Unauthorized - Admins only', null, 403);
        }

        $validator = Validator::make($request->all(), [
            'number' => 'required|string|unique:rooms',
            'type' => 'required|in:single,double,suite',
            'price_per_night' => 'required|numeric|min:0',
            'status' => 'required|in:available,unavailable',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors());
        }

        $room = Room::create($request->all());

        return $this->successResponse($room, 'Room created successfully', 201);
    }

    public function show(string $id)
    {
        $room = Room::findOrFail($id);
        return $this->successResponse($room, 'Room details retrieved');
    }

    public function update(Request $request, string $id)
    {
        if (Auth::user()->role !== 'admin') {
            return $this->errorResponse('Unauthorized - Admins only', null, 403);
        }

        $room = Room::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'number' => 'string|unique:rooms,number,' . $id,
            'type' => 'in:single,double,suite',
            'price_per_night' => 'numeric|min:0',
            'status' => 'in:available,unavailable',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', $validator->errors());
        }

        $room->update($request->all());

        return $this->successResponse($room, 'Room updated successfully');
    }

    public function destroy(string $id)
    {
        if (Auth::user()->role !== 'admin') {
            return $this->errorResponse('Unauthorized - Admins only', null, 403);
        }

        $room = Room::findOrFail($id);
        $room->delete();

        return $this->successResponse(null, 'Room deleted successfully');
    }
}
