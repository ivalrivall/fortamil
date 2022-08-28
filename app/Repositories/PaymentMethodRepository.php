<?php

namespace App\Repositories;

use App\Interfaces\PaymentMethodRepositoryInterface;
use App\Models\PaymentMethod;
use App\Repositories\BaseRepository;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodRepositoryInterface
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
    public function __construct(PaymentMethod $model)
    {
        $this->model = $model;
    }
}
