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
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

}
