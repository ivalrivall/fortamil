<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface RoleRepositoryInterface extends BaseRepositoryInterface
{
    public function getRoleService($request);
}
