<?php

namespace panix\engine\data;

use Yii;
use yii\data\ActiveDataProvider as BaseActiveDataProvider;

class ActiveDataProvider extends BaseActiveDataProvider
{

    public function init()
    {
        /* @var  \yii\base\Model $modelClass*/
        $modelClass = $this->query->modelClass;

        $moduleId = $modelClass::MODULE_ID;
        $settings = Yii::$app->settings;

        if ($moduleId && Yii::$app->controller->dashboard) {
            if ($settings->get($moduleId, 'pagenum')) {
                $this->getPagination()->pageSize = $settings->get($moduleId, 'pagenum');
            } else {

                //$this->getPagination()->pageSize = $settings->get('app', 'pagenum');
            }
        }
        parent::init();


    }

}
