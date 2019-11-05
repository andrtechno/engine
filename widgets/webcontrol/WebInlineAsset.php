<?php

namespace panix\engine\widgets\webcontrol;

use panix\engine\web\AssetBundle;

/**
 * Class WebInlineAsset
 * @package panix\engine\widgets\webcontrol
 */
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
        'panix\engine\assets\BootstrapNotifyAsset',
    ];

}
