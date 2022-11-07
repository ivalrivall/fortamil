<?php

namespace App\Repositories;

use App\Interfaces\CloudinaryRepositoryInterface;
use App\Models\User;
use App\Repositories\BaseRepository;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryRepository implements CloudinaryRepositoryInterface
{
    /**
     * upload asset to cloudinary
     * @param array $payload
     * @return string url file
     */
    public function upload(array $payload) : string
    {
        $options = [];
        if (isset($payload['folder']) && $payload['folder'] !== '') {
            $options = [
                'folder' => $payload['folder']
            ];
        }
        $uploadedFileUrl = Cloudinary::upload($payload['file']->getRealPath(), $options)->getSecurePath();
        return $uploadedFileUrl;
    }

    /**
     * delete asset on cloudinary
     * @param string $fileName
     */
    public function delete(string $fileName)
    {
        $uploadedFileUrl = Cloudinary::destroy($fileName);
        return $uploadedFileUrl;
    }
}
