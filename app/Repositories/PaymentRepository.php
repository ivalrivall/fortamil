<?php

namespace App\Repositories;

use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Payment;
use App\Repositories\BaseRepository;
use Carbon\Carbon;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    /**
     * @var Payment
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Payment $model
     */
    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    /**
     * create payment for order
     */
    public function makePaymentByOrder($payload)
    {
        $timestamp = Carbon::now('Asia/Jakarta')->timestamp;
        $orderId = $payload['order_id'];
        $invoiceId = "INV-$orderId-$timestamp";
        $paymentMethodId = $payload['payment_method_id'];
        $expAt = $payload['expired_at'];
        if ($paymentMethodId !== 3) {
            $expAt = Carbon::now('Asia/Jakarta')->addDays(1);
        }
        $status = 'paid';
        if ($paymentMethodId == 1) {
            $status = 'ongoing';
        } else if ($paymentMethodId == 2) {
            $status = 'unpaid';
        }
        $payment = $this->create([
            'invoice_id' => $invoiceId,
            'payment_method_id' => $paymentMethodId,
            'picture' => null,
            'expired_at' => $expAt,
            'status' => $status
        ]);
        return $payment;
    }
}
