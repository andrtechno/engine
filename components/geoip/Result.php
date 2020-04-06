<?php

namespace panix\engine\components\geoip;

use panix\engine\CMS;
use Yii;
use yii\base\BaseObject;

/**
 * Class Result
 * @package lysenkobv\GeoIP
 *
 * @property string|null city
 * @property string|null country
 * @property Location location
 */
class Result extends BaseObject implements ResultInterface
{
    /**
     * @var array
     */
    private $result = null;
    /**
     * @var array
     */
    private $_response = [];


    public function __construct($data,$config = [])
    {
        $this->_response = $data;
        parent::__construct($config);

    }

    public function getCity()
    {
        if (isset($this->_response['city'])) {
            $this->result = $this->_response['city'];
        }
        return $this->result;
    }
    public function getCountryCode()
    {
        if (isset($this->_response['countryCode'])) {
            $this->result = $this->_response['countryCode'];
        }
        return $this->result;
    }

    public function getCountry()
    {
        if (isset($this->_response['country'])) {
            $this->result = $this->_response['country'];
        }
        return $this->result;
    }

    public function getLocation()
    {
        if (isset($this->_response['lat']) && isset($this->_response['lon'])) {
            $this->result = new Location($this->_response['lat'], $this->_response['lon']);
        }
        return $this->result;
    }


    public function getTimezone()
    {
        if (isset($this->_response['timezone'])) {
            $this->result = $this->_response['timezone'];
        }
        return $this->result;
    }
    public function getOrg()
    {
        if (isset($this->_response['org'])) {
            return $this->_response['org'];
        }
       // return $this->result;
    }


    public function getRegion()
    {
        if (isset($this->_response['regionName'])) {
            return $this->_response['regionName'];
        }
        //return $this->_region;
    }
    public function getRegionCode()
    {
        if (isset($this->_response['region'])) {
            return $this->_response['region'];
        }
      //  return $this->result;
    }
    public function isDetected()
    {
        return ($this->location->lat !== null && $this->location->lng !== null);
    }

}
