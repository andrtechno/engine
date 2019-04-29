<?php

namespace panix\engine\assets;

use panix\engine\web\AssetBundle;

/**
 * Class ClipboardAsset
 * @package panix\engine\assets
 */
class ClipboardAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        'js/clipboard/clipboard.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
