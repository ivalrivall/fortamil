<?php

namespace App\Interfaces;

interface CloudinaryRepositoryInterface
{
    public function upload(array $payload): string;
    public function delete(string $fileName);
}
