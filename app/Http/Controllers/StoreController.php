<?php

namespace App\Http\Controllers;

use App\Models\Store;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

use App\Http\Library\ApiHelpers;
use App\Interfaces\AddressRepositoryInterface;
use App\Interfaces\CloudinaryRepositoryInterface;
use App\Interfaces\StoreRepositoryInterface;
use App\Models\Address;

class StoreController extends Controller
{
    use ApiHelpers;

    private StoreRepositoryInterface $storeRepository;
    private AddressRepositoryInterface $addressRepository;
    private CloudinaryRepositoryInterface $cloudinary;
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        AddressRepositoryInterface $addressRepository,
        CloudinaryRepositoryInterface $cloudinary,
    )
    {
        $this->storeRepository = $storeRepository;
        $this->addressRepository = $addressRepository;
        $this->cloudinary = $cloudinary;
    }

    /**
     * create store
     */
    public function create(Request $request) : JsonResponse
    {
        $slug = $this->createSlug($request->name);
        $validated = $request->validated();

        $pictureUrl = $this->cloudinary->upload($request->file('file'));

        $store = $this->storeRepository->create([
            'name' => $validated['name'],
            'marketplace_id' => $validated['marketplace_id'],
            'picture' => $pictureUrl,
            'address' => $validated['address'],
            'slug' => $slug
        ]);

        $address = new Address;
        $address->is_primary = false;
        $address->title = $validated['address_title'];
        $address->recipient = $validated['address_recipient'];
        $address->phone_recipient = $validated['address_phone_recipient'];

        $store->addresses()->save($store);

        return $this->onSuccess($store, 'Store created');
    }
}
