<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getCartItems(int $userId);
    public function paginateService($request);
    public function disableUserService(array $data);
    public function deleteUserService(int $id);
    public function createUser($request);
    public function getUserById($id);
}
