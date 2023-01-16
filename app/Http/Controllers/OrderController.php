<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\BasePaginateRequest;
use App\Http\Requests\Order\OrderCreateRequest;
use App\Http\Requests\Order\V2OrderCreateRequest;
use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\CustomerRepositoryInterface;
use App\Interfaces\NoteRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Interfaces\InvoiceRepositoryInterface;
use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\WarehouseRepositoryInterface;
use App\Notifications\OrderCreated;
use App\Repositories\CloudinaryRepository;
use App\Repositories\CustomerRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use ApiHelpers;

    private OrderRepositoryInterface $order;
    private CustomerRepository $customer;
    private NoteRepositoryInterface $note;
    private CartRepositoryInterface $cart;
    private InvoiceRepositoryInterface $invoice;
    private UserRepositoryInterface $userRepo;
    private WarehouseRepositoryInterface $warehouseRepo;
    private NotificationRepositoryInterface $notifRepo;

    protected $cloudinary;

    public function __construct(
        OrderRepositoryInterface $order,
        CustomerRepositoryInterface $customer,
        NoteRepositoryInterface $note,
        CartRepositoryInterface $cart,
        InvoiceRepositoryInterface $invoice,
        UserRepositoryInterface $userRepo,
        WarehouseRepositoryInterface $warehouseRepo,
        NotificationRepositoryInterface $notifRepo
    )
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->note = $note;
        $this->cart = $cart;
        $this->invoice = $invoice;
        $this->cloudinary = new CloudinaryRepository;
        $this->userRepo = $userRepo;
        $this->warehouseRepo = $warehouseRepo;
        $this->notifRepo = $notifRepo;
    }

    /**
     * create order
     */
    public function create(OrderCreateRequest $request) : JsonResponse
    {
        $validated = $request->validated();
        $userId = $request->user()->id;

        try {
            $customer = $this->customer->createWithAutoNameAndPhone($validated['customer_plain_shipment_address'], $userId);
        } catch (\Throwable $th) {
            return $this->onError($th->getMessage());
        }

        try {
            $order = $this->order->createOrder([
                'store_id' => $validated['store_id'],
                'user_id' => $userId,
                'number_resi' => $validated['number_resi'],
                'marketplace_number_invoice' => $validated['marketplace_number_invoice'],
                'marketplace_picture_label' => $request->file('marketplace_picture_label'),
                'customer_id' => $customer->id,
                'cart_id' => $validated['cart_id'],
                'warehouse_id' => $validated['warehouse_id']
            ]);
        } catch (\Throwable $th) {
            return $this->onError($th->getMessage());
        }

        try {
            $this->cart->emptyCart($userId);
        } catch (\Throwable $th) {
            return $this->onError($th->getMessage());
        }

        if (is_string($validated['notes']) && $validated['notes'] !== null) {
            $notes = ['content' => $validated['notes']];
            try {
                $this->note->saveOrderNote($order, $notes);
            } catch (\Throwable $th) {
                return $this->onError($th->getMessage());
            }
        }

        try {
            $invoice = $this->invoice->makeInvoiceByOrder([
                'payment_method_id' => $validated['payment_method_id'],
                'user_id' => $userId
            ], $order);
        } catch (\Throwable $th) {
            return $this->onError($th->getMessage());
        }

        return $this->onSuccess(array_merge(['order' => $order], ['invoice' => $invoice]), 'OK');
    }


    /**
     * get user order
     * @param BasePaginateRequest $request
     */
    public function getUserOrder(BasePaginateRequest $request) : JsonResponse
    {
        $request->validate(['warehouse_id' => 'nullable']);
        $validated = $request->validated();
        if (!$this->validateWarehouse($request->user(), $request->warehouse_id)) {
            return $this->onError('List order tidak dapat di akses', 403);
        }
        $mergedRequest = $request->merge(array_merge($validated, [
            'user_id' => $request->user()->id,
            'warehouse_id' => $request->warehouse_id
        ]));
        $order = $this->order->getUserOrder($mergedRequest);

        return $this->onSuccess($order, 'OK');
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
        return $this->onSuccess($order, 'OK');
    }

    /**
     * reject order
     */
    public function rejectOrder(Request $request, $orderId) : JsonResponse
    {
        try {
            $order = $this->order->rejectOrderRepo($orderId, $request->notes);
        } catch (\Throwable $th) {
            return $this->onError($th->getMessage());
        }
        return $this->onSuccess($order, 'OK');
    }

    /**
     * accpet order
     */
    public function acceptOrder(Request $request, $orderId) : JsonResponse
    {
        try {
            $order = $this->order->acceptOrderRepo($orderId, $request->user()->id);
        } catch (\Throwable $th) {
            return $this->onError($th->getMessage());
        }
        return $this->onSuccess($order, 'OK');
    }

    /**
     * create order V2
     */
    public function v2Create(V2OrderCreateRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        try {
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
        } catch (\Throwable $th) {
            return $this->onError($th->getMessage());
        }

        try {
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
        } catch (\Throwable $th) {
            return $this->onError($th->getMessage());
        }

        try {
            $this->cart->emptyCart($request->user()->id);
        } catch (\Throwable $th) {
            return $this->onError($th->getMessage());
        }

        if (is_string($validated['notes']) && $validated['notes'] !== null) {
            $notes = ['content' => $validated['notes']];
            try {
                $this->note->saveOrderNote($order, $notes);
            } catch (\Throwable $th) {
                return $this->onError($th->getMessage());
            }
        }
        return $this->onSuccess($order, 'OK');
    }

    /**
     * scan order product
     */
    public function scanOrderProduct(Request $request)
    {
        $valid = $request->validate(['order_product_id' => 'required|integer|min:1'], $request->all());
        try {
            $result = $this->order->scanProduct($valid['order_product_id']);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($result);
    }

    /**
     * upload proof packing
     */
    public function uploadProofPacking(Request $request) : JsonResponse
    {
        $valid = $request->validate([
            'order_id' => 'required|integer|min:1',
            'picture' => 'required|image'
        ], $request->all());
        $request = $request->replace($valid);
        try {
            $result = $this->order->uploadProofOfPacking($request->picture, $valid['order_id'], $request->user()->id);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($result);
    }

    /**
     * confirm arrived order
     */
    public function confirmArrived(Request $request)
    {
        $valid = $request->validate([
            'order_id' => 'required|numeric|min:1'
        ]);
        try {
            $payload = ['orderId' => $valid['order_id'], 'userId' => $request->user()->id];
            $order = $this->order->confirmArrivedOrder($payload);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($order);
    }

    /**
     * order from cashier
     */
    public function orderFromCashier(Request $request)
    {
        DB::beginTransaction();

        $validated = $request->validate([
            'store_id' => 'required|numeric',
            'warehouse_id' => 'required|numeric',
            // 'marketplace_picture_label' => 'required|image',
            // 'marketplace_number_invoice' => 'required',
            'number_resi' => 'required|string',
            'customer_plain_shipment_address' => 'required|string',
            'notes' => 'present|string|nullable',
            'cart_id' => 'present|array|min:1',
            'cart_id.*' => 'required|numeric|distinct',
            'warehouse_id' => 'required|numeric',
            'payment_method_id' => 'required|numeric'
        ]);
        $userId = $request->user()->id;

        try {
            $customer = $this->customer->createWithAutoNameAndPhone($validated['customer_plain_shipment_address'], $userId);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->onError($th->getMessage());
        }

        try {
            $uniqueCarts = array_unique($validated['cart_id']);
            $cartRepo = $this->cart->getCartByArrayId($uniqueCarts);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[OrderController@orderFromCashier] => (1) '.$e->getMessage());
            return $this->onError('Gagal mendapatkan cart dengan id');
        }

        if (count(collect($cartRepo)) <= 0) {
            DB::rollBack();
            return $this->onError('Cart tidak ditemukan');
        }
        try {
            $order = $this->order->create([
                'status' => 'waiting',
                'store_id' => $validated['store_id'],
                'user_id' => $userId,
                'number_resi' => $validated['number_resi'],
                'marketplace_number_invoice' => Str::random(10),
                'marketplace_picture_label' => null,
                'customer_id' => $customer->id,
                'warehouse_id' => $validated['warehouse_id']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[OrderController@orderFromCashier] => (2) '.$e->getMessage());
            return $this->onError('Gagal membuat order');
        }

        try {
            $payload = collect($cartRepo)->map(function($q) use ($order) {
                $data = [
                    'product_id' => $q->product->id,
                    'single_price' => $q->product->price_retail,
                    'order_id' => $order->id,
                    'quantity' => $q->quantity
                ];
                return $data;
            })->all();

            $hasMultiWarehouse = $this->cart->hasMultiWarehouse(collect($payload)->pluck('product_id')->all());
            if ($hasMultiWarehouse) {
                DB::rollBack();
                return $this->onError('Mohon pilih produk hanya di 1 warehouse');
            }
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[OrderController@orderFromCashier] => (3) '.$e->getMessage());
            return $this->onError('Gagal cek produk di warehouse berbeda atau tidak');
        }

        try {
            $order->orderProducts()->createMany($payload);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('[OrderController@orderFromCashier] => (4) '.$e->getMessage());
            return $this->onError('Gagal menghubungkan produk yang di order');
        }

        // SEND NOTIF KE USER ADMIN
        try {
            $admins = $this->userRepo->getUsersByRoleAndWarehouseId(1, $order->warehouse_id);
        } catch (Exception $th) {
            DB::rollBack();
            Log::error('[OrderController@orderFromCashier] (5) => '.$th->getMessage());
            return $this->onError('Gagal mendapatkan data user admin');
        }

        if (count($admins) > 0) {
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
                } catch (Exception $th) {
                    DB::rollBack();
                    Log::error('[OrderController@orderFromCashier] (6) => '.$th->getMessage());
                    return $this->onError('Gagal mengirimkan notifikasi ke user admin');
                }
            }
            Notification::sendNow($admins, new OrderCreated($order)); // SEND NOTIF KE EMAIL SEMUA ADMIN
        }

        try {
            $this->cart->emptyCart($userId);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->onError($th->getMessage());
        }

        if (is_string($validated['notes']) && $validated['notes'] !== null) {
            $notes = ['content' => $validated['notes']];
            try {
                $this->note->saveOrderNote($order, $notes);
            } catch (\Throwable $th) {
                DB::rollBack();
                return $this->onError($th->getMessage());
            }
        }

        try {
            $invoice = $this->invoice->makeInvoiceByOrder([
                'payment_method_id' => $validated['payment_method_id'],
                'user_id' => $userId
            ], $order);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->onError($th->getMessage());
        }

        DB::commit();

        return $this->onSuccess(array_merge(['order' => $order], ['invoice' => $invoice]), 'OK');
    }
}
