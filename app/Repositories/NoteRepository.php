<?php

namespace App\Repositories;

use App\Interfaces\NoteRepositoryInterface;
use App\Models\Note;
use App\Models\Order;
use App\Repositories\BaseRepository;

class NoteRepository extends BaseRepository implements NoteRepositoryInterface
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
    public function __construct(Note $model)
    {
        $this->model = $model;
    }

    public function saveOrderNote(Order $order, array $notePayload)
    {
        $note = new $this->model($notePayload);
        $notes = $order->note()->save($note);
        return $notes;
    }
}
