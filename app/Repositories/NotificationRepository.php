<?php

namespace App\Repositories;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;
use App\Repositories\BaseRepository;
use Kutia\Larafirebase\Facades\Larafirebase;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    public function sendNotification(array $payload, array $fcmToken)
    {
        return Larafirebase::fromArray($payload)->sendNotification($fcmToken);
    }

    public function sendMessage(array $payload, array $fcmToken)
    {
        return Larafirebase::fromArray($payload)->sendMessage($fcmToken);
    }

    public function unread(int $notifId)
    {
        return $this->model->update($notifId, ['read' => false]);
    }

    public function read(int $notifId)
    {
        return $this->model->update($notifId, ['read' => true]);
    }

    public function readAll(int $userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->update(['read' => true]);
    }

    public function deleteAll(int $userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->delete();
    }

    /**
     * get notification paginate by user
     */
    public function getNotifByUserPaginate($request, int $userId)
    {
        $per_page = $request->per_page;
        $sort = $request->sort;
        $search = $request->search;

        $data = $this->model
            ->where('user_id', $userId);

        if ($search) {
            $data->where(function($q) use ($search) {
                $q->where('type', 'ilike', "%$search%")
                ->orWhere('priority', 'ilike', "%$search%")
                ->orWhere('description', 'ilike', "%$search%");
            });
        }

        if ($sort) {
            $sort = explode('|', $sort);
            $data = $data->orderBy($sort[0], $sort[1]);
        }

        if (!$per_page) {
            $per_page = 10;
        }

        return $data->simplePaginate($per_page);
    }
}
