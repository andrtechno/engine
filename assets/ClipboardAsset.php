<?php

namespace panix\engine\assets;

use yii\web\AssetBundle;

class ClipboardAsset extends AssetBundle {

    public $sourcePath = '@vendor/panix/engine/assets';
    public $js = [
        'js/clipboard/clipboard.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
