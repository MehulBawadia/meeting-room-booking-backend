<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Models\SubscriptionOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Buy the subscription.
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function buy(Request $request)
    {
        $user = $request->user();
        $subsriptionId = $request->input('subscription_id');

        $subscription = Subscription::find($subsriptionId);
        if (! $subscription) {
            return response()->json([
                'status' => 'failed',
                'message' => 'The subscription could not found.',
                'data' => [],
            ], 404);
        }

        DB::beginTransaction();

        try {
            $order = SubscriptionOrder::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'subscription_id' => $subscription->id,
                'subscription_name' => $subscription->name,
                'total_amount' => $subscription->price,
                'payment_gateway_id' => 0,
                'payment_type' => null,
                'payment_done_from' => null,
                'transaction_id' => null,
                'payment_gateway_response' => null,
            ]);

            $user->update([
                'subscription_id' => $subscription->id,
                'subscription_plan' => $subscription->name,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Subscription successfully purchased.',
                'data' => $order,
            ], 201);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            logger()->error($e->getTraceAsString());

            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => 'Could not purchase subscription.',
                'data' => [],
            ], 500);
        }
    }
}
