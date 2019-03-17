<?php

namespace panix\engine\assets;

use panix\engine\web\AssetBundle;

class CommonAsset extends AssetBundle
{

    public $sourcePath = '@vendor/panix/engine/assets';

    public $js = [
        'js/common.js',
    ];

    public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
        'panix\engine\widgets\notify\NotifyAsset',
        'panix\engine\assets\IconAsset'
    ];

}
