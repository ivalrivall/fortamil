<?php

namespace App\Repositories;

use App\Interfaces\CloudinaryRepositoryInterface;
use App\Models\User;
use App\Repositories\BaseRepository;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryRepository extends BaseRepository implements CloudinaryRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function upload(array $payload) : string
    {
        $uploadedFileUrl = Cloudinary::upload($payload['file']->getRealPath())->getSecurePath();
        return $uploadedFileUrl;
    }
}
