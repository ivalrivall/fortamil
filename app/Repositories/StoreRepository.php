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

    public function paginate($request)
    {
        $per_page = $request->per_page;
        $store = $this->model->where('user_id', $request->user()->id);
        if (!$request->per_page) {
            $per_page = 10;
        }
        return $store->simplePaginate($per_page);
    }
}
