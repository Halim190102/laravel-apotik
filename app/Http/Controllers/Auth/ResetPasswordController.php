<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\ResetPasswordService;
use App\Models\User;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function __construct(
        protected ResetPasswordService $resetPasswordService,
    ) {}

    public function sendCodeLink(Request $request)
    {
        $user = User::where('email', $request['email'])->first();

        if ($user) {
            $send = $this->resetPasswordService->sendVerificationlink($user);
            if ($send) {
                return $send;
            }
        } else {
            return response()->json([
                'message' => 'User not found'
            ]);
        }
    }

    public function checkCodeVerify(Request $request)
    {
        return $this->resetPasswordService->verifyCode($request);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->resetPasswordService->resetPass($request);
    }
}
