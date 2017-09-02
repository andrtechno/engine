<?php

namespace panix\engine\base;

class Theme extends \yii\base\Theme {

    public $name;

    public function init() {
        $this->name = \Yii::$app->settings->get('app', 'theme');
        $this->basePath = "@webroot/themes/{$this->name}";
        $this->pathMap = [
            '@app/views' => "@webroot/themes/{$this->name}/views",
            '@app/modules' => "@webroot/themes/{$this->name}/modules",
            '@app/widgets' => "@webroot/themes/{$this->name}/widgets",
            //'@app/views/layouts' => "@webroot/themes/{$this->name}",
        ];
        $this->baseUrl = "@webroot/themes/{$this->name}";
        parent::init();
    }

}
