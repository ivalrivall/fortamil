<?php

namespace App\Http\Library;

use Illuminate\Http\JsonResponse;

trait ApiHelpers
{
    protected function isAdmin($user): bool
    {
        if (!empty($user)) {
            return $user->tokenCan('admin');
        }

        return false;
    }

    protected function isDropshipper($user): bool
    {

        if (!empty($user)) {
            return $user->tokenCan('dropshipper');
        }

        return false;
    }

    protected function isWarehouseOfficer($user): bool
    {
        if (!empty($user)) {
            return $user->tokenCan('warehouse_officer');
        }

        return false;
    }

    protected function isAuthehticatedUser($user): bool
    {
        if (!empty($user)) {
            $isWarehouse = $user->tokenCan('warehouse_officer');
            $isDropshipper = $user->tokenCan('dropshipper');
            $isAdmin = $user->tokenCan('admin');
            if ($isWarehouse || $isDropshipper || $isAdmin) {
                return true;
            }
        }
        return false;
    }

    protected function onSuccess($data, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function onError(string $message = '', int $code): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'success' => false,
            'message' => $message,
        ], $code);
    }

    protected function productValidationRules(): array
    {
        return [
            'title' => 'required|string',
            'content' => 'required|string',
        ];
    }

    protected function userValidatedRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
