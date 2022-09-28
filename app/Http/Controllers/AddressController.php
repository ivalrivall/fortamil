<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Interfaces\AddressRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiHelpers;

    private AddressRepositoryInterface $address;

    public function __construct(AddressRepositoryInterface $address)
    {
        $this->address = $address;
    }

    public function getAddressByUserId(Request $request, $userId)
    {
        try {
            $address = $this->address->getAddressByUserService($userId);
        } catch (Exception $th) {
            return $this->onError($th->getMessage());
        }
        return $this->onSuccess($address, 'Success get address');
    }
}
