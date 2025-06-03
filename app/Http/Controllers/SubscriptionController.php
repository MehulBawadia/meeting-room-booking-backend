<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Get the list of all the subscriptions.
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $subscriptions = Subscription::query()
            ->oldest('price')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'The subscriptions successfully fetched.',
            'data' => SubscriptionResource::collection($subscriptions),
        ]);
    }
}
