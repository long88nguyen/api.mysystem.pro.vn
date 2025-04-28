<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFileController extends Controller
{
    public function uploadFile(Request $request)
    {
        try
        {
            if ($file = $request->file('file')) {
                $path = 'uploads/u' . date("/Y/m/d/");
                $extension  = $file->getClientOriginalExtension();
                $image_name = time() .  '.' . $extension;
                $filePath  = $path   . $image_name; // đặt file ảnh trong folder chính / năm / tháng / ngày upload
    
                $result = Storage::disk('s3')->putFileAs($path, $file, $image_name); // upload lên S3
                if($result)
                {
                    $fileSize = $file->getSize();
                    // Lấy URL đầy đủ của tệp trên S3
                    $fileUrl = Storage::disk('s3')->url($filePath);
                    return [
                        'url' => $fileUrl,
                        'size' => $fileSize,
                    ];
                }
                else{
                    return false;
                }
            }
        }
        catch(Exception $e)
        {
            Log::error("Lỗi upload file :" .$e->getMessage());
            return false;
        }
    }
}
