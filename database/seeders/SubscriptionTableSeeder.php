<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            [
                'name' => 'free',
                'booking_per_day' => 3,
                'price' => 0.0,
            ],
            [
                'name' => 'basic',
                'booking_per_day' => 5,
                'price' => 100.00,
            ],
            [
                'name' => 'advance',
                'booking_per_day' => 7,
                'price' => 500.00,
            ],
            [
                'name' => 'premium',
                'booking_per_day' => 10,
                'price' => 1000.00,
            ],
        ];

        foreach ($records as $record) {
            Subscription::create($record);
        }
    }
}
