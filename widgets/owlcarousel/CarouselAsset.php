<?php

namespace panix\engine\widgets\owlcarousel;

use panix\engine\web\AssetBundle;

class CarouselAsset extends AssetBundle {

    public $sourcePath = __DIR__ . '/assets';
    public $css = [
        'css/owl.carousel.min.css',
        'css/owl.theme.default.min.css',
    ];
    public $js = [
        'js/owl.carousel.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
