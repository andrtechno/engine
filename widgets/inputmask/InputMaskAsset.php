<?php

namespace panix\engine\widgets\inputmask;

use panix\engine\web\AssetBundle;

class InputMaskAsset extends AssetBundle {

    public $sourcePath = __DIR__ . '/assets';
    public $js = [
        'js/inputmask.min.js',
        'js/jquery.inputmask.min.js',
        'js/inputmask.phone.extensions.min.js',
        
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
