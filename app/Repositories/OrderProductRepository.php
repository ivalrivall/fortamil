<?php

namespace App\Repositories;

use App\Interfaces\OrderProductRepositoryInterface;
use App\Models\OrderProduct;
use App\Repositories\BaseRepository;

class OrderProductRepository extends BaseRepository implements OrderProductRepositoryInterface
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
    public function __construct(OrderProduct $model)
    {
        $this->model = $model;
    }

    public function createOrderProduct(array $payload)
    {
        $uniqueCarts = array_unique($payload['cart_id']);
    }
}
