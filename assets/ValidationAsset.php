<?php

namespace panix\engine\assets;

use yii\web\AssetBundle;

class ValidationAsset extends AssetBundle {

    public $sourcePath = '@vendor/panix/engine/assets';
    public $js = [
        //'js/panix.validation.js',
        'js/translitter.js',
        'js/init_translitter.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
