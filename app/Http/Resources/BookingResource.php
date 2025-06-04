<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'meeting_room_id' => $this->meeting_room_id,
            'name_of_meeting' => $this->name_of_meeting,
            'start_time' => $this->start_time->format('d-M-Y h:i a'),
            'end_time' => $this->end_time->format('d-M-Y h:i a'),
            'number_of_members' => $this->number_of_members,
            'duration' => $this->start_time->diffInMinutes($this->end_time),

            'user' => new UserResource($this->whenLoaded('user')),
            'meeting_room' => new MeetingRoomResource($this->whenLoaded('meetingRoom')),
        ];
    }
}
