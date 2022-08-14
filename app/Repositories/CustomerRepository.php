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
}
