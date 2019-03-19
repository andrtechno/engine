<?php

namespace panix\engine\widgets\inputmask;

use panix\engine\web\AssetBundle;

class InputMaskAsset extends AssetBundle {

    public $sourcePath = __DIR__ . '/assets';
    public $js = [
        'dist/inputmask/inputmask.js',
        'dist/inputmask/inputmask.extensions.js',
        'dist/inputmask/inputmask.numeric.extensions.js',
        'dist/inputmask/inputmask.date.extensions.js',
        'dist/inputmask/jquery.inputmask.js',
        
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
