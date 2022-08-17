<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\Product\GetProductByWarehouseRequest;
use App\Http\Requests\Product\CreateProductRequest;
use App\Interfaces\CloudinaryRepositoryInterface;
use App\Interfaces\PictureProductRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\WarehouseRepositoryInterface;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class ProductController extends Controller
{
    use ApiHelpers;
    private ProductRepositoryInterface $product;
    private CloudinaryRepositoryInterface $cloudinary;
    private PictureProductRepositoryInterface $pictureProduct;
    private WarehouseRepositoryInterface $warehouse;

    public function __construct(
        ProductRepositoryInterface $product,
        CloudinaryRepositoryInterface $cloudinary,
        PictureProductRepositoryInterface $pictureProduct,
        WarehouseRepositoryInterface $warehouse
    )
    {
        $this->product = $product;
        $this->cloudinary = $cloudinary;
        $this->pictureProduct = $pictureProduct;
        $this->warehouse = $warehouse;
    }

    public function create(CreateProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $pictureUrl = [];
        foreach ($request->pictures as $key => $value) {
            $pictureUrl[] = $this->cloudinary->upload(['file' => $value]);
        }
        $product = $this->product->addProduct([
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'description' => $validated['description'],
            'price_retail' => $validated['price_retail'],
            'price_grosir' => $validated['price_grosir'],
            'price_modal' => $validated['price_modal'],
            'price_dropship' => $validated['price_dropship'],
            'stock' => $validated['stock'],
            'weight' => $validated['weight'],
            'warehouse_id' => $validated['warehouse_id'],
            'category_id' => $validated['category_id']
        ]);
        foreach ($pictureUrl as $key => $value) {
            $this->pictureProduct->create([
                'product_id' => $product->id,
                'path' => $value,
                'thumbnail_path' => $value,
                'is_featured' => $key == 0 ? true : false
            ]);
        }
        $product->pictures;
        return $this->onSuccess($product, 'Product created');
    }

    public function getProductByWarehouse(GetProductByWarehouseRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $productByWarehouse = $this->warehouse->findById($validated['warehouse_id'], ['*'], ['products']);
        return $this->onSuccess($productByWarehouse->products, 'Success get product');
    }
}
