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
   // public $languages = [];
    /*public function createUrl3($params) {

        if (isset($params['lang_id'])) {
            //Если указан идентификатор языка, то делаем попытку найти язык в БД,
            //иначе работаем с языком по умолчанию
            $lang = \panix\mod\admin\models\Languages::findOne($params['lang_id']);
            if ($lang === null) {
                $lang = \panix\mod\admin\models\Languages::getDefaultLang();
            }
            unset($params['lang_id']);
        } else {
            //Если не указан параметр языка, то работаем с текущим языком
            $lang = \panix\mod\admin\models\Languages::getCurrent();
        }

        //Получаем сформированный URL(без префикса идентификатора языка)
        $url = parent::createUrl($params);

        //Добавляем к URL префикс - буквенный идентификатор языка
        if ($url == '/') {
            return '/' . $lang->code;
        } else {
            return '/' . $lang->code . $url;
        }
    }*/

    public function createUrl($params, $respectLang=true) {
        $result = parent::createUrl($params);
        if ($respectLang === true) {
            $langPrefix = Yii::$app->languageManager->getUrlPrefix();

            if ($langPrefix)
                $result = '/' . $langPrefix . $result;
        }

        return $result;
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
