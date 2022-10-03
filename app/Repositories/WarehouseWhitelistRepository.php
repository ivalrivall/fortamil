<?php

namespace App\Repositories;

use App\Interfaces\WarehouseWhitelistRepositoryInterface;
use App\Models\WarehouseWhitelist;
use App\Repositories\BaseRepository;

class WarehouseWhitelistRepository extends BaseRepository implements WarehouseWhitelistRepositoryInterface
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
    public function __construct(WarehouseWhitelist $model)
    {
        $this->model = $model;
    }
}
