<?php

namespace panix\engine\api;

use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Url;
use yii\web\UrlManager;

class ApiHelpers
{

    /**
     * Remove url prefix "/api
     * @param $route
     * @return string|string[]
     */
    public static function url($route, $scheme = false)
    {
        return str_replace(Yii::$app->request->baseUrl, '', Url::to($route, $scheme));
    }

}