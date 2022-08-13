<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\CreateCustomerAddressRequest;
use App\Interfaces\CustomerRepositoryInterface;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use ApiHelpers;

    private CustomerRepositoryInterface $customer;

    public function __construct(CustomerRepositoryInterface $customer)
    {
        $this->customer = $customer;
    }

    /**
     * create customer with address
     */
    public function createWithAddress(CreateCustomerAddressRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $customer = $this->customer->create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'user_id' => $request->user()->id
        ]);

        $address = new Address();
        $address->is_primary = false;
        $address->title = $validated['address_title'];
        $address->recipient = $validated['address_recipient_name'];
        $address->phone_recipient = $validated['address_recipient_phone'];
        $address->city_id = $validated['address_city_id'];
        $address->district_id = $validated['address_district_id'];
        $address->province_id = $validated['address_province_id'];
        $address->village_id = $validated['address_village_id'];
        $address->postal_code = $validated['address_postal_code'];

        $customer->addresses()->save($address);
        $customer->latestAddress;
        return $this->onSuccess($customer, 'Customer created');
    }
}
