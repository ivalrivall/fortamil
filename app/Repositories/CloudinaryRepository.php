<?php

namespace App\Repositories;

use App\Interfaces\CloudinaryRepositoryInterface;
use App\Models\User;
use App\Repositories\BaseRepository;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryRepository implements CloudinaryRepositoryInterface
{
    public function upload(array $payload) : string
    {
        $uploadedFileUrl = Cloudinary::upload($payload['file']->getRealPath())->getSecurePath();
        return $uploadedFileUrl;
    }
}
