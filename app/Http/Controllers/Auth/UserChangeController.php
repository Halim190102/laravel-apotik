<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ImageRequest;
use App\Services\ImageService;
use App\Services\NameService;
use App\Services\PasswordService;
use Illuminate\Http\Request;

class UserChangeController extends Controller
{
    public function __construct(
        protected PasswordService $passwordService,
        protected NameService $nameService,
        protected ImageService $imageService,
    ) {}
    public function changeImage(ImageRequest $request)
    {
        return $this->imageService->updateImage($request['profilepict']);
    }

    public function changeUserPassword(ChangePasswordRequest $request)
    {
        return $this->passwordService->changePassword($request);
    }

    public function changeName(Request $request)
    {
        return $this->nameService->changeName($request['name']);
    }
}
