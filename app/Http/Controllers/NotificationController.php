<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\BasePaginateRequest;
use App\Interfaces\NotificationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiHelpers;

    private NotificationRepositoryInterface $notif;

    public function __construct(
        NotificationRepositoryInterface $notif
    )
    {
        $this->notif = $notif;
    }

    public function paginateUserNotif(BasePaginateRequest $request, $userId) : JsonResponse
    {
        $validated = $request->validated();
        $notif = $this->notif->getNotifByUserPaginate($request->merge($validated), $userId);
        return $this->onSuccess($notif);
    }

    public function readAll(Request $request)
    {
        $notif = $this->notif->readAll($request->user()->id);
        return $this->onSuccess($notif);
    }

    public function markRead(Request $request, $notifId)
    {
        $notif = $this->notif->read($notifId);
        return $this->onSuccess($notif);
    }

    public function markUnread(Request $request, $notifId)
    {
        $notif = $this->notif->unread($notifId);
        return $this->onSuccess($notif);
    }
}
