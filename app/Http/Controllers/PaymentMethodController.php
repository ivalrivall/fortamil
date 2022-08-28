<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Interfaces\PaymentMethodRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    use ApiHelpers;

    private PaymentMethodRepositoryInterface $paymentMethod;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getAll() : JsonResponse
    {
        $result = $this->paymentMethod->all();
        return $this->onSuccess($result);
    }
}
