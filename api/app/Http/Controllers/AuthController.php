<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

use App\Models\User;
use App\Notifications\SendOtpNotification;

class AuthController extends Controller
{
    /**
     * Login endpoint using Sanctum
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'The email or password is incorrect'], 401);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'The email and password is incorrect'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $employeeId = null;
        $userId = $user->id;
        if ($user->role === 'employee') {
            // Assuming there is a relation or field to get employee id from user
            $employee = \App\Models\Employee::where('user_id', $user->id)->first();
            $employeeId = $employee ? $employee->id : null;
        }
        return response()->json([
            'token' => $token,
            'user_role' => $user->role,
            'user_id' => $userId,
            'employee_id' => $employeeId,
            'user_email' => $user->email,
        ]);
    }

    /**
     * Request OTP for password reset
     */
    public function forgotPassword(Request $request)
    {
        // Advanced email validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'it`s fail-1'], 200);
        }

        $email = $request->email;
        // RFC validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => 'it`s fail-2'], 200);
        }
        // MX record check
        $domain = substr(strrchr($email, '@'), 1);
        if (!checkdnsrr($domain, 'MX')) {
            return response()->json(['message' => 'it`s fail-3'], 200);
        }
        // Disposable email check (stub, replace with real check or API)
        if ($this->isDisposableEmail($email)) {
            return response()->json(['message' => 'it`s fail-4'], 200);
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $otp = random_int(100000, 999999);
            $expiresAt = Carbon::now()->addMinutes(10);

            // Remove old OTPs
            DB::table('password_resets')->where('email', $user->email)->delete();

            DB::table('password_resets')->insert([
                'email' => $user->email,
                'otp' => $otp,
                'expires_at' => $expiresAt,
                'failed_attempts' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Queue the notification
            $user->notify((new SendOtpNotification($otp))->onQueue('default'));
        }
        return response()->json([
            'message' => 'If the email is correct, you will receive a verification code',
            'OTP => ' => $otp, // For testing purposes, remove in production
        ], 200);
    }

    // Stub for disposable email check (replace with real package or API)
    private function isDisposableEmail($email)
    {
        // Fallback: Use a more complete disposable domain list
        $disposableDomains = [
            'mailinator.com',
            '10minutemail.com',
            'guerrillamail.com',
            'trashmail.com',
            'tempmail.com',
            'yopmail.com',
            'getnada.com',
            'throwawaymail.com',
            'maildrop.cc',
            'dispostable.com',
            'fakeinbox.com',
            'mintemail.com',
            'mytemp.email',
            'spamgourmet.com',
            'mailnesia.com',
            'sharklasers.com',
            'guerrillamailblock.com',
            'spam4.me',
            'temp-mail.org',
            'tempail.com',
            'moakt.com',
            'emailondeck.com',
            'trashmail.de',
            'mailcatch.com',
            'spambog.com',
            // Add more as needed
        ];
        $domain = strtolower(substr(strrchr($email, '@'), 1));
        return in_array($domain, $disposableDomains);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'The verification code is incorrect or expired'], 422);
        }

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'The verification code is incorrect or expired'], 422);
        }


        // Check OTP and expiry only
        if ($record->otp !== $request->otp || $record->expires_at < now()) {
            return response()->json(['message' => 'The verification code is incorrect or expired'], 422);
        }

        return response()->json(['message' => 'Verification code verified successfully'], 200);
    }

    /**
     * Reset password using OTP
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Please check the entered data'], 422);
        }

        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return response()->json(['message' => 'The verification code is incorrect or expired'], 422);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
            DB::table('password_resets')->where('email', $request->email)->delete();
        }

        return response()->json(['message' => 'Password changed successfully, you can now log in'], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'message' => 'Logged out successfully.'
            ], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
