<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Interfaces\RegionRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    use ApiHelpers;
    private RegionRepositoryInterface $region;
    public function __construct(
        RegionRepositoryInterface $region
    ) {
        $this->region = $region;
    }

    public function getProvinces()
    {
        try {
            $response = $this->region->getAllProvince();
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getProvince($provinceId)
    {
        try {
            $response = $this->region->getProvince($provinceId);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getCities($provinceId)
    {
        try {
            $response = $this->region->getCityByProvince($provinceId);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getAllCities()
    {
        try {
            $response = $this->region->getAllCityRepo();
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getCity($cityId)
    {
        try {
            $response = $this->region->getCity($cityId);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getDistricts($cityId)
    {
        try {
            $response = $this->region->getDistrictByCity($cityId);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getDistrict($districtId)
    {
        try {
            $response = $this->region->getDistrict($districtId);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getVillages($districtId)
    {
        try {
            $response = $this->region->getVillageByDistrict($districtId);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getVillage($villageId)
    {
        try {
            $response = $this->region->getVillage($villageId);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getRegions(Request $request)
    {
        $data = $request->only([
            'name',
            'regionType'
        ]);
        try {
            $response = $this->region->getRegionByName($data);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getPaginateRegions(Request $request)
    {
        $data = $request->only([
            'name',
            'regionType'
        ]);
        try {
            $response = $this->region->getPaginateRegionByName($data);
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }

    public function getAllDistricts()
    {
        try {
            $response = $this->region->getAllDistrictRepo();
        } catch (Exception $e) {
            return $this->onError($e->getMessage());
        }
        return $this->onSuccess($response);
    }
}
