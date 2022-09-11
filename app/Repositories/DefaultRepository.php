<?php

namespace App\Repositories;

use App\Interfaces\DefaultRepositoryInterface;
use App\Models\Order;
use App\Repositories\BaseRepository;

class DefaultRepository extends BaseRepository implements DefaultRepositoryInterface
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
    public function __construct(Order $model)
    {
        $this->model = $model;
    }
}
