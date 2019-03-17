<?php

namespace panix\engine\assets;

use panix\engine\web\AssetBundle;

/**
 * Class JqueryCookieAsset
 * @package panix\engine\assets
 */
class JqueryCookieAsset extends AssetBundle
{

    public $sourcePath = '@vendor/panix/engine/assets';
    public $js = [
        'js/jquery.cookie.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
