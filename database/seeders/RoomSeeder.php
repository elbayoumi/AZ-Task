<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        Room::insert([
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'number' => '101',
                'type' => 'single',
                'price_per_night' => 50.00,
                'status' => 'available',
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'number' => '102',
                'type' => 'double',
                'price_per_night' => 75.00,
                'status' => 'available',
            ],
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'number' => '201',
                'type' => 'suite',
                'price_per_night' => 120.00,
                'status' => 'unavailable',
            ],
        ]);
    }
}
