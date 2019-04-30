<?php

namespace panix\engine\base;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class Theme extends \yii\base\Theme
{

    public $name;

    public function init()
    {
        if ($this->name == null) {
            $this->name = Yii::$app->settings->get('app', 'theme');
        }

        if (preg_match("/admin/", Yii::$app->request->getUrl())) {
            $this->name = 'dashboard';
        }

        $this->basePath = "@app/web/themes/{$this->name}";
        $this->baseUrl = "@app/web/themes/{$this->name}";
        if(!file_exists(Yii::getAlias($this->basePath))){
            die("Error: theme \"{$this->name}\" not found!");
        }

        $modulesPaths = [];
        foreach (Yii::$app->getModules() as $id => $mod) {
            $modulesPaths['@' . $id] = "@app/web/themes/{$this->name}/modules/{$id}";
            //  $modulesPaths['@app/modules/' . $id] = "@frontend/themes/{$this->name}/modules/{$id}";
        }

        $this->pathMap = ArrayHelper::merge([
            "@app/views" => "@app/web/themes/{$this->name}/views",
            '@app/modules' => "@app/web/themes/{$this->name}/modules",
            '@app/widgets' => "@app/web/themes/{$this->name}/widgets",
        ], $modulesPaths);

        parent::init();
    }

}
