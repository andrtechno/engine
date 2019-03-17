<?php

namespace panix\engine\assets;

use panix\engine\web\AssetBundle;

/**
 * Class ValidationAsset
 * @package panix\engine\assets
 */
class ValidationAsset extends AssetBundle
{

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
