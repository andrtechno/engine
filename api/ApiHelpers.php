<?php

namespace panix\engine\api;

use Yii;
use yii\helpers\Url;

class ApiHelpers
{
    /**
     * Remove url prefix "/api
     * @param $route
     * @return string|string[]
     */
    public static function url($route)
    {
        return str_replace(Yii::$app->request->baseUrl, '', Url::to($route));
    }
}