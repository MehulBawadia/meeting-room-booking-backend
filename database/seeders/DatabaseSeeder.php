<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(MeetingRoomSeeder::class);
        $this->call(SubscriptionTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(BookingTableSeeder::class);
    }
}
