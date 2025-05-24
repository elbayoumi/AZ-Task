<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RoomRequest;
use App\Models\Room;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    use ApiResponse ,AuthorizesRequests;

    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10); // Default 10 items per page

            $rooms = Room::paginate($perPage);

            return $this->successResponse($rooms, 'Rooms retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve rooms', $e->getMessage(), 500);
        }
    }

    public function store(RoomRequest $request)
    {
        try {
            $this->authorize('create', Room::class);

            $validator = $request->validated();


            $room = Room::create($validator);

            return $this->successResponse($room, 'Room created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create room', $e->getMessage(), 500);
        }
    }

    public function show(string $id)
    {
        try {
            $room = Room::findOrFail($id);
            return $this->successResponse($room, 'Room details retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve room', $e->getMessage(), 500);
        }
    }

    public function update(RoomRequest $request, string $id)
    {
        try {
            $room = Room::findOrFail($id);
            $this->authorize('update', $room);

            $validator = $request->validated();


            $room->update($validator);

            return $this->successResponse($room, 'Room updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update room', $e->getMessage(), 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $room = Room::findOrFail($id);
            $this->authorize('delete', $room);

            $room->delete();

            return $this->successResponse(null, 'Room deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete room', $e->getMessage(), 500);
        }
    }
}
