<?php

namespace App\Repositories;

use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use App\Repositories\BaseRepository;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
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
    public function __construct(Order $model)
    {
        $this->model = $model;
        $this->cloudinary = new CloudinaryRepository();
    }

    public function createOrder(array $payload)
    {
        $pictureUrl = $this->cloudinary->upload(['file' => $payload['marketplace_picture_label']]);

        $order = $this->create([
            'status' => 'waiting',
            'store_id' => $payload['store_id'],
            'user_id' => $payload['user_id'],
            'number_resi' => $payload['number_resi'],
            'marketplace_number_invoice' => $payload['marketplace_number_invoice'],
            'marketplace_picture_label' => $pictureUrl,
            'customer_id' => $payload['customer_id']
        ]);

        return $order;
    }
}
