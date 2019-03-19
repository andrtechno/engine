<?php

namespace panix\engine\widgets\webcontrol;

use panix\engine\web\AssetBundle;

class WebInlineAsset extends AssetBundle {

    public $sourcePath = __DIR__ . '/assets';
    public $css = [
        'css/style.css'
    ];
    public $js = [
        'js/jquery.cookie.js',
        'js/webcontrol.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'panix\engine\assets\IconAsset',
        'panix\engine\widgets\notify\NotifyAsset',
        'panix\mod\admin\assets\AdminCountersAsset',
    ];

}
