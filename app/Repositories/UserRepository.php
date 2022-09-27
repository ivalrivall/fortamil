<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository implements UserRepositoryInterface
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

    /**
     * get all cart item of current user
     */
    public function getCartItems(int $userId): Collection
    {
        $user = $this->findById($userId, ['*'], ['carts.product']);
        if (!$user) {
            throw new Exception('User not found');
        }

        try {
            $cart = $user->carts;
        } catch (Exception $th) {
            throw new Exception('Failed get cart item');
        }

        return $cart;
    }

    /**
     * get paginate user
     */
    public function paginateService($request)
    {
        $per_page = $request->per_page;
        $sort = $request->sort;
        $search = $request->search;
        $status = $request->status;
        $roleId = $request->role_id;

        $data = $this->model->with(['role' => function ($q) {
            $q->select('id','name','slug');
        }])->select('id', 'name', 'email', 'role_id', 'created_at', 'updated_at', 'deleted_at');

        if ($status) {
            if ($status == 'inactive') {
                $data = $data->onlyTrashed();
            }
            if ($status == 'all') {
                $data = $data->withTrashed();
            }
        }

        if (is_array($roleId)) {
            if (!is_null($roleId[0])) {
                $data = $data->whereIn('role_id', $roleId);
            }
        }

        if ($search) {
            $data = $data->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%$search%")
                ->orWhere('email', 'ilike', "%$search%")
                ->orWhereRelation('role', 'name', 'ilike', "%$search%");
            });
        }

        if ($sort) {
            $sort = explode('|', $sort);
            $data = $data->orderBy($sort[0], $sort[1]);
        }

        if (!$per_page) {
            $per_page = 10;
        }

        $data = $data->paginate($per_page);
        $data->makeVisible(['deleted_at']);
        return $data;
    }

    /**
     * disable or enable user by id
     */
    public function disableUserService(array $data)
    {
        if ($data['status']) {
            return $this->restoreById($data['id']);
        }
        return $this->deleteById($data['id']);
    }

    /**
     * delete user permanently
     * @param int $id
     */
    public function deleteUserService(int $id)
    {
        return $this->permanentlyDeleteById($id);
    }

    /**
     * create user
     */
    public function createUser($request)
    {
        $user = $this->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(10)),
            'role_id' => $request->role_id,
            'fcm_token' => null
        ]);
        return $user;
    }

    /**
     * get user by id
     */
    public function getUserById($id)
    {
        try {
            $user = $this->findById($id, ['*'], ['role' => function($q) {
                $q->select('id','name','slug');
            }]);
        } catch (\Throwable $th) {
            $user = $this->findTrashedById($id);
            $role = collect($user->role);
            $user->role = $role;
        }
        return $user;
    }
}
