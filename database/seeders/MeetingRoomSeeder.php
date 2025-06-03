<?php

namespace Database\Seeders;

use App\Models\MeetingRoom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MeetingRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            [
                'name' => 'Meeting Room 1',
                'capacity' => 3,
            ],
            [
                'name' => 'Meeting Room 2',
                'capacity' => 10,
            ],
            [
                'name' => 'Meeting Room 3',
                'capacity' => 15,
            ],
            [
                'name' => 'Meeting Room 4',
                'capacity' => 2,
            ],
            [
                'name' => 'Meeting Room 5',
                'capacity' => 1,
            ],
        ];

        foreach ($records as $record) {
            MeetingRoom::create($record);
        }
    }
}
