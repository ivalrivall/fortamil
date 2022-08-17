<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\Order\OrderCreateRequest;
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

        $customer = $this->customer->createWithAddress([
            'name' => 'name',
            'phone' => $validated['phone'],
            'user_id' => $request->user()->id
        ], [
            'is_primary' => false,
            'title' => $validated['address_title'],
            'recipient' => $validated['address_recipient'],
            'phone_recipient' => $validated['address_phone_recipient'],
            'city_id' => $validated['city_id'],
            'district_id' => $validated['district_id'],
            'province_id' => $validated['province_id'],
            'village_id' => $validated['village_id'],
            'postal_code' => $validated['postal_code']
        ]);

        $pictureUrl = $this->cloudinary->upload(['file' => $request->file('marketplace_picture_label')]);

        $order = $this->order->create([
            'status' => 'success',
            'store_id' => $validated['store_id'],
            'user_id' => $request->user()->id,
            'number_resi' => $validated['number_resi'],
            'marketplace_number_resi' => $validated['marketplace_number_resi'],
            'marketplace_picture_label' => $pictureUrl,
            'customer_id' => $customer->id
        ]);

        return $this->onSuccess($order, 'Order created');
    }
}
