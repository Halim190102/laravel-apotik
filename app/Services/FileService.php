<?php

namespace App\Services;

use Illuminate\Support\Facades\File;


class FileService
{
    public function postFile($file, $category)
    {
        $filePath = 'uploads/drugs/' . strtolower($category) . '/';

        $file_name = strtolower($category) . time() . rand(1, 9) . "." . $file->getClientOriginalExtension();
        $file->move($filePath, $file_name);
        return $filePath . $file_name;
    }

    public function updateFile($file, $category, $databaseFile)
    {
        $databaseFile !== env('DRUGS_DEFAULT') ? File::delete($databaseFile) : null;

        return $this->postFile($file, $category);
    }
}
