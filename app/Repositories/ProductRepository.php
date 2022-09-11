<?php

namespace App\Repositories;

use App\Interfaces\PictureProductRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\PictureProduct;
use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    protected $pictureProduct;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Product $model, PictureProductRepositoryInterface $pictureProduct)
    {
        $this->model = $model;
        $this->pictureProduct = $pictureProduct;
        $this->cloudinary = new CloudinaryRepository;
    }

    public function addProduct($data): ?Model
    {
        $pictureUrl = [];
        foreach ($data['pictures'] as $key => $value) {
            $pictureUrl[] = $this->cloudinary->upload(['file' => $value]);
        }
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
            'warehouse_id' => $data['warehouse_id'],
            'category_id' => $data['category_id']
        ]);
        foreach ($pictureUrl as $key => $value) {
            $this->pictureProduct->create([
                'product_id' => $product->id,
                'path' => $value,
                'thumbnail_path' => $value,
                'is_featured' => $key == 0 ? true : false
            ]);
        }
        return $product;
    }

    public function checkStockIsAvailable(int $productId, int $quantity): bool
    {
        $product = $this->findById($productId);
        if ($product) {
            $stock = $product->stock;
            if ($quantity > $stock) {
                return false;
            }
            return true;
        }
    }
}
