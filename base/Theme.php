<?php

namespace panix\engine\base;

use Yii;
use yii\helpers\ArrayHelper;

class Theme extends \yii\base\Theme {

    public $name;

    public function init() {
        $this->name = \Yii::$app->settings->get('app', 'theme');
        $this->basePath = "@app/web/themes/{$this->name}";
        $modulesPaths = [];
        foreach (Yii::$app->getModules() as $id => $mod) {
            $modulesPaths['@' . $id] = "@app/web/themes/{$this->name}/modules/{$id}";
        }

        $this->pathMap = ArrayHelper::merge([
                    "@app/views" => "@app/web/themes/{$this->name}/views",
                        ], $modulesPaths);

        $this->baseUrl = "@app/web/themes/{$this->name}";
        parent::init();
    }

}
