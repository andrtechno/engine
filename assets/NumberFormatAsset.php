<?php

namespace panix\engine\assets;

use panix\engine\web\AssetBundle;

/**
 * Class NumberFormatAsset
 * @package panix\engine\assets
 */
class NumberFormatAsset extends AssetBundle
{

    public $sourcePath = '@vendor/panix/engine/assets';

    public $js = [
        'js/number_format.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

}
