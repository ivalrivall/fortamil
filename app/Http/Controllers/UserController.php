<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiHelpers;

    private UserRepositoryInterface $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    public function getCarts(Request $request)
    {
        try {
            $cart = $this->user->getCartItems($request->user()->id);
        } catch (Exception $th) {
            return $this->onError($th->getMessage());
        }

        return $this->onSuccess($cart, 'Success get carts');
    }
}
