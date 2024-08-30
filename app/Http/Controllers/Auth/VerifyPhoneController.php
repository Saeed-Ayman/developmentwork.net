<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerifyPhoneController extends Controller
{
    public function verifyCode(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'code' => 'required|integer|digits:6',
            'phone' => 'required|min:11|max:20|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::all()->where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found!',
            ], 404);
        }

        if ($user->verified_at != null) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User is already verified!',
            ], 422);
        }

        if (!$user->verifyCode($request->input('code'))) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid verification code!',
            ], 422);
        }

        \DB::table('verification_phone_codes')
            ->where('phone', $request->phone)
            ->delete();

        $user->verified_at = Carbon::now();
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Phone verified!',
            'token' => $user->createToken('verify-login')->plainTextToken,
            'user' => $user,
        ]);
    }
}
