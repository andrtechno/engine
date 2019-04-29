<?php

namespace panix\engine\assets;

use panix\engine\web\AssetBundle;

/**
 * Class ErrorAsset
 * @package panix\engine\assets
 */
class ErrorAsset extends AssetBundle {

    public $sourcePath = __DIR__ . '/assets';

    public $css = [
        'css/error.css',
    ];


}
