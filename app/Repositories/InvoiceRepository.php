<?php

namespace App\Repositories;

use App\Interfaces\InvoiceRepositoryInterface;
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
    public function __construct(Invoice $model)
    {
        $this->model = $model;
    }

    /**
     * create invoice for order
     */
    public function makeInvoiceByOrder($payload)
    {
        $orderId = $payload['order_id'];
        $date = Carbon::now('Asia/Jakarta')->format('Ymd');
        $invoiceId = "INV/$date/$orderId";
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
            'number' => $invoiceId,
            'user_id' => $payload['user_id'],
            'order_id' => $orderId,
            'expired_at' => $expAt,
            'status' => $status
        ]);
        return $invoice;
    }
}
