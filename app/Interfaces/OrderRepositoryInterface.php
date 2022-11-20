<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    public function createOrder(array $payload);
    public function getUserOrder($payload);
    public function getDetailOrder($orderId);
    public function changeStatusOrder($orderId, string $status);
    public function rejectOrderRepo($orderId, $notes);
    public function acceptOrderRepo($orderId, $adminId);
    public function scanProduct(int $orderProductId);
    public function uploadProofOfPacking($picture, $orderId, $warehouseOfficerId);
}
