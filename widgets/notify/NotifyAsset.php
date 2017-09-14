<?php

namespace panix\engine\widgets\notify;

use yii\web\AssetBundle;

class NotifyAsset extends AssetBundle {

    public $sourcePath = '@vendor/panix/engine/widgets/notify/assets';
        public $jsOptions = array(
        'position' => \yii\web\View::POS_HEAD
    );
    public $js = [
        'bootstrap-notify.min.js',
    ];

}
