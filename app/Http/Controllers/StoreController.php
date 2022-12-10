<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Http\Requests\BasePaginateRequest;
use App\Http\Requests\Store\StoreCreateRequest;
use App\Http\Requests\Store\StoreUpdateRequest;
use App\Interfaces\AddressRepositoryInterface;
use App\Interfaces\CloudinaryRepositoryInterface;
use App\Interfaces\StoreRepositoryInterface;
use App\Models\Address;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $pictureUrl = $this->cloudinary->upload(['file' => $request->file('picture'), 'folder' => 'store']);

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

    /**
     * edit store
     */
    public function update(StoreUpdateRequest $request) : JsonResponse
    {
        $validated = $request->validated();

        $slug = $this->createSlug($request->name);

        try {
            $store = $this->storeRepository->findById((int)$validated['id']);
        } catch (Exception $e) {
            return $this->onError('Toko tidak ditemukan');
        }

        if ($store->user_id !== $request->user()->id) {
            return $this->onError('Tidak diizinkan untuk mengubah toko', 403);
        }

        $pictureUrl = $store->picture;

        if ($request->file('picture') !== null) {
            $this->cloudinary->delete(pathinfo($store->picture, PATHINFO_FILENAME));
            $pictureUrl = $this->cloudinary->upload(['file' => $request->file('picture'), 'folder' => 'store']);
        }

        DB::beginTransaction();
        $this->storeRepository->update($validated['id'], [
            'name' => $validated['name'],
            'marketplace_id' => $validated['marketplace_id'],
            'picture' => $pictureUrl,
            'address' => $validated['address'],
            'slug' => $slug
        ]);

        try {
            $address = $this->addressRepository->findById((int)$validated['latest_address_id']);
        } catch (Exception $e) {
            return $this->onError('Alamat tidak ditemukan');
        }

        $address->update([
            'title' => $validated['address_title'],
            'recipient' => $validated['address_recipient'],
            'phone_recipient' => $validated['address_phone_recipient'],
            'city_id' => $validated['city_id'],
            'district_id' => $validated['district_id'],
            'province_id' => $validated['province_id'],
            'village_id' => $validated['village_id'],
            'postal_code' => $validated['postal_code']
        ]);

        DB::commit();
        return $this->onSuccess(null, 'Berhasil ubah toko');
    }

    /**
     * get by id
     */
    public function getById(Request $request, $storeId)
    {
        try {
            $store = $this->storeRepository->findById((int)$storeId, ['*'], ['latestAddress']);
        } catch (Exception $e) {
            return $this->onError('Toko tidak ditemukan');
        }

        if ($store->user_id !== $request->user()->id) {
            return $this->onError('Toko tidak ditemukan', 403);
        }

        return $this->onSuccess($store);
    }

    /**
     * delete by id
     */
    public function deleteById(Request $request, $storeId)
    {
        try {
            $store = $this->storeRepository->findById((int)$storeId, ['*'], ['latestAddress']);
        } catch (Exception $e) {
            return $this->onError('Toko tidak ditemukan');
        }

        if ($store->user_id !== $request->user()->id) {
            return $this->onError('Toko tidak ditemukan', 403);
        }

        try {
            $this->storeRepository->checkStoreHaveOnGoingOrder($store);
        } catch (Exception $e) {
            return $this->onError('Masih ada order berlangsung, tidak bisa menghapus toko ini');
        }

        $store->delete();

        return $this->onSuccess(null, 'Sukses menghapus toko');
    }
}
