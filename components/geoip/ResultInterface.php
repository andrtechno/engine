<?php

namespace panix\engine\components\geoip;


interface ResultInterface
{

    public function getCity();

    public function getCountry();

    public function getLocation();

    public function getTimezone();

    public function getCountryCode();

    public function getOrg();

    public function getRegion();

    public function getRegionCode();
}
