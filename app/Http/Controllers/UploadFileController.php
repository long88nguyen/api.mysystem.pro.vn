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
            // Validate the request
            $request->validate([
                'file' => 'required|file|max:10240', // Max 10MB
            ]);
    
            // Check if file exists in the request
            if (!$request->hasFile('file')) {
                return response()->json([
                    'status' => false,
                    'message' => 'No file uploaded!',
                ]);
            }
    
            $file = $request->file('file');
            
            // Check if file is valid
            if (!$file->isValid()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid file!',
                ]);
            }
    
            $directory = 'uploads';
            
            // Generate a unique filename
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Full path including directory
            $filePath = $directory . '/' . $filename;
            
            // Upload the file to S3 using stream instead of file_get_contents
            $path = Storage::disk('s3')->putFileAs($directory, $file, $filename);
            
            if ($path) {
                // Return the full URL to the file
                $url = Storage::disk('s3')->url($path);
                return response()->json([
                    'status' => true,
                    'url' => $url,
                ]);
            }
            
            return response()->json([
                'status' => false,
                'message' => 'Upload File không thành công!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
        
    }
}
