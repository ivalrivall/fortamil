<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface CloudinaryRepositoryInterface extends BaseRepositoryInterface
{
    public function upload(array $payload): string;
}
