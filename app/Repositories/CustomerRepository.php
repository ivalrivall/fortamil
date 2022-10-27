<?php

namespace App\Repositories;

use App\Interfaces\CustomerRepositoryInterface;
use App\Models\Address;
use App\Models\Customer;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Customer $model)
    {
        $this->model = $model;
    }

    /**
     * create if customer doesnt exist, but get customer is exist
     * save address data of currentcustomers
     */
    public function createWithAddress($customer, $address): ?Model
    {
        $customer = $this->firstOrCreate($customer, []);

        $add = new Address();
        $add->is_primary = $address['is_primary'];
        $add->title = $address['title'];
        $add->recipient = $address['recipient'];
        $add->phone_recipient = $address['phone_recipient'];
        $add->city_id = $address['city_id'];
        $add->district_id = $address['district_id'];
        $add->province_id = $address['province_id'];
        $add->village_id = $address['village_id'];
        $add->postal_code = $address['postal_code'];

        $customer->addresses()->save($add);
        $customer->latestAddress;
        return $customer;
    }

    /**
     * create customer with auto generated name and phone
     */
    public function createWithAutoNameAndPhone(string $customerPlainAddress, $userId): ?Model
    {
        $customer = $this->create([
            'name' => 'auto generate '. random_int(1, 9999999),
            'phone' => '0123456789',
            'user_id' => $userId,
            'plain_shipment_address' => $customerPlainAddress
        ]);
        return $customer;
    }
}
