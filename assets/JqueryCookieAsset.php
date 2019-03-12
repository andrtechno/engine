<?php

namespace panix\engine\assets;

use yii\web\AssetBundle;

class JqueryCookieAsset extends AssetBundle {

    public $sourcePath = '@vendor/panix/engine/assets';
    public $js = [
        'js/jquery.cookie.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
