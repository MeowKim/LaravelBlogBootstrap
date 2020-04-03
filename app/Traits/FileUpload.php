<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait FileUpload
{
    public function uploadFile(UploadedFile $uploadedFile, $path = null, $disk = 'public', $user = 'guest')
    {
        $filename = date('YmdHis') . '_' . Str::slug($user) . '_' . Str::random(25);
        $filename .=  '.' . $uploadedFile->getClientOriginalExtension();

        return $uploadedFile->storeAs($path, $filename, $disk);
    }
}
