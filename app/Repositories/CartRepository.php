<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\Cart;
use App\Repositories\BaseRepository;

class CartRepository extends BaseRepository implements UserRepositoryInterface
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
    public function __construct(Cart $model)
    {
        $this->model = $model;
    }
}
