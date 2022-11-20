<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Interfaces\ReturnRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    use ApiHelpers;

    private ReturnRepositoryInterface $returnRepo;

    public function __construct(ReturnRepositoryInterface $returnRepo)
    {
        $this->returnRepo = $returnRepo;
    }

    public function requestReturn(Request $request)
    {
        $valid = $request->validate(['order_id' => 'required|numeric|min:1'], $request->all());
        try {
            $payload = [
                'orderId' => $valid['order_id'],
                'userId' => $request->user()->id
            ];
            $data = $this->returnRepo->requestReturnService($payload);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($data);
    }
}
