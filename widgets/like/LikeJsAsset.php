<?php

namespace panix\engine\widgets\like;

use yii\web\AssetBundle;

class LikeJsAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        'js/like.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}