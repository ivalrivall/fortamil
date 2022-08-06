<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\Product;

use App\Http\Library\ApiHelpers;

class ProductController extends Controller
{
    use ApiHelpers;

    public function create(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($this->isAdmin($user) || $this->isWriter($user)) {
            $validator = Validator::make($request->all(), $this->productValidationRules());
            if ($validator->passes()) {
                $product = new Product();
                $product->name = $request->name;
                $product->sku = $request->sku;
                $product->description = $request->description;
                $product->price = $request->price;
                $product->stock = $request->stock;
                $product->store_id = $request->store_id;
                $product->save();

                return $this->onSuccess($product, 'Product Created');
            }
            return $this->onError($validator->errors(), 400);
        }
    }
}
