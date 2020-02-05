<?php

namespace panix\engine\data;

use Yii;
use yii\data\ActiveDataProvider as BaseActiveDataProvider;

/**
 * Class ActiveDataProvider
 * @package panix\engine\data
 */
class ActiveDataProvider extends BaseActiveDataProvider
{

    public function init()
    {
        /* @var \yii\base\Model $modelClass */
        $modelClass = $this->query->modelClass;

        $this->setPagination([
            'class' => Pagination::class,
        ]);

        $moduleId = $modelClass::MODULE_ID;
        $settings = Yii::$app->settings;

        if ($moduleId && !Yii::$app->controller->dashboard) {
            if ($settings->get($moduleId, 'pagenum')) {
                $this->getPagination()->pageSize = $settings->get($moduleId, 'pagenum');
            }
        }
        parent::init();
    }

}
