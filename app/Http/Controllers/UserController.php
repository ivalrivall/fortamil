<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\BasePaginateRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserPaginateRequest;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    /**
     * soft delete user
     */
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

    /**
     * force delete user
     */
    public function forceDelete(Request $request, $userId): JsonResponse
    {
        try {
            $u = $this->user->deleteUserService($userId);
            return $this->onSuccess($u, "Success delete");
        } catch (Exception $th) {
            Log::error($th->getMessage());
            return $this->onError('Failed delete');
        }
    }

    /**
     * create user
     */
    public function createUser(UserCreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $this->user->createUser($request->replace($validated));
            return $this->onSuccess($user, 'User created');
        } catch (Exception $th) {
            Log::error('create user =>'. $th->getMessage());
            return $this->onError('Failed create');
        }
    }

    /**
     * get user by id
     */
    public function getUserById(Request $request, $id)
    {
        try {
            $user = $this->user->getUserById($id);
            return $this->onSuccess($user, 'User fetched');
        } catch (Exception $th) {
            Log::error('get user by id =>'. $th->getMessage());
            return $this->onError('User not found');
        }
    }
}
