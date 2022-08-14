<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
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
    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function addProduct($data): ?Model
    {
        $product = $this->create([
            'name' => $data['name'],
            'sku' => $data['sku'],
            'description' => $data['description'],
            'price_retail' => $data['price_retail'],
            'price_grosir' => $data['price_grosir'],
            'price_modal' => $data['price_modal'],
            'price_dropship' => $data['price_dropship'],
            'stock' => $data['stock'],
            'weight' => $data['weight'],
            'store_id' => $data['store_id'],
            'category_id' => $data['category_id']
        ]);
        return $product;
    }
}
