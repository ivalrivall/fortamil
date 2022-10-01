<?php

namespace App\Repositories;

use App\Interfaces\AddressRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Address;
use App\Models\User;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model, $user;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Address $model, User $user)
    {
        $this->model = $model;
        $this->user = $user;
    }

    /**
     * get address by user id
     * @param int $userId
     */
    public function getAddressByUserService(int $userId)
    {
        try {
            $addresses = $this->model->where('addressable_type', 'App\Models\User')->where('addressable_id', $userId)->with([
                'province' => function($q) {
                    $q->select('id','name');
                },'city' => function($q) {
                    $q->select('id','name');
                },'district' => function($q) {
                    $q->select('id','name');
                },'village' => function($q) {
                    $q->select('id','name');
                }])->get();
        } catch (Exception $th) {
            Log::error("failed get address => ". $th->getMessage());
            throw new InvalidArgumentException('User not found');
        }

        if (count($addresses) > 0) { return $addresses; };
        throw new InvalidArgumentException('No addresses found');
    }
}
