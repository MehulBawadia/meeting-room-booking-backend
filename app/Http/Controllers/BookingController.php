<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * The holder for all the available plans.
     *
     * @var array
     */
    protected $planLimits = [
        'free' => 3, // Free Plan: Max 3 bookings per day
        'basic' => 5, // Basic Plan: Max 5 bookings per day
        'advance' => 7, // Advance Plan: Max 7 bookings per day
        'premium' => 10, // Premium Plan: Max 10 bookings per day
    ];

    /**
     * Display the bookings done by the authenticated user.
     * Filter them based on the provided values in the query params.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $filter = $request->query('filter');
        $now = now();

        $bookings = $user->bookings()
            ->when($filter === 'past', function ($filterQuery) use ($now) {
                $filterQuery->where('end_time', '<', $now)
                    ->latest('end_time');
            })
            ->when($filter === 'upcoming', function ($filterQuery) use ($now) {
                $filterQuery->where('end_time', '>=', $now)
                    ->oldest('start_time');
            })
            ->with(['meetingRoom'])
            ->paginate(10);

        return BookingResource::collection($bookings);
    }

    /**
     * Store the booking details.
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function store(BookingRequest $request)
    {
        $user = $request->user();

        $currentPlan = $user->subscription_plan ?? 'free';
        $subscription = Subscription::find($user->subscription_id);
        $dailyLimit = $subscription->booking_per_day;

        $todayBookingsCount = $user->bookings()
            ->whereDate('created_at', today())
            ->count();

        if ($todayBookingsCount >= $dailyLimit) {
            return response()->json([
                'status' => 'failed',
                'message' => "You have reached your daily booking limit ($dailyLimit bookings) for your {$currentPlan} plan. Consider upgrading your subscription.",
                'data' => [],
            ], 403);
        }

        $meetingRoom = MeetingRoom::find($request->meeting_room_id);
        $startTime = Carbon::parse($request->start_time);
        $duration = (int) $request->duration;
        $endTime = $startTime->copy()->addMinutes($duration);
        $requestedMembers = $request->members;

        if ($meetingRoom->capacity < $requestedMembers) {
            return response()->json([
                'status' => 'failed',
                'message' => "The selected meeting room only has capacity for {$meetingRoom->capacity} members. Please choose a different room or reduce the number of members.",
                'data' => [],
            ], 403);
        }

        $conflictingBookings = Booking::query()
            ->where('meeting_room_id', $meetingRoom->id)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime->copy()->subSecond()])
                    ->orWhereBetween('end_time', [$startTime->copy()->addSecond(), $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<', $startTime)
                            ->where('end_time', '>', $endTime);
                    });
            })
            ->count();

        if ($conflictingBookings > 0) {
            return response()->json([
                'status' => 'failed',
                'message' => 'The selected meeting room is no longer available for this time slot. Please choose another time or room.',
                'data' => [],
            ], 403);
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'meeting_room_id' => $request->meeting_room_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'name_of_meeting' => $request->name_of_meeting,
            'number_of_members' => $requestedMembers,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Meeting room has been booked',
            'data' => new BookingResource($booking->load(['user', 'meetingRoom'])),
        ], 201);
    }
}
