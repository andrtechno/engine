<?php

namespace panix\engine\widgets\langSwitcher;

use Yii;
use yii\base\Component;
use yii\base\Widget;
use yii\bootstrap4\ButtonDropdown;
use yii\helpers\Url;
use yii\web\Cookie;

class LangSwitcher extends Widget {

    public function init() {
        if (php_sapi_name() === 'cli') {
            return true;
        }

        parent::init();
    }

    public function run() {
        $langManager = Yii::$app->languageManager;
        $languages = $langManager->getLanguages();
        $currentDataArray = [];
        foreach ($languages as $l) {
            $currentDataArray[$l->code] = $l->name;
        }

        $current = $currentDataArray[Yii::$app->language];

        if (count($languages) > 1) {
            foreach ($languages as $lang) {
                $class = ($langManager->active->id == $lang->id) ? 'active' : '';
                $link = ($lang->is_default) ? \panix\engine\CMS::currentUrl() : '/' . $lang->code . \panix\engine\CMS::currentUrl();


             //   echo \yii\helpers\Html::a($lang->code, $link, array('class' => 'text-uppercase'));
            }
        }

        $items = [];
        foreach ($languages as $lang) {

            $link = ($lang->is_default) ? \panix\engine\CMS::currentUrl() : '/' . $lang->code . \panix\engine\CMS::currentUrl();
            $class = ($langManager->active->id == $lang->id) ? 'active' : '';
            $temp = [];
            $temp['label'] = $lang->name;
            $temp['url'] = $link;
            $temp['options']=['class'=>$class];
            array_push($items, $temp);
        }

        echo ButtonDropdown::widget([
            'label' => $current,
            'dropdown' => [
                'items' => $items,
            ],
            'options'=>['class'=>'btn btn-sm btn-default'],
        ]);
    }

}
