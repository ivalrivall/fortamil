<?php

namespace App\Repositories;

use App\Interfaces\NotificationRepositoryInterface;
use App\Models\Notification;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
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
        $notif = $this->model->create([
            ''
        ]);
        return Larafirebase::fromArray($payload)->sendNotification($fcmToken);
    }

    public function sendMessage(array $payload, array $fcmToken)
    {
        return Larafirebase::fromArray($payload)->sendMessage($fcmToken);
    }

    public function unread($notifId)
    {
        return $this->model->where('id', $notifId)->update(['read_at' => null]);
    }

    public function read($notifId)
    {
        return $this->model->where('id', $notifId)->update(['read_at' => Carbon::now()]);
    }

    public function readAll(int $userId)
    {
        return $this->model
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
            ->update(['read_at' => Carbon::now()]);
    }

    public function deleteAll(int $userId)
    {
        return $this->model
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId)
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
            ->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId);

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
