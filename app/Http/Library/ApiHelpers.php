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

    protected function onError(string $message = '', int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'success' => false,
            'message' => $message,
        ], $code);
    }

    protected function createSlug($str, $delimiter = '-')
    {
        $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
        return $slug;
    }
}
