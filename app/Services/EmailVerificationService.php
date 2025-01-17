<?php

namespace App\Services;

use App\Models\EmailVerificationToken;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class EmailVerificationService
{
    public function sendVerificationlink(object $user): void
    {
        Notification::send($user, new EmailVerificationNotification($this->generateVerificationLink($user['email'])));
    }

    public function resendLink($email)
    {
        $user = User::where('email', $email)->first();

        $this->sendVerificationlink($user);
        return response()->json([
            'message' => 'Email verification link has been send'
        ]);
    }

    public function verifyToken(string $email, string $token)
    {
        $token = EmailVerificationToken::where('email', $email)->where('token', $token)->first();
        if ($token) {
            if ($token->expired_at >= now()) {
                return $token;
            } else {
                return 'expired';
            }
        } else {
            return 'invalid';
        }
    }

    public function verifyEmail(string $email, string $token)
    {
        $user = User::where('email', $email)->first();

        if ($user->email_verified_at) {
            return 'already_verified';
        }
        $verifiedToken = $this->verifyToken($email, $token);

        if ($verifiedToken === 'expired' || $verifiedToken === 'invalid') {
            return $verifiedToken;
        }

        $user->markEmailAsVerified();
        $verifiedToken->delete();

        return 'success';
    }

    public function generateVerificationLink(string $email): string
    {
        $checkIfTokenExists = EmailVerificationToken::where('email', $email)->first();

        if ($checkIfTokenExists) {
            $checkIfTokenExists->delete();
        }

        $token = Str::uuid();
        $url = config('app.url') . "/api/success=" . $email . "&" . $token;
        $saveToken = EmailVerificationToken::create([
            "email" => $email,
            "token" => $token,
            "expired_at" => now()->addMinutes(60),
        ]);

        if ($saveToken) {
            return $url;
        }

        return '';
    }
}
