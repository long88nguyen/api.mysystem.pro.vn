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
        try {
            $request->validate([
                'file' => 'required|file|max:20480', // 20MB
            ]);
        
            if (!$request->hasFile('file')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No file uploaded!',
                ]);
            }
        
            $file = $request->file('file');
        
            if (!$file->isValid()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid file!',
                ]);
            }
        
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = Storage::disk('s3')->putFileAs('uploads', $file, $filename, 'public');
        
            if ($path) {
                $url = Storage::disk('s3')->url($path);
                return response()->json([
                    'status' => true,
                    'url' => $url,
                ]);
            }
        
            return response()->json([
                'status' => false,
                'message' => 'Failed to upload file to S3.',
            ]);
        } catch (\Exception $e) {
            Log::error('Upload S3 error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
        
    }
}
