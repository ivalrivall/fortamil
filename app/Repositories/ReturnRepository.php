<?php

namespace App\Repositories;

use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\ReturnRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Order;
use App\Repositories\BaseRepository;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ReturnRepository extends BaseRepository implements ReturnRepositoryInterface
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
    public function __construct(
        Order $model,
        NotificationRepositoryInterface $notifRepo,
        UserRepositoryInterface $userRepo
    )
    {
        $this->model = $model;
        $this->notifRepo = $notifRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * request return
     */
    public function requestReturnService($payload)
    {
        try {
            $order = $this->findById($payload['orderId']);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Order tidak ditemukan');
        }

        if ($order->user_id !== $payload['userId']) {
            throw new InvalidArgumentException('Anda tidak diizinkan untuk melakukan retur di order ini');
        }

        if ($order->status == 'return') {
            throw new InvalidArgumentException('Order sedang dalam proses retur');
        }

        if ($order->status !== 'arrived') {
            throw new InvalidArgumentException('Order masih dalam perjalanan / belum di selesaikan');
        }

        $wo = $this->userRepo->getUsersByRoleId(2);
        foreach ($wo as $key => $value) {
            try {
                $payloadNotif = [
                    'title' => 'Request retur order',
                    'type' => 'App\Notifications\SystemInfo',
                    'icon' => 'ring',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $wo->id,
                    'data' => json_encode($order),
                    'priority' => 'high',
                    'description' => "Request retur oleh dropshipper ".$payload['userId'],
                ];
                $this->notifRepo->create($payloadNotif);
            } catch (Exception $e) {
                Bugsnag::notifyException($e);
                Log::error('[requestReturnService@ReturnRepository] => '.$e->getMessage());
            }
        }

        $order->status = 'return';
        $order->save();
        return $order;
    }
}
