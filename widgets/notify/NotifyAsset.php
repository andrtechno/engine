<?php

namespace panix\engine\widgets\notify;

use yii\web\AssetBundle;

class NotifyAsset extends AssetBundle {

    public $sourcePath = '@vendor/panix/engine/widgets/notify/assets';

    public $js = [
        'bootstrap-notify.min.js',
    ];

}
