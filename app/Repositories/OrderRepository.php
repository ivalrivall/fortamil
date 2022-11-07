<?php

namespace App\Repositories;

use App\Http\Library\ApiHelpers;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\WarehouseRepositoryInterface;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Notifications\OrderCreated;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    use ApiHelpers;
    /**
     * @var Model
     */
    protected $model;
    protected $cartRepo;
    protected $userRepo;
    protected $warehouseRepo;
    protected $notifRepo;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(
        Order $model,
        CartRepositoryInterface $cartRepo,
        UserRepositoryInterface $userRepo,
        WarehouseRepositoryInterface $warehouseRepo,
        NotificationRepositoryInterface $notifRepo
    )
    {
        $this->model = $model;
        $this->orderProduct = new OrderProduct();
        $this->cloudinary = new CloudinaryRepository();
        $this->cartRepo = $cartRepo;
        $this->userRepo = $userRepo;
        $this->warehouseRepo = $warehouseRepo;
        $this->notifRepo = $notifRepo;
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
                'customer_id' => $payload['customer_id'],
                'warehouse_id' => $payload['warehouse_id']
            ]);

            $user = $this->userRepo->findById($payload['user_id']);

            $payload = collect($cartRepo)->map(function($q) use ($order) {
                $data = [
                    'product_id' => $q->product->id,
                    'single_price' => $q->product->price_dropship,
                    'order_id' => $order->id,
                    'quantity' => $q->quantity
                ];
                return $data;
            })->all();

            $hasMultiWarehouse = $this->cartRepo->hasMultiWarehouse(collect($payload)->pluck('product_id')->all());
            if ($hasMultiWarehouse) {
                DB::rollBack();
                throw new Exception('Please pick product only in 1 warehouse');
            }

            if ($this->isDropshipper($user)) {
                $warehouseIds = $this->warehouseRepo->getWarehouseByProductList(collect($payload)->pluck('product_id')->all());
                $warehouse = $this->warehouseRepo->findById($warehouseIds[0]);
                $canAccessWarehouse = $this->isCanAccessWarehouse($user, $warehouse);
                if (!$canAccessWarehouse) {
                    DB::rollBack();
                    throw new Exception("User must be whitelisted on warehouse: $warehouse->name");
                }
            }
            $order->orderProducts()->createMany($payload);

            // SEND NOTIF KE USER DROPSHIPPER
            try {
                $payloadNotif = [
                    'title' => 'Order dibuat',
                    'type' => 'App\Notifications\SystemInfo',
                    'icon' => 'ring',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $order->user_id,
                    'data' => json_encode($order),
                    'priority' => 'high',
                    'description' => 'Order baru dibuat dengan nomor resi: '.$order->number_resi,
                ];
                $this->notifRepo->create($payloadNotif);
            } catch (\Throwable $th) {
                throw $th->getMessage();
            }

            // SEND NOTIF KE USER ADMIN
            try {
                $admins = $this->userRepo->getUsersByRoleId(1);
            } catch (\Throwable $th) {
                throw $th->getMessage();
            }
            foreach ($admins as $key => $value) {
                try {
                    $payloadNotif = [
                        'title' => 'Order dibuat',
                        'type' => 'App\Notifications\SystemInfo',
                        'icon' => 'ring',
                        'notifiable_type' => 'App\Models\User',
                        'notifiable_id' => $value->id,
                        'data' => json_encode($order),
                        'priority' => 'high',
                        'description' => 'Order baru dibuat dengan nomor resi: '.$order->number_resi,
                    ];
                    $this->notifRepo->create($payloadNotif);
                } catch (\Throwable $th) {
                    throw $th->getMessage();
                }
            }
            Notification::sendNow($admins, new OrderCreated($order)); // SEND NOTIF KE EMAIL SEMUA ADMIN
            DB::commit();
            return $order;
        }
        throw new Exception('cart not found');
    }

    /**
     * get user order
     * @param Request $request
     */
    public function getUserOrder($request)
    {
        $per_page = $request->per_page;
        $sort = $request->sort;
        $s = $request->search;
        $user = $request->user();

        if ($this->isDropshipper($user)) {
            $data = $this->model->where('user_id', $request->user_id)
                ->with(['store.marketplace', 'customer']);
        }

        if ($this->isAdmin($user) || $this->isSuperAdmin($user)){
            $data = $this->model->with(['store.marketplace', 'customer']);
        }

        if ($s) {
            $data = $data->where(function($q) use ($s) {
                $q->where('status', 'ilike', "%$s%")
                ->orWhere('number_resi', 'ilike', "%$s%")
                ->orWhere('marketplace_number_invoice', 'ilike', "%$s%")
                ->orWhereHas('store', function($x) use ($s) {
                    $x->where('name', 'ilike', "%$s%")->orWhereHas('marketplace', function($xx) use ($s) {
                        $xx->where('name', 'ilike', "%$s%");
                    });
                })->orWhereHas('customer', function($x) use ($s) {
                    $x->where(function($xx) use ($s) {
                        $xx->where('name', 'ilike', "%$s%")
                        ->orWhere('phone', 'ilike', "%$s%");
                    });
                });
            });
        }

        if ($sort) {
            $sort = explode('|', $sort);
            $data = $data->orderBy($sort[0], $sort[1]);
        }

        if (!$per_page) {
            $per_page = 10;
        }

        return $data->simplePaginate($per_page);
    }

    /**
     * get detail order
     * @param $orderId
     */
    public function getDetailOrder($orderId)
    {
        $order = $this->model->where('id', $orderId)->with([
            'store',
            'customer',
            'warehouse',
            'orderProducts.product.category',
            'orderProducts.product.pictures' => function($q) {
                $q->select('product_id','path','is_featured','thumbnail_path');
            },
        ])->first();
        if (!$order) {
            throw new Exception('Order not found');
        }
        return $order;
    }

    /**
     * change status order
     * @param $orderId
     * @param string $status
     */
    public function changeStatusOrder($orderId, string $status)
    {
        try {
            $order = $this->model->where('id', $orderId)->update(['status' => $status]);
        } catch (\Throwable $th) {
            throw new Exception('Failed update status order to '.$status);
        }
        return $order;
    }

    /**
     * reject order
     */
    public function rejectOrderRepo($orderId, $notes)
    {
        try {
            $order = $this->findById($orderId);
        } catch (\Throwable $th) {
            throw new Exception('Order not found');
        }
        $payloadNotif = [
            'title' => 'Order ditolak',
            'type' => 'App\Notifications\SystemInfo',
            'icon' => 'ring',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $order->user_id,
            'priority' => 'high',
            'data' => json_encode($order),
            'description' => 'Order anda ditolak dengan alasan: '.$notes,
        ];
        $this->notifRepo->create($payloadNotif);
        $order->status = 'reject';
        $order->save();
        return $order;
    }

    /**
     * accept order
     */
    public function acceptOrderRepo($orderId)
    {
        try {
            $order = $this->findById($orderId);
        } catch (\Throwable $th) {
            throw new Exception('Order not found');
        }
        $payloadNotif = [
            'title' => 'Order diterima',
            'type' => 'App\Notifications\SystemInfo',
            'icon' => 'ring',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $order->user_id,
            'data' => json_encode($order),
            'priority' => 'high',
            'description' => 'Order sudah disetujui dan akan segera diproses',
        ];
        $this->notifRepo->create($payloadNotif);
        $order->status = 'on-progress';
        $order->save();
        return $order;
    }
}
