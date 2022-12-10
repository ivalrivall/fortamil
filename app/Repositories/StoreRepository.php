<?php

namespace App\Repositories;

use App\Interfaces\StoreRepositoryInterface;
use App\Models\Store;
use App\Repositories\BaseRepository;
use InvalidArgumentException;

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
        $sort = $request->sort;

        $data = $this->model->with(['latestAddress'])->where('user_id', $request->user()->id);

        if ($sort) {
            $sort = explode('|', $sort);
            $data = $data->orderBy($sort[0], $sort[1]);
        }

        if (!$per_page) {
            $per_page = 10;
        }

        return $data->simplePaginate($per_page);
    }

    /**
     * check store have on going order
     * @param Store $store
     */
    public function checkStoreHaveOnGoingOrder(Store $store)
    {
        $order = $this->model->where('id', $store->id)->whereHas('orders', function($q) {
            $q->whereIn('status', ['waiting','accepted','packing','complaint','return']);
        })->first();
        if ($order) {
            throw new InvalidArgumentException('Masih ada order yang berlangsung di toko ini');
        };
        return true;
    }
}
