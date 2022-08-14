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

        $address = [
            'is_primary' => false,
            'title' => $validated['address_title'],
            'recipient' => $validated['address_recipient_name'],
            'phone_recipient' => $validated['address_recipient_phone'],
            'city_id' => $validated['address_city_id'],
            'district_id' => $validated['address_district_id'],
            'province_id' => $validated['address_province_id'],
            'village_id' => $validated['address_village_id'],
            'postal_code' => $validated['address_postal_code']
        ];

        $customer = $this->customer->createWithAddress([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'user_id' => $request->user()->id
        ], $address);

        return $this->onSuccess($customer, 'Customer created');
    }
}
