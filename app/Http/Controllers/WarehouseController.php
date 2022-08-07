<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\BasePaginateRequest;
use App\Http\Requests\WarehouseCreateRequest;
use App\Interfaces\CloudinaryRepositoryInterface;
use App\Interfaces\WarehouseRepositoryInterface;
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

    public function paginate(BasePaginateRequest $request) : JsonResponse
    {
        $warehouse = $this->warehouse->paginate($request);
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
            'address' => $validated['address']
        ]);
        return $this->onSuccess($warehouse);
    }

}
