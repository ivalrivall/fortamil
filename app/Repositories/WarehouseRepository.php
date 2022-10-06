<?php

namespace App\Repositories;

use App\Interfaces\AddressRepositoryInterface;
use App\Interfaces\CloudinaryRepositoryInterface;
use App\Interfaces\WarehouseRepositoryInterface;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseWhitelist;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class WarehouseRepository extends BaseRepository implements WarehouseRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    protected $product;
    protected $cloud;
    protected $address;
    protected $warehouseWhitelist;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(
        Warehouse $model,
        Product $product,
        WarehouseWhitelist $warehouseWhitelist,
        CloudinaryRepositoryInterface $cloud,
        AddressRepositoryInterface $address
    )
    {
        $this->model = $model;
        $this->product = $product;
        $this->cloud = $cloud;
        $this->address = $address;
        $this->warehouseWhitelist = $warehouseWhitelist;
    }

    /**
     * paginate warehouse
     */
    public function paginate($request)
    {
        $per_page = $request->per_page;
        $sort = $request->sort;
        $city = $request->city;
        $search = $request->search;

        $whitelist = $this->warehouseWhitelist->select('user_id','warehouse_id')->get();

        $userWarehouseWhitelist = collect($whitelist)->pluck('user_id')->all();
        $warehouseWhitelist = collect($whitelist)->pluck('user_id')->all();

        if (in_array($request->user()->id, $userWarehouseWhitelist)) {
            $data = $this->model->with(['addresses.city' => function($q) {
                $q->select('id','name','meta');
            }, 'addresses.district' => function($q) {
                $q->select('id','name','meta');
            }, 'addresses.province' => function($q) {
                $q->select('id','name','meta');
            }, 'addresses.village' => function ($q) {
                $q->select('id','name','meta');
            }])->whereIn('id', $warehouseWhitelist);
        } else {
            $data = $this->model->with(['addresses.city' => function($q) {
                $q->select('id','name','meta');
            }, 'addresses.district' => function($q) {
                $q->select('id','name','meta');
            }, 'addresses.province' => function($q) {
                $q->select('id','name','meta');
            }, 'addresses.village' => function ($q) {
                $q->select('id','name','meta');
            }]);
        }


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

        if ($search) {
            $data = $data->where(function($q) use ($search) {
                $q->where('name', 'ilike', "%$search%")
                    ->orWhere('address', 'ilike', "%$search%");
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

        $data = $data->paginate($per_page);
        $data->makeVisible(['deleted_at']);
        return $data;
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

    /**
     * edit warehouse
     */
    public function editService($request, $warehouseId)
    {
        try {
            $warehouse = $this->findById($warehouseId);
        } catch (Exception $th) {
            throw new InvalidArgumentException('warehouse not found');
        }

        DB::beginTransaction();
        try {
            $address = $this->address->findById($request->address_id);
            $address->makeVisible(['addressable_type','addressable_id']);
        } catch (Exception $th) {
            DB::rollBack();
            throw new InvalidArgumentException('Selected address not found');
        }
        DB::rollBack();
        if ($address->addressable_type == 'App\Models\Warehouse' && $address->addressable_id == $warehouseId) {
            try {
                $address->is_primary = false;
                $address->title = $request->address_title;
                $address->recipient = $request->address_recipient;
                $address->phone_recipient = $request->address_phone_recipient;
                $address->city_id = $request->city_id;
                $address->district_id = $request->district_id;
                $address->province_id = $request->province_id;
                $address->village_id = $request->village_id;
                $address->postal_code = $request->postal_code;
                $address->save();
            } catch (Exception $th) {
                Log::error($th->getMessage());
                DB::rollBack();
                throw new InvalidArgumentException('Failed update address');
            }
        } else {
            DB::rollBack();
            throw new InvalidArgumentException('Address is not belongs to warehouse');
        }

        if ($request->file('picture')) {
            $path = pathinfo($warehouse->picture);
            $currentPictureName = $path['filename'];
            $pictureUrl = $this->cloud->upload(['file' => $request->file('picture')]);
            $this->cloud->delete($currentPictureName);
        } else {
            $pictureUrl = $warehouse->picture;
        }

        try {
            $warehouse = $warehouse->update([
                'name' => $request->name,
                'picture' => $pictureUrl,
                'address' => $request->address,
            ]);
        } catch (Exception $th) {
            Log::error($th->getMessage());
            DB::rollBack();
            throw new InvalidArgumentException('Failed update warehouse');
        }

        DB::commit();
    }
}
