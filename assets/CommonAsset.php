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
        'css/corner-icons.css',
    ];
    public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'panix\engine\widgets\notify\NotifyAsset',
    ];

}
