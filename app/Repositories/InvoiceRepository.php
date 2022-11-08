<?php

namespace App\Repositories;

use App\Interfaces\InvoiceRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Invoice;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

class InvoiceRepository extends BaseRepository implements InvoiceRepositoryInterface
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
    public function __construct(Invoice $model, OrderRepositoryInterface $orderRepo)
    {
        $this->model = $model;
        $this->orderRepo = $orderRepo;
    }

    /**
     * create invoice for order
     */
    public function makeInvoiceByOrder($payload, $order)
    {
        $orderId = $order->id;
        $orderProducts = $order->orderProducts;
        $totalAmount = 0;
        foreach ($orderProducts as $key => $value) {
            $totalAmount += ($value->single_price * $value->quantity);
        }
        $date = Carbon::now('Asia/Jakarta')->format('Ymd');
        $invoiceNumber = "INV/$date/$orderId";
        $paymentMethodId = $payload['payment_method_id'];
        $expAt = Carbon::now('Asia/Jakarta')->toDateTimeString();
        if ($paymentMethodId !== 3) {
            $expAt = Carbon::now('Asia/Jakarta')->addDays(1);
        }
        $status = 'pending';
        if ($paymentMethodId == 3) {
            $status = 'success';
        }
        $invoice = $this->create([
            'number' => $invoiceNumber,
            'user_id' => $payload['user_id'],
            'order_id' => $orderId,
            'expired_at' => $expAt,
            'status' => $status,
            'total_amount' => $totalAmount,
            'outstanding_amount' => $totalAmount,
            'payment_method_id' => $paymentMethodId
        ]);
        return $invoice;
    }
}
