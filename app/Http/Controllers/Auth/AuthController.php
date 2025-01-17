<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Services\EmailVerificationService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\RefreshToken;
use App\Models\User;
use App\Services\ImageService;
use App\Services\TokenService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected EmailVerificationService $emailVerificationService,
        protected TokenService $tokenService,
        protected ImageService $imageService
    ) {}

    public function register(RegisterRequest $request)
    {
        $picture = $request['profilepict'] !== null ? $this->imageService->postImage($request['profilepict']) : env('IMAGE_DEFAULT');

        $user = User::create([
            'name' => $request['name'],
            'role' => 'pasien',
            'profilepict' => $picture,
            'email' => $request['email'],
            'password' => $request['password']
        ]);

        $this->emailVerificationService->sendVerificationlink($user);

        return response()->json([
            'message' => 'Registered successfully',
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email has not been verified'
            ]);
        }

        return $this->tokenService->respondWithToken($token);
    }

    public function profile()
    {
        if (auth()->user()->role === 'admin') {
            $all_profile = User::where('role', 'pasien')->get();

            return response()->json([
                'message' => 'Get data success',
                'data' => auth()->user(),
                'patient' => $all_profile,
            ]);
        } else {
            return response()->json([
                'message' => 'Get data success',
                'data' => auth()->user(),
            ]);
        }
    }



    public function logout(Request $request)
    {
        $storedToken = RefreshToken::where('token', hex2bin(base64_decode($request['refresh_token'])))->first();

        try {
            $storedToken->delete();
            auth()->logout(true);
            return response()->json([
                'message' => 'Logged out successfully'
            ]);
        } catch (\Exception $e) {
            $storedToken->delete();

            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        }
    }

    public function refresh(Request $request)
    {
        // $user_id = auth()->user()->id;
        $storedToken = RefreshToken::where('token', hex2bin(base64_decode($request['refresh_token'])))->first();
        if (!$storedToken) {
            return response()->json([
                'message' => 'Refresh token is missing'
            ]);
        }
        $newAccessToken = auth()->refresh(true);

        return $this->tokenService->respondWithToken($newAccessToken, $storedToken);
    }

    public function revokeAllTokens()
    {
        try {
            RefreshToken::where('user_id', auth()->id())->delete();
            auth()->invalidate(true);

            return response()->json([
                'message' => 'All tokens revoked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to revoke tokens'
            ]);
        }
    }
}
