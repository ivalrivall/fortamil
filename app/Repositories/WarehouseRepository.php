<?php

namespace App\Repositories;

use App\Http\Library\ApiHelpers;
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
    use ApiHelpers;
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
        $user = $request->user();
        $data = $this->model->with(['addresses.city' => function($q) {
            $q->select('id','name','meta');
        }, 'addresses.district' => function($q) {
            $q->select('id','name','meta');
        }, 'addresses.province' => function($q) {
            $q->select('id','name','meta');
        }, 'addresses.village' => function ($q) {
            $q->select('id','name','meta');
        }]);

        if ($this->isDropshipper($user)) {
            $whitelist = $this->warehouseWhitelist->select('user_id','warehouse_id')->where('user_id', $user->id)->get();
            if (count($whitelist) > 0) {
                $warehouseWhitelist = collect($whitelist)->pluck('warehouse_id')->unique()->all();
                $data = $data->whereIn('id', $warehouseWhitelist);
            }
        }

        if ($sort) {
            $sort = explode('|', $sort);
            $data = $data->orderBy($sort[0], $sort[1]);
        }

        if ($city) {
            $data = $data->whereHas('addresses', function($q) use ($city) {
                $q->whereHas('city', function($c) use ($city) {
                    $c->where('name', 'like', "%$city%");
                });
            });
        }

        if ($search) {
            $data = $data->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%");
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
                $q->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%")
                ->orWhere('sku', 'like', "%$search%")
                ->orWhereRelation('category', 'name', 'like', "%$search%");
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
        $user = $request->user();
        $data = $this->model;

        if ($this->isDropshipper($user)) {
            $whitelist = $this->warehouseWhitelist->select('user_id','warehouse_id')->where('user_id', $user->id)->get();
            if (count($whitelist) > 0) {
                $warehouseWhitelist = collect($whitelist)->pluck('warehouse_id')->unique()->all();
                $data = $data->whereIn('id', $warehouseWhitelist);
            }
        }

        if ($search) {
            $data = $data->where('name', 'like', "%$search%")->orWhere('address', 'like', "%$search%")
                ->orWhereHas('addresses', function($q) use ($search) {
                    $q->whereHas('city', function($c) use ($search) {
                        $c->where('name', 'like', "%$search%");
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

    /**
     * get warehouse by product list
     * @param array $productIds
     * @return array $warehouseIds
     */
    public function getWarehouseByProductList(array $productIds) : array
    {
        $p = $this->product->select('warehouse_id')->whereIn('id', $productIds)->get();
        $warehouseIds = collect($p)->pluck('warehouse_id')->unique()->all();
        return $warehouseIds;
    }
}
