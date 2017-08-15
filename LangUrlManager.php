<?php

namespace panix\engine;
use Yii;
use yii\web\UrlManager;
use app\models\Lang;

class LangUrlManager extends UrlManager {
   public function init() {

        //  print_r($this->rules);
        //  die;
        $this->modulesRoutes();
        parent::init();
    }
    public function createUrl($params) {
        if (isset($params['lang_id'])) {
            //Если указан идентификатор языка, то делаем попытку найти язык в БД,
            //иначе работаем с языком по умолчанию
            $lang = Lang::findOne($params['lang_id']);
            if ($lang === null) {
                $lang = Lang::getDefaultLang();
            }
            unset($params['lang_id']);
        } else {
            //Если не указан параметр языка, то работаем с текущим языком
            $lang = Lang::getCurrent();
        }

        //Получаем сформированный URL(без префикса идентификатора языка)
        $url = parent::createUrl($params);

        //Добавляем к URL префикс - буквенный идентификатор языка
        // $locale = ($lang->default==1) ? '/'.$url: $lang->url;
        $locale = $lang->url;

        if ($lang->default) {
            $result = $url;
        } else {
            $result = '/' . $locale . $url;
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
        
      //print_r($this->rules);
      // die;
    }
}