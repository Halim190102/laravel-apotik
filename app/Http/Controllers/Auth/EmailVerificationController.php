<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EmailVerificationService;
use App\Models\User;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function __construct(
        protected EmailVerificationService $emailVerificationService,
    ) {}

    public function verifiedSuccess($email, $token)
    {
        $verificationStatus = $this->emailVerificationService->verifyEmail($email, $token);

        switch ($verificationStatus) {
            case 'success':
                return view('verify')->with(['message' => 'success']);
                break;
            case 'invalid':
                return view('verify')->with(['message' => 'invalid']);
                break;
            case 'already_verified':
                return view('verify')->with([
                    'message' => 'already_verified',
                ]);
                break;
            case 'expired':
                return view('verify')->with([
                    'message' => 'token_expired',
                    'email' => $email,
                ]);
                break;
            default:
                return view('verify')->with('message', 'error');
        }
    }

    public function resendEmailVerificationLink(Request $request)
    {
        return $this->emailVerificationService->resendLink($request['email']);
    }

    public function checkVerify(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user && $user->email_verified_at) {
            return response()->json([
                'message' => 'Email has already been verified'
            ]);
        } else {
            return response()->json([
                'message' => 'Email has not been verified'
            ]);
        }
    }
}
