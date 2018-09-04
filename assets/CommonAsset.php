<?php

namespace panix\engine\assets;

use yii\web\AssetBundle;

class CommonAsset extends AssetBundle {

    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    public $sourcePath = '@vendor/panix/engine/assets';
    public $js = [
        'js/common.js',
    ];
    public $css = [
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
