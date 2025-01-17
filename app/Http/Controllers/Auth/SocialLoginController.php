<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialLogin;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function toProvider($driver)
    {
        return response()->json([
            'url' => Socialite::driver($driver)->stateless()->redirect()->getTargetUrl()
        ]);
    }

    public function handleCallback($driver)
    {
        $socialUser = Socialite::driver($driver)->stateless()->user();

        $userAccount = SocialLogin::firstOrNew([
            'provider' => $driver,
            'provider_id' => $socialUser->getId(),
        ]);

        $dbUser = $userAccount->user ?? User::firstOrNew(['email' => $socialUser->getEmail()]);

        if (!$dbUser->exists) {
            $dbUser->fill([
                'name' => $socialUser->getName(),
                'profilepict' => $socialUser->getAvatar(),
                'role' => 'student',
                'email_verified_at' => now(),
            ])->save();
        }

        if (is_null($dbUser->email_verified_at)) {
            $dbUser->markEmailAsVerified();
        }

        $userAccount->user()->associate($dbUser)->save();

        auth()->login($dbUser);

        $token = $dbUser->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'access_token' => $token,
        ]);
    }
}
