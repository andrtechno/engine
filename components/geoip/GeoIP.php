<?php


namespace panix\engine\components\geoip;

use Yii;
use yii\base\Component;
use yii\httpclient\Client;
use yii\web\Session;

/**
 * Class GeoIP
 */
class GeoIP extends Component
{
    /**
     * @var string
     */
    public $dbPath;

    /**
     * @var Session
     */
    private $session;
    public $url;

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->session = Yii::$app->session;
        $this->url = 'http://ip-api.com/json/%s?fields=status,message,continent,continentCode,country,countryCode,region,regionName,city,district,zip,lat,lon,timezone,currency,isp,org,as,asname,reverse,mobile,proxy,hosting,query&lang=' . Yii::$app->language;
        parent::init();
    }

    /**
     * @param string|null $ip
     * @return Result
     */
    public function ip($ip = null)
    {
        if ($ip === null) {
            $ip = Yii::$app->request->getRemoteIP();
        }

       // return Yii::$app->cache->getOrSet('IP_CACHE_' . $ip, function () use ($ip) {
            return new Result($this->connect($ip));
        //}, 86400 * 30);
    }


    private function connect($ip)
    {
        $client = new Client(['baseUrl' => sprintf($this->url, $ip)]);
        $response = $client->createRequest()
            ->setFormat(Client::FORMAT_JSON)
            //->addHeaders(['content-type' => 'application/json'])
            ->send();

        if ($response->isOk) {
            return $response->data;
        }
        return false;
    }

}
