<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\OrderCreateRequest;
use App\Interfaces\OrderRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiHelpers;
    private OrderRepositoryInterface $order;

    public function __construct(OrderRepositoryInterface $order)
    {
        $this->order = $order;
    }

    /**
     * create order
     */
    public function create(OrderCreateRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $slug = $this->createSlug($request->name);

        $pictureUrl = $this->cloudinary->upload(['file' => $request->file('picture')]);

        $store = $this->storeRepository->create([
            'name' => $validated['name'],
            'marketplace_id' => $validated['marketplace_id'],
            'picture' => $pictureUrl,
            'address' => $validated['address'],
            'slug' => $slug,
            'user_id' => $request->user()->id
        ]);

        $address = new Address;
        $address->is_primary = false;
        $address->title = $validated['address_title'];
        $address->recipient = $validated['address_recipient'];
        $address->phone_recipient = $validated['address_phone_recipient'];
        $address->city_id = $validated['city_id'];
        $address->district_id = $validated['district_id'];
        $address->province_id = $validated['province_id'];
        $address->village_id = $validated['village_id'];
        $address->postal_code = $validated['postal_code'];

        $store->addresses()->save($address);
        $store->latestAddress;
        return $this->onSuccess($store, 'Store created');
    }
}
