<?php

namespace App\Services;

use App\Models\Update;

class NameService
{

    public function changeName($name)
    {
        $user = auth()->user();

        $update = Update::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name_time' => now()
            ]
        );
        if ($update->name_time > now()) {
            return response()->json(['message' => 'You can update your name after a month']);
        }

        $update->update(['name_time' => now()->addMonth()]);
        $user->update(['name' => $name]);

        return response()->json([
            'message' => 'Name updated successfully',
            'data' => $user
        ]);
    }
}
