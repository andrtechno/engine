<?php

namespace panix\engine\grid\columns;

use panix\engine\web\AssetBundle;

/**
 * Class CheckboxColumnAsset
 * @package app\backend\themes\dashboard\assets
 */
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
