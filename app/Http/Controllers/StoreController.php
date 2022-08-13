<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\BasePaginateRequest;
use App\Http\Requests\StoreCreateRequest;
use App\Interfaces\AddressRepositoryInterface;
use App\Interfaces\CloudinaryRepositoryInterface;
use App\Interfaces\StoreRepositoryInterface;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
class StoreController extends Controller
{
    use ApiHelpers;

    private StoreRepositoryInterface $storeRepository;
    private AddressRepositoryInterface $addressRepository;
    private CloudinaryRepositoryInterface $cloudinary;
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        AddressRepositoryInterface $addressRepository,
        CloudinaryRepositoryInterface $cloudinary
    )
    {
        $this->storeRepository = $storeRepository;
        $this->addressRepository = $addressRepository;
        $this->cloudinary = $cloudinary;
    }

    /**
     * create store
     */
    public function create(StoreCreateRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $slug = $this->createSlug($request->name);

        $pictureUrl = $this->cloudinary->upload(['file' => $request->file('picture')]);

        $store = $this->storeRepository->create([
            'name' => $validated['name'],
            'marketplace_id' => $validated['marketplace_id'],
            'picture' => $pictureUrl,
            'address' => $validated['address'],
            'slug' => $slug,
            'user_id' => $request->user()->id
        ]);

        $address = new Address;
        $address->is_primary = false;
        $address->title = $validated['address_title'];
        $address->recipient = $validated['address_recipient'];
        $address->phone_recipient = $validated['address_phone_recipient'];
        $address->city_id = $validated['city_id'];
        $address->district_id = $validated['district_id'];
        $address->province_id = $validated['province_id'];
        $address->village_id = $validated['village_id'];
        $address->postal_code = $validated['postal_code'];

        $store->addresses()->save($address);
        $store->latestAddress;
        return $this->onSuccess($store, 'Store created');
    }

    /**
     * get store paginate
     */
    public function paginate(BasePaginateRequest $request) : JsonResponse
    {
        $validated = $request->validated();
        $store = $this->storeRepository->paginate($request->merge($validated));
        return $this->onSuccess($store);
    }
}
