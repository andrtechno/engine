<?php

namespace panix\engine;

use Yii;
use yii\web\UrlManager;
use yii\helpers\Url;

class ManagerUrl extends UrlManager {

    public function init() {
        $this->modulesRoutes();
        parent::init();
    }

    public function createUrl($params) {
        $result = parent::createUrl($params);
        if ($respectLang === true) {
            $langPrefix = Yii::app()->languageManager->getUrlPrefix();
            if ($langPrefix)
                $result = '/' . $langPrefix . $result;
        }

        return $result;
        //return Url::to(['dsa','dsa'=>1]);
    }

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

}
