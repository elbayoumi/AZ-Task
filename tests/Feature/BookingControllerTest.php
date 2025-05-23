<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Carbon\Carbon;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user and authenticate
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        // Create available room
        $this->room = Room::factory()->create([
            'status' => 'available',
            'price_per_night' => 100,
        ]);
    }

    public function test_user_can_create_booking()
    {
        $response = $this->postJson('/api/v1/bookings', [
            'room_id' => $this->room->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_user_can_view_own_booking()
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'total_price' => 200,
        ]);

        $response = $this->getJson("/api/v1/bookings/{$booking->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $booking->id]);
    }

    public function test_user_cannot_view_other_users_booking()
    {
        $anotherUser = User::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $anotherUser->id,
            'room_id' => $this->room->id,
        ]);

        $response = $this->getJson("/api/v1/bookings/{$booking->id}");
        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_booking()
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
        ]);

        $response = $this->deleteJson("/api/v1/bookings/{$booking->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }
}
