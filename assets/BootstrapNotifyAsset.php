<?php

namespace panix\engine\assets;

use yii\web\View;
use yii\web\AssetBundle;

/**
 * Class Asset
 * @package panix\engine\assets
 */
class BootstrapNotifyAsset extends AssetBundle
{

    public $jsOptions = [
        'position' => View::POS_END
    ];

    public $sourcePath = '@bower/remarkable-bootstrap-notify';

    public $js = [
        'bootstrap-notify.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
