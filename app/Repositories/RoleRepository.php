<?php

namespace App\Repositories;

use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    /**
     * @var Role
     */
    protected $model;

    /**
     * RoleRepository constructor.
     *
     * @param Role $model
     */
    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    /**
     * get role service
     */
    public function getRoleService($request)
    {
        $data = $this->model->select('id','name');

        $search = $request->search;
        if ($search) {
            $data = $data->where('name', 'ilike', "%$search%");
        }
        return $data->orderBy('name', 'ASC')->limit(10)->get();
    }
}
