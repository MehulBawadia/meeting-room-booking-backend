<?php

namespace App\Http\Controllers;

use App\Http\Resources\MeetingRoomResource;
use App\Models\Booking;
use App\Models\MeetingRoom;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoomAvailabilityController extends Controller
{
    /**
     * Get the available rooms based on the user provided filters.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $startTime = Carbon::parse($request->start_time);
        $duration = (int) $request->duration;
        $endTime = $startTime->copy()->addMinutes($duration);
        $requiredCapacity = $request->members;

        $potentialRooms = MeetingRoom::query()
            ->where('capacity', '>=', $requiredCapacity)
            ->get();
        if ($potentialRooms->isEmpty()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'The meeting rooms are not available.',
                'data' => [],
            ]);
        }

        $availableRooms = collect();
        foreach ($potentialRooms as $room) {
            $conflictingBookings = Booking::query()
                ->where('meeting_room_id', $room->id)
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->whereBetween('start_time', [$startTime, $endTime->subSecond()])
                        ->orWhereBetween('end_time', [$startTime->addSecond(), $endTime])
                        ->orWhere(function ($query) use ($startTime, $endTime) {
                            $query->where('start_time', '<', $startTime)
                                ->where('end_time', '>', $endTime);
                        });
                })
                ->count();

            if ($conflictingBookings === 0) {
                $availableRooms->push($room);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Meeting rooms successfully fetched.',
            'data' => MeetingRoomResource::collection($availableRooms),
        ]);
    }
}
