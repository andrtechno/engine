<?php

namespace panix\engine\grid\columns;

use panix\engine\web\AssetBundle;

class CheckboxColumnAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';
        parent::init();
    }

    public $js = [
        'js/grid-checkbox.js',
    ];

    public $depends = [
        'yii\widgets\PjaxAsset',
    ];

}
