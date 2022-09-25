<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Interfaces\RoleRepositoryInterface;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ApiHelpers;

    private RoleRepositoryInterface $role;

    public function __construct(RoleRepositoryInterface $role)
    {
        $this->role = $role;
    }

    public function getRoles(Request $request)
    {
        $validated = $request->validate(['search' => 'present|nullable']);
        $role = $this->role->getRoleService($request->replace($validated));
        return $this->onSuccess($role);
    }
}
