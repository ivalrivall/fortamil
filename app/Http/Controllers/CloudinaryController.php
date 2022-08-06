<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
// use Cloudinary\Configuration\Configuration;
// use Cloudinary\Api\Upload\UploadApi;
// use Cloudinary\Cloudinary;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Http\Library\ApiHelpers;


class CloudinaryController extends Controller
{
    use ApiHelpers;

    public function upload(Request $request) : JsonResponse
    {
        // $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
        $uploadedFileUrl = Cloudinary::upload($request->file('file')->getRealPath())->getSecurePath();
        return $this->onSuccess($uploadedFileUrl, 'Success upload');
        // return (new UploadApi())->upload($file. $options);
    }
}
