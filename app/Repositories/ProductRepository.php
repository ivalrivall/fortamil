<?php

namespace App\Repositories;

use App\Interfaces\PictureProductRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\PictureProduct;
use App\Models\Product;
use App\Repositories\BaseRepository;
use Exception;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

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
        try {
            $product = $this->findById($productId);
        } catch (\Throwable $th) {
            throw new Exception('Product not found');
        }
        if ($product) {
            $stock = $product->stock;
            if ($quantity > $stock) {
                return false;
            }
            return true;
        }
    }

    /**
     * disable or enable product by id
     */
    public function disableProductService(array $data)
    {
        if ($data['status']) {
            return $this->restoreById($data['id']);
        }
        return $this->deleteById($data['id']);
    }

    /**
     * reduce stock product
     */
    public function reduceProductStockService(array $data)
    {
        try {
            $product = $this->findById($data['id']);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Product not found');
        }

        if ($product->stock >= $data['quantity']) {
            $product->stock = ($product->stock - $data['quantity']);
            $product->save();
        } else {
            throw new InvalidArgumentException('Total stock not sufficient');
        }
        return true;
    }
}
