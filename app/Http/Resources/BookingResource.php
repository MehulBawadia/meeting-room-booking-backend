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
            'start_time' => $this->start_time->toDateTimeString(),
            'end_time' => $this->end_time->toDateTimeString(),
            'number_of_members' => $this->number_of_members,

            'user' => new UserResource($this->whenLoaded('user')),
            'meeting_room' => new MeetingRoomResource($this->whenLoaded('meetingRoom')),
        ];
    }
}
