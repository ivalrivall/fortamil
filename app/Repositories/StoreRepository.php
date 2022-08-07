<?php

namespace App\Repositories;

use App\Interfaces\StoreRepositoryInterface;
use App\Models\Store;
use App\Repositories\BaseRepository;

class StoreRepository extends BaseRepository implements StoreRepositoryInterface
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
    public function __construct(Store $model)
    {
        $this->model = $model;
    }
}
