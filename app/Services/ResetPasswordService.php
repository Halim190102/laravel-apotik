<?php

namespace App\Services;

use App\Models\RefreshToken;
use App\Models\ResetPassword;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;

class ResetPasswordService
{
    public function sendVerificationlink(object $user)
    {
        $reset = ResetPassword::firstOrCreate(
            ['email' => $user->email],
            [
                'password_change' => 0,
                'password_time' => now(),
                "code" => '0',
                "reset" => false,
                "expired_at" => now(),
            ]
        );
        if ($reset->password_time <= now()) {
            $reset->update([
                'password_change' => 1,
                'password_time' => now()->addHour()
            ]);
        } else {
            if ($reset->password_change >= 3) {
                return response()->json([
                    'message' => 'You can update your password after an hour'
                ]);
            }
            $reset->increment('password_change');
        }
        Notification::send($user, new ResetPasswordNotification($this->generateVerificationLink($reset)));
        return response()->json([
            'message' => 'Verification code has been send'
        ]);
    }

    public function verifyCode($data)
    {
        $resetRequest = ResetPassword::where('email', $data['email'])->where('code', $data['code'])->first();

        if (!$resetRequest) {
            return response()->json([
                'message' => 'Invalid email or reset code'
            ]);
        }

        if ($resetRequest->expired_at < now()) {
            return response()->json([
                'message' => 'Reset code has expired'
            ]);
        }

        $resetRequest->update(['reset' => true]);

        return response()->json([
            'message' => 'Reset code verified successfully'
        ]);
    }

    public function resetPass($data)
    {
        $reset = ResetPassword::where('email', $data['email'])->first();
        $user = User::where('email', $data['email'])->first();

        if (!$reset || !$user) {
            return response()->json(['message' => 'Invalid email or reset request']);
        }

        if ($reset->reset) {
            $user->update(['password' => bcrypt($data['password'])]);
            $reset->update(['reset' => false]);
            return response()->json(['message' => 'Reset password success']);
        } else {
            return response()->json([
                'message' => 'Please verify the reset code first'
            ]);
        }
    }

    public function generateVerificationLink(ResetPassword $resetPassword): string
    {
        $code = mt_rand(100000, 999999);
        $saveToken =  $resetPassword->update([
            "code" => $code,
            "expired_at" => now()->addHour(),
        ]);

        if ($saveToken) {
            return $code;
        }

        return '';
    }
}
