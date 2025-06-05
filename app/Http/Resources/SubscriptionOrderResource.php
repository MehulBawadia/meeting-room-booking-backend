<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;

class SubscriptionOrderResource extends JsonResource
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
            'subscription_id' => $this->subscription_id,
            'subscription_name' => $this->subscription_name,
            'booking_per_day' => $this->booking_per_day,
            'total_amount' => Number::currency($this->total_amount, 'INR'),
            'payment_gateway_id' => $this->payment_gateway_id,
            'payment_type' => $this->payment_type,
            'payment_done_from' => $this->payment_done_from,
            'transaction_id' => $this->transaction_id,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
