<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register the user in the application.
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();

        try {
            $password = bcrypt($request->input('password'));
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $password,
                'subscription_plan' => 'free',
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully.',
                'user' => new UserResource($user),
            ], 201);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            logger()->error($e->getTraceAsString());

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Could not register.',
            ], 500);
        }
    }

    /**
     * Login the user. Lock the user account for 24 hours
     * if 3 invalid/incorrect attempts are made.
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $user = User::query()
            ->where('email', $request->email)
            ->first();

        if ($user && $user->locked_until && $user->locked_until > now()) {
            $lockedUntil = $user->locked_until;

            return response()->json([
                'status' => 'failed',
                'message' => "Your account has been locked for 24 hours. Access your account again after {$lockedUntil->format('F d, h:i A')}",
            ], 423);
        }

        $totalAttempts = 3;
        $availeblAttempts = $totalAttempts - ($user->failed_attempts + 1);

        if (! Hash::check($request->input('password'), $user->password)) {
            $user->update([
                'failed_attempts' => ++$user->failed_attempts ?? 1,
            ]);

            if ($user->failed_attempts === 3) {
                $lockedUntil = now()->addHours(24);
                $user->update([
                    'locked_until' => $lockedUntil,
                ]);

                return response()->json([
                    'status' => 'failed',
                    'message' => "Your account has been locked for 24 hours. Access your account again after {$lockedUntil->format('F d, h:i A')}",
                ], 423);
            }

            return response()->json([
                'status' => 'failed',
                'message' => "Invalid credentials. You have {$availeblAttempts} attempts left.",
            ], 401);
        }

        $user->update([
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User successfully logged in.',
            'access_token' => $user->createToken('API-Token')->plainTextToken,
            'user' => new UserResource($user->fresh()),
        ], 200);
    }

    /**
     * Logout the user. Delete the current access token.
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User successfully logged out.',
        ], 200);
    }
}
