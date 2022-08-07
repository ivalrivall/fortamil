<?php

namespace App\Interfaces;

use App\Interfaces\BaseRepositoryInterface;

interface RegionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllProvince();
    public function getAllCityRepo();
    public function getAllDistrictRepo();
    public function getProvince(int $provinceId);
    public function getCityByProvince(int $provinceId);
    public function getCity(int $cityId);
    public function getDistrictByCity(int $cityId);
    public function getDistrict(int $districtId);
    public function getVillageByDistrict(int $districtId);
    public function getVillage(int $villageId);
    public function getRegionByName(array $payload);
    public function getPaginateRegionByName(array $payload);
}
