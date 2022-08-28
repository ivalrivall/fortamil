<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function sendNotification(array $payload, array $fcmToken);
    public function sendMessage(array $payload, array $fcmToken);
    public function unread(int $notifId);
    public function read(int $notifId);
    public function readAll(int $userId);
    public function deleteAll(int $userId);
    public function getNotifByUserPaginate($request, int $userId);
}
