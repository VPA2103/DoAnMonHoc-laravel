<?php

namespace App\Http\Controllers;

use Cloudinary\Cloudinary as CloudinaryAlias;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'Không tìm thấy file'], 400);
        }

        $cloudinary = new CloudinaryAlias([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_KEY'),
                'api_secret' => env('CLOUDINARY_SECRET'),
            ],
        ]);

        $result = $cloudinary->uploadApi()->upload(
            $request->file('image')->getRealPath(),
            ['folder' => 'bep-viet']
        );

        return response()->json([
            'url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ]);
    }
}