<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'phone' => ['required', 'string', 'min:9', 'max:20'],
            'password' => ['required', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Auth::attempt($validator->validate())) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = Auth::user();

        if ($user->verified_at === null) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Your phone number is not verified.'
            ], 409);
        }

        return response()->json([
            'status' => 'success',
            'token' => $user->createToken('register-token')->plainTextToken,
            'user' => $user,
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'logout successfully successfully.',
        ]);
    }
}
