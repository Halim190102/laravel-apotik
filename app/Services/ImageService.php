<?php

namespace App\Services;

use Illuminate\Support\Facades\File;


class ImageService
{
    public function postImage($profilepict)
    {
        $path =  'uploads/profile/';

        $nama_gambar = 'profile-' . time() . rand(1, 9) . "." . $profilepict->getClientOriginalExtension();
        $profilepict->move($path, $nama_gambar);
        return $path . $nama_gambar;
    }

    public function updateImage($profilepict)
    {
        $user = auth()->user();

        $user->profilepict !== env('IMAGE_DEFAULT') ? File::delete($user->profilepict) : null;
        $user->profilepict = $this->postImage($profilepict);
        $user->save();

        return response()->json([
            'message' => 'Profile image updated successfully',
            'data' => $user
        ]);
    }
}
