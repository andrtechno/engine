<?php

namespace panix\engine\api;

use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Url;
use yii\web\UrlManager;

class ApiUrl
{
    public static $urlManager;

    public static function to($url = '', $scheme = false)
    {
        if (is_array($url)) {
            return static::toRoute($url, $scheme);
        }

        $url = Yii::getAlias($url);
        if ($url === '') {
            $url = Yii::$app->getRequest()->getUrl();
        }

        if ($scheme === false) {
            return $url;
        }

        if (static::isRelative($url)) {
            // turn relative URL into absolute
            $url = static::getUrlManager()->getHostInfo() . '/' . ltrim($url, '/');
        }

        return static::ensureScheme($url, $scheme);
    }


    private static function toRoute($route, $scheme = false)
    {
        $route = (array)$route;
        $route[0] = self::normalizeRoute($route[0]);

        if ($scheme !== false) {
            return static::getUrlManager()->createAbsoluteUrl($route, is_string($scheme) ? $scheme : null);
        }


        return static::getUrlManager()->createUrl($route);
    }

    public static function normalizeRoute($route)
    {
        $route = Yii::getAlias((string)$route);
        if (strncmp($route, '/', 1) === 0) {
            // absolute route
            return ltrim($route, '/');
        }

        // relative route
        if (Yii::$app->controller === null) {
            throw new InvalidArgumentException("Unable to resolve the relative route: $route. No active controller is available.");
        }

        if (strpos($route, '/') === false) {
            // empty or an action ID
            return $route === '' ? Yii::$app->controller->getRoute() : Yii::$app->controller->getUniqueId() . '/' . $route;
        }

        // relative to module
        return ltrim(Yii::$app->controller->module->getUniqueId() . '/' . $route, '/');

    }

    protected static function getUrlManager()
    {

        $urlManger = new UrlManager();
        $urlManger->enablePrettyUrl = true;
        $urlManger->enableStrictParsing = true;
        $urlManger->showScriptName = false;
        $urlManger->normalizer = [
            'class' => 'yii\web\UrlNormalizer',
            'action' => \yii\web\UrlNormalizer::ACTION_REDIRECT_TEMPORARY,
        ];

        return static::$urlManager ?: $urlManger;
    }
}