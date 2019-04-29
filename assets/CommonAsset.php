<?php

namespace panix\engine\assets;

use panix\engine\web\AssetBundle;

class CommonAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        'js/common.js',
    ];

    public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
        'panix\asset\bootstrap\notify\Asset',
        'panix\engine\assets\IconAsset'
    ];

}
