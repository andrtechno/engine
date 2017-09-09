<?php

namespace panix\engine\assets;

use yii\web\AssetBundle;

class IconAsset extends AssetBundle {

    public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    public $sourcePath = '@vendor/panix/engine/assets';

    public $css = [
        'css/corner-icons.css',
    ];


}
