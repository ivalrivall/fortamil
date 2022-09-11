<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\BasePaginateRequest;
use App\Http\Requests\Warehouse\WarehouseCreateRequest;
use App\Http\Requests\Warehouse\WarehousePaginateRequest;
use App\Interfaces\CloudinaryRepositoryInterface;
use App\Interfaces\WarehouseRepositoryInterface;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    use ApiHelpers;

    private WarehouseRepositoryInterface $warehouse;
    private CloudinaryRepositoryInterface $cloud;
    public function __construct(
        WarehouseRepositoryInterface $warehouse,
        CloudinaryRepositoryInterface $cloud
    )
    {
        $this->warehouse = $warehouse;
        $this->cloud = $cloud;
    }

    public function paginate(WarehousePaginateRequest $request) : JsonResponse
    {
        $validated = $request->validated();
        $warehouse = $this->warehouse->paginate($request->merge($validated));
        return $this->onSuccess($warehouse);
    }

    /**
     * create warehouse
     */
    public function create(WarehouseCreateRequest $request) : JsonResponse
    {
        $validated = $request->validated();
        $pictureUrl = $this->cloud->upload(['file' => $request->file('picture')]);
        $warehouse = $this->warehouse->create([
            'name' => $validated['name'],
            'picture' => $pictureUrl,
            'address' => $validated['address'],
            'created_by' => $request->user()->id
        ]);

        $address = new Address();
        $address->is_primary = false;
        $address->title = $validated['address_title'];
        $address->recipient = $validated['address_recipient'];
        $address->phone_recipient = $validated['address_phone_recipient'];
        $address->city_id = $validated['city_id'];
        $address->district_id = $validated['district_id'];
        $address->province_id = $validated['province_id'];
        $address->village_id = $validated['village_id'];
        $address->postal_code = $validated['postal_code'];

        $warehouse->addresses()->save($address);
        $warehouse->latestAddress;

        return $this->onSuccess($warehouse);
    }

    /**
     * get product paginate
     */
    public function getProductByWarehousePaginate(BasePaginateRequest $request, $warehouseId): JsonResponse
    {
        $validated = $request->validated();
        $product = $this->warehouse->getProductPaginate($request->merge($validated), $warehouseId);
        return $this->onSuccess($product);
    }

}
