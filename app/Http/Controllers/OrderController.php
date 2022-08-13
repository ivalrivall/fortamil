<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\OrderCreateRequest;
use App\Interfaces\CustomerRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiHelpers;
    private OrderRepositoryInterface $order;
    private CustomerRepository $customer;

    public function __construct(
        OrderRepositoryInterface $order,
        CustomerRepositoryInterface $customer
    )
    {
        $this->order = $order;
        $this->customer = $customer;
    }

    /**
     * create order
     */
    public function create(OrderCreateRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $pictureUrl = $this->cloudinary->upload(['file' => $request->file('marketplace_picture_label')]);

        $order = $this->order->create([
            'status' => 'success',
            'store_id' => $validated['store_id'],
            'number_resi' => $validated['number_resi'],
            'marketplace_number_resi' => $validated['marketplace_number_resi'],
            'slug' => $pictureUrl,
            'user_id' => $request->user()->id
        ]);

        $this->customer->create([
            'status' => 'success',
            'store_id' => $validated['store_id'],
            'number_resi' => $validated['number_resi'],
            'marketplace_number_resi' => $validated['marketplace_number_resi'],
            'slug' => $pictureUrl,
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
