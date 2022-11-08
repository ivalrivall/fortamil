<?php

namespace App\Repositories;

use App\Interfaces\InvoiceRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Payment;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

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
    public function __construct(Payment $model, InvoiceRepositoryInterface $invoiceRepo)
    {
        $this->model = $model;
        $this->invoiceRepo = $invoiceRepo;
        $this->cloudinary = new CloudinaryRepository();
    }

    /**
     * create payment for order
     */
    public function payInvoiceOrder($payload)
    {
        DB::beginTransaction();
        $invoiceId = $payload['invoice_id'];
        $paymentMethodId = $payload['payment_method_id'];
        try {
            $pictureUrl = $this->cloudinary->upload(['file' => $payload['picture'], 'folder' => 'payment']);
        } catch (Exception $th) {
            DB::rollBack();
            Log::error('error upload picture');
            Log::error("[payInvoiceOrder@PaymentRepository] ". $th->getMessage());
            throw $th;
        }
        try {
            $invoice = $this->invoiceRepo->findById($invoiceId);
        } catch (Exception $th) {
            DB::rollBack();
            Log::error('Failed find invoice');
            Log::error("[payInvoiceOrder@PaymentRepository] ". $th->getMessage());
            throw $th;
        }

        if ($invoice->outstanding_amount > 0) {
            $payment = $this->create([
                'invoice_id' => $invoiceId,
                'payment_method_id' => $paymentMethodId,
                'picture' => $pictureUrl,
                'status' => 'pending',
                'amount' => $payload['amount']
            ]);
        }
        DB::commit();
        return $payment;
    }
}
