<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Cloudinary\Configuration\Configuration;

class MarketplaceController extends Controller
{
    use ApiHelpers;

    /**
     * list market place
     */
    public function getAll(Request $request) : JsonResponse
    {
        if ($this->isAuthehticatedUser($request->user())) {
            $marketplace = Marketplace::all();
            return $this->onSuccess($marketplace, 'Success listing');
        }
        return $this->onError('Unauthorized', 401);
    }
}
