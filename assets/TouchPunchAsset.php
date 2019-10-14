<?php

namespace panix\engine\assets;

use panix\engine\web\AssetBundle;

/**
 * Class TouchPunchAsset
 * @package panix\engine\assets
 */
class TouchPunchAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        YII_DEBUG ? 'js/jquery.ui.touch-punch.js' : 'js/jquery.ui.touch-punch.min.js',
    ];

    public $depends = [
        'yii\jui\JuiAsset',
        'yii\web\JqueryAsset',
    ];
}
