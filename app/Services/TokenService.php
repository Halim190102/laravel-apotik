<?php

namespace App\Services;

use App\Models\RefreshToken;
use Illuminate\Support\Str;

class TokenService
{
    public function respondWithToken($token, $storedToken = null)
    {
        $refreshToken = Str::random(20) . '.' . Str::random(108) . '.' . Str::random(20);

        if ($storedToken) {
            $storedToken->update([
                'token' => $refreshToken,
            ]);
        } else {
            RefreshToken::create([
                'user_id' => auth()->id(),
                'token' => $refreshToken,
            ]);
        }

        return response()->json([
            'message' => 'Login successfully',
            'access_token' => $token,
            'refresh_token' => base64_encode(bin2hex($refreshToken)),
            'token_type' => 'bearer',
        ]);
    }
}
