<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\BasePaginateRequest;
use App\Http\Requests\User\UserPaginateRequest;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function getPaginate(UserPaginateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $cart = $this->user->paginateService($request->replace($validated));
        } catch (Exception $th) {
            return $this->onError($th->getMessage());
        }
        return $this->onSuccess($cart, 'Success fetch');
    }

    public function disableUser(Request $request, $userId): JsonResponse
    {
        try {
            $u = $this->user->disableUserService([
                'id' => $userId,
                'status' => $request->status,
            ]);
            return $this->onSuccess($u, "Success change user $userId with status ".($request->status ? 'active' : 'inactive'));
        } catch (Exception $th) {
            Log::error($th->getMessage());
            return $this->onError('Failed change status');
        }
    }
}
