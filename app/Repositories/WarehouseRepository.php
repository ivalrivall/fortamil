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
    protected $product;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Warehouse $model, Product $product)
    {
        $this->model = $model;
        $this->product = $product;
    }

    /**
     * paginate warehouse
     */
    public function paginate($request)
    {
        $per_page = $request->per_page;
        $sort = $request->sort;
        $city = $request->city;

        $data = $this->model->with(['addresses.city' => function($q) {
            $q->select('id','name','meta');
        }, 'addresses.district' => function($q) {
            $q->select('id','name','meta');
        }, 'addresses.province' => function($q) {
            $q->select('id','name','meta');
        }, 'addresses.village' => function ($q) {
            $q->select('id','name','meta');
        }]);

        if ($sort) {
            $sort = explode('|', $sort);
            $data = $data->orderBy($sort[0], $sort[1]);
        }

        if ($city) {
            $data = $data->whereHas('addresses', function($q) use ($city) {
                $q->whereHas('city', function($c) use ($city) {
                    $c->where('name', 'ilike', "%$city%");
                });
            });
        }

        if (!$per_page) {
            $per_page = 10;
        }

        $data = $data->simplePaginate($per_page);
        $data->makeVisible(['deleted_at']);
        return $data;
    }

    /**
     * get product for current warehouse paginate
     */
    public function getProductPaginate($request, int $warehouseId)
    {
        $per_page = $request->per_page;
        $sort = $request->sort;
        $search = $request->search;
        $status = $request->status;

        $data = $this->product
            ->with(['category' => function ($q) {
                $q->select('id','name','slug','picture');
            }, 'pictures' => function ($q) {
                $q->select('path', 'product_id');
            }]);

        if ($status) {
            if ($status == 'inactive') {
                $data = $data->onlyTrashed();
            }
            if ($status == 'all') {
                $data = $data->withTrashed();
            }
        }

        if ($warehouseId) {
            $data = $data->where('warehouse_id', $warehouseId);
        };

        if ($search) {
            $data = $data->where(function($q) use ($search) {
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

        return $data->simplePaginate($per_page)->makeVisible(['deleted_at']);
    }

    /**
     * get warehouse by search query
     */
    public function searchWarehouse($request)
    {
        $search = $request->search;
        $data = $this->model;

        if ($search) {
            $data = $data->where('name', 'ilike', "%$search%")->orWhere('address', 'ilike', "%$search%")
                ->orWhereHas('addresses', function($q) use ($search) {
                    $q->whereHas('city', function($c) use ($search) {
                        $c->where('name', 'ilike', "%$search%");
                    });
                });
        }

        return $data->orderBy('name', 'ASC')->limit(10)->get();
    }
}
