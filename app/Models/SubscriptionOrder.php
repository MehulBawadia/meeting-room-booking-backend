<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'subscription_id',
        'subscription_name',
        'booking_per_day',
        'total_amount',
        'payment_gateway_id',
        'payment_type',
        'payment_done_from',
        'transaction_id',
        'payment_gateway_response',
    ];

    /**
     * Subscription order belongs to a single user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, SubscriptionOrder>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
