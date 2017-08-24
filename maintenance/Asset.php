<?php

namespace panix\engine\maintenance;

class Asset extends \yii\web\AssetBundle {

    public $sourcePath = '@vendor/panix/engine/maintenance/assets';
    public $css = [
        YII_ENV_DEV ? 'css/styles.css' : 'css/styles.min.css',
    ];
    public $js = [];
    public $depends = [
        'yii\web\YiiAsset',

    ];

}
