<?php

namespace panix\engine\widgets\like;

use yii\web\AssetBundle;

class LikeAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        'js/like.js',
    ];

    public $css = [
        'css/like.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}