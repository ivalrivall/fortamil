<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\BasePaginateRequest;
use App\Http\Requests\Order\OrderCreateRequest;
use App\Interfaces\CustomerRepositoryInterface;
use App\Interfaces\NoteRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiHelpers;

    private OrderRepositoryInterface $order;
    private CustomerRepository $customer;
    private NoteRepositoryInterface $note;

    public function __construct(
        OrderRepositoryInterface $order,
        CustomerRepositoryInterface $customer,
        NoteRepositoryInterface $note
    )
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->note = $note;
    }

    /**
     * create order
     */
    public function create(OrderCreateRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $customer = $this->customer->createWithAddress([
            'name' => $validated['customer_name'],
            'phone' => $validated['customer_phone'],
            'user_id' => $request->user()->id
        ], [
            'is_primary' => false,
            'title' => '[Auto Generated] Address of '.$validated['customer_name'],
            'recipient' => $validated['customer_recipient_name'],
            'phone_recipient' => $validated['customer_recipient_phone'],
            'province_id' => $validated['customer_province_id'],
            'city_id' => $validated['customer_city_id'],
            'district_id' => $validated['customer_district_id'],
            'village_id' => $validated['customer_village_id'],
            'postal_code' => $validated['customer_postal_code']
        ]);

        $order = $this->order->createOrder([
            'store_id' => $validated['store_id'],
            'user_id' => $request->user()->id,
            'number_resi' => $validated['number_resi'],
            'marketplace_number_invoice' => $validated['marketplace_number_invoice'],
            'marketplace_picture_label' => $request->file('marketplace_picture_label'),
            'customer_id' => $customer->id,
            'cart_id' => $validated['cart_id'],
            'warehouse_id' => $validated['warehouse_id']
        ]);

        if (is_string($validated['notes']) && $validated['notes'] !== null) {
            $notes = ['content' => $validated['notes']];
            $this->note->saveOrderNote($order, $notes);
        }

        return $this->onSuccess($order, 'Order created');
    }


    /**
     * get user order
     * @param BasePaginateRequest $request
     */
    public function getUserOrder(BasePaginateRequest $request) : JsonResponse
    {
        $validated = $request->validated();
        $mergedRequest = $request->merge(array_merge($validated, ['user_id' => $request->user()->id]));
        $order = $this->order->getUserOrder($mergedRequest);

        return $this->onSuccess($order, 'Order fetched');
    }

    /**
     * get detail order
     */
    public function getDetailOrder($orderId) : JsonResponse
    {
        try {
            $order = $this->order->getDetailOrder($orderId);
        } catch (\Throwable $th) {
            return $this->onError($th->getMessage());
        }
        return $this->onSuccess($order, 'Order fetched');
    }
}
