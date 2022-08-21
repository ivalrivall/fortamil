<?php

namespace App\Repositories;

use App\Interfaces\WarehouseRepositoryInterface;
use App\Models\Product;
use App\Models\Warehouse;
use App\Repositories\BaseRepository;

class WarehouseRepository extends BaseRepository implements WarehouseRepositoryInterface
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
    public function __construct(Warehouse $model)
    {
        $this->model = $model;
        $this->product = new Product();
    }

    /**
     * paginate warehouse
     */
    public function paginate($request)
    {
        $per_page = $request->per_page;
        $sort = $request->sort;

        $data = $this->model;

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
     * get product for current warehouse paginate
     */
    public function getProductPaginate($request, int $warehouseId)
    {
        $per_page = $request->per_page;
        $sort = $request->sort;
        $search = $request->search;

        $data = $this->product
            ->exclude(['deleted_at','created_at','updated_at','warehouse_id'])
            ->where('warehouse_id', $warehouseId)
            ->with(['category' => function ($q) {
                $q->select('id','name','slug','picture');
            }]);

        if ($search) {
            $data->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%$search%")
                ->orWhere('description', 'ilike', "%$search%")
                ->orWhere('sku', 'ilike', "%$search%")
                ->orWhereRelation('category', 'name', 'ilike', "%$search%");
            });
        }

        if ($sort) {
            $sort = explode('|', $sort);
            $data = $data->orderBy($sort[0], $sort[1]);
        }

        if (!$per_page) {
            $per_page = 10;
        }

        return $data->simplePaginate($per_page);
    }
}
