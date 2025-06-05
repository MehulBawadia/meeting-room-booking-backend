<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class BookingTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $users = User::inRandomOrder()->take(10)->get();
        $rooms = MeetingRoom::all();

        $allTimeSlots = [];

        foreach ($rooms as $room) {
            $roomSlots = [];

            while (count($roomSlots) < 100) {
                $isFuture = $faker->boolean();

                $baseDay = $isFuture
                    ? now()->addDays(rand(0, 6))
                    : now()->subDays(rand(0, 6));

                $hour = rand(8, 17);
                $minute = $faker->randomElement([0, 15, 30, 45]);
                $start = $baseDay->copy()->setTime($hour, $minute);

                $duration = $faker->randomElement([30, 60, 90]);
                $end = $start->copy()->addMinutes($duration);

                if ($end->day !== $start->day) {
                    continue;
                }

                $conflict = false;
                foreach ($roomSlots as [$existingStart, $existingEnd]) {
                    if (
                        ($start < $existingEnd) &&
                        ($end > $existingStart)
                    ) {
                        $conflict = true;
                        break;
                    }
                }

                if ($conflict) {
                    continue;
                }

                $roomSlots[] = [$start, $end];

                $allTimeSlots[] = [
                    'meeting_room_id' => $room->id,
                    'start_time' => $start,
                    'end_time' => $end,
                ];
            }
        }

        shuffle($allTimeSlots);

        $bookingInserts = [];
        $slotIndex = 0;

        foreach ($users as $user) {
            $numBookings = rand(40, 50);

            for ($i = 0; $i < $numBookings; $i++) {
                if (! isset($allTimeSlots[$slotIndex])) {
                    break 2;
                }

                $slot = $allTimeSlots[$slotIndex++];
                $room = $rooms->firstWhere('id', $slot['meeting_room_id']);
                $maxMembers = $room->capacity;
                $members = rand(1, $maxMembers);

                $bookingInserts[] = [
                    'user_id' => $user->id,
                    'meeting_room_id' => $slot['meeting_room_id'],
                    'name_of_meeting' => ucwords($faker->words(3, true)),
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'number_of_members' => $members,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Booking::insert($bookingInserts);
    }
}
