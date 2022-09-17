<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Cloudinary\Configuration\Configuration;

class MarketplaceController extends Controller
{
    /**
     * list market place
     */
    public function getAll() : JsonResponse
    {
        $marketplace = Marketplace::all();
        return $this->onSuccess($marketplace, 'Success listing');
    }
}
