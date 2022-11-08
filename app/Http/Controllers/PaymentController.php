<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\Payment\PayOrderRequest;
use App\Interfaces\PaymentRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use ApiHelpers;
    private PaymentRepositoryInterface $paymentRepo;
    public function __construct(PaymentRepositoryInterface $paymentRepo)
    {
        $this->paymentRepo = $paymentRepo;
    }

    public function payOrder(PayOrderRequest $request)
    {
        $validated = $request->validated();
        try {
            $payment = $this->paymentRepo->payInvoiceOrder($validated);
        } catch (Exception $th) {
            Log::error("[payOrder@PaymentController] ".$th->getMessage());
            return $this->onError('Gagal bayar');
        }
        return $this->onSuccess($payment);
    }
}
