<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
}
