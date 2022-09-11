<?php

namespace App\Repositories;

use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    protected $cartRepo;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Order $model, CartRepositoryInterface $cartRepo)
    {
        $this->model = $model;
        $this->orderProduct = new OrderProduct();
        $this->cloudinary = new CloudinaryRepository();
        $this->cartRepo = $cartRepo;
    }

    public function createOrder(array $payload)
    {
        $uniqueCarts = array_unique($payload['cart_id']);

        $cartRepo = $this->cartRepo->getCartByArrayId($uniqueCarts);

        if (count(collect($cartRepo)) > 0) {
            $pictureUrl = $this->cloudinary->upload(['file' => $payload['marketplace_picture_label']]);

            DB::beginTransaction();

            $order = $this->create([
                'status' => 'waiting',
                'store_id' => $payload['store_id'],
                'user_id' => $payload['user_id'],
                'number_resi' => $payload['number_resi'],
                'marketplace_number_invoice' => $payload['marketplace_number_invoice'],
                'marketplace_picture_label' => $pictureUrl,
                'customer_id' => $payload['customer_id']
            ]);

            $payload = $cartRepo->map(function($q) use ($order) {
                $data = [
                    'product_id' => $q->product->id,
                    'single_price' => $q->product->price_dropship,
                    'order_id' => $order->id,
                    'quantity' => $q->quantity
                ];
                return $data;
            })->all();

            $order->orderProducts()->createMany($payload);

            DB::commit();

            return $order;
        }
        throw new Exception('cart not found');
    }
}
