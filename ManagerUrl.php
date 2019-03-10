<?php

namespace panix\engine;

use Yii;
use yii\web\UrlManager;
use yii\helpers\Url;

class ManagerUrl extends UrlManager {

    public function __init() {
        $this->modulesRoutes();
        parent::init();
    }

    public function createUrl($params, $respectLang = true) {
        $result = parent::createUrl($params);
        if ($respectLang === true) {
            $langPrefix = Yii::$app->languageManager->getUrlPrefix();
            if ($langPrefix) {
                $result = '/' . $langPrefix . $result;
            }
        }
        return $result;
    }


    /**
     * @deprecated use BootstrapModule Class
     */
    protected function modulesRoutes() {
        $cacheKey = 'url_manager';
        $rules = Yii::$app->cache->get($cacheKey);
        if (YII_DEBUG || !$rules) {
            $modules = Yii::$app->getModules();
            $rules = array();
            foreach ($modules as $mod => $params) {
                $run = Yii::$app->getModule($mod, true);
                if (isset($run->routes)) {
                    $rules = array_merge($run->routes, $rules);
                }
            }
            Yii::$app->cache->set($cacheKey, $rules, 3600 * 24);
        }
        $this->rules = array_merge($rules, $this->rules);
    }

    /**
     * Add param to current url. Url is based on $data and $_GET arrays
     *
     * @param $route
     * @param $data array of the data to add to the url.
     * @param $selectMany
     * @return string
     */
    public function addUrlParam($route, $data, $selectMany = false) {
        foreach ($data as $key => $val) {
            if (isset($_GET[$key]) && $key !== 'url' && $selectMany === true) {
                $tempData = explode(',', $_GET[$key]);
                $data[$key] = implode(',', array_unique(array_merge((array) $data[$key], $tempData)));
            }
        }

        return $this->createUrl(array_merge([$route], array_merge($_GET, $data)));
    }

    /**
     * Delete param/value from current
     *
     * @param string $route
     * @param string $key to remove from query
     * @param null $value If not value - delete whole key
     * @return string new url
     */
    public function removeUrlParam($route, $key, $value = null) {
        $get = Yii::$app->request->get();
        if (isset($get[$key])) {
            if ($value === null)
                unset($get[$key]);
            else {
                $get[$key] = explode(',', $get[$key]);
                $pos = array_search($value, $get[$key]);
                // Delete value
                if (isset($get[$key][$pos]))
                    unset($get[$key][$pos]);
                // Save changes
                if (!empty($get[$key]))
                    $get[$key] = implode(',', $get[$key]);
                // Delete key if empty
                else
                    unset($get[$key]);
            }
        }
        return $this->createUrl(array_merge([$route], $get));
    }

}
