<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\BasePaginateRequest;
use App\Http\Requests\Product\GetProductByWarehouseRequest;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\ReduceStockProductRequest;
use App\Interfaces\CloudinaryRepositoryInterface;
use App\Interfaces\PictureProductRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\WarehouseRepositoryInterface;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Milon\Barcode\Facades\DNS2DFacade;

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
        try {
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
                'category_id' => $validated['category_id'],
                'pictures' => $validated['pictures'],
            ]);
            $product->pictures;
            return $this->onSuccess($product, 'Product created');
        } catch (Exception $th) {
            return $this->onError($th->getMessage());
        }
    }

    public function getProductByWarehouse(GetProductByWarehouseRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $warehouse = $this->warehouse->findById($validated['warehouse_id'], ['*'], ['products']);
        $canAccessWarehouse = $this->isCanAccessWarehouse($request->user(), $warehouse);
        if ($canAccessWarehouse) {
            return $this->onSuccess($warehouse->products, 'Success get product');
        }
        return $this->onError('Cannot access warehouse', 403);
    }

    public function disableProduct(Request $request, $productId): JsonResponse
    {
        try {
            $product = $this->product->disableProductService([
                'id' => $productId,
                'status' => $request->status,
            ]);
            return $this->onSuccess($product, "Success change product $productId with status ".($request->status ? 'active' : 'inactive'));
        } catch (Exception $th) {
            Log::error($th->getMessage());
            return $this->onError('Failed change status');
        }
    }

    public function reduceStock(ReduceStockProductRequest $request, $productId): JsonResponse
    {
        $validated = $request->validated();
        try {
            $product = $this->product->reduceProductStockService([
                'id' => $productId,
                'quantity' => $validated['quantity'],
            ]);
            return $this->onSuccess($product, "Success reduce stock product $productId ");
        } catch (Exception $th) {
            Log::error('[reduceStock@ProductContoller]'.$th->getMessage());
            return $this->onError('Failed reduce stock');
        }
    }

    public function exportProducts(Request $request)
    {
        $validated = $request->validate(['warehouse_id' => 'required|numeric|min:1']);
        $data = Product::where('warehouse_id', $validated['warehouse_id'])->get();
        $pdf = Pdf::loadView('pdf.product.export_barcode', ['products' => $data]);
        // download PDF file with download method
        // return $pdf->download('nama.pdf');
        return $pdf->stream();
    }
}
