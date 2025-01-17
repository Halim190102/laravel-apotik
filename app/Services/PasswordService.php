<?php

namespace App\Services;

use App\Models\Update;
use Illuminate\Support\Facades\Hash;

class PasswordService
{

    private function validateCurrentPassword($current_password)
    {
        if (!!Hash::check($current_password, auth()->user()->password)) {
            return response()->json([
                'message' => 'Password did not match the current password'
            ]);
        }
    }

    public function changePassword($data)
    {
        $user = auth()->user();

        $update = Update::firstOrCreate(
            ['user_id' => $user->id],
            [
                'password_change' => 0,
                'password_time' => now()
            ]
        );

        if ($update->password_time <= now()) {
            $validationResponse = $this->validateCurrentPassword($data['current_password']);
            if ($validationResponse) {
                return $validationResponse;
            }
            $update->update([
                'password_change' => 1,
                'password_time' => now()->addHour()
            ]);
        } else {
            $validationResponse = $this->validateCurrentPassword($data['current_password']);
            if ($validationResponse) {
                return $validationResponse;
            }
            if ($update->password_change >= 3) {
                return response()->json([
                    'message' => 'You can update your password after an hour'
                ]);
            }
            $update->increment('password_change');
        }

        $user->update(['password' => bcrypt($data['new_password'])]);

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }
}
