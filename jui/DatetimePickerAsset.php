<?php

namespace panix\engine\jui;

use yii\web\AssetBundle;

class DatetimePickerAsset extends AssetBundle {

   // public $sourcePath = '@vendor/panix/engine/jui/assets';
    public $sourcePath = __DIR__ . '/assets';

    public $css = [
        'datetimepicker/jquery-ui-timepicker-addon.css',
    ];

    public $js = [
        'datetimepicker/jquery-ui-timepicker-addon.js',
    ];
    public $depends = [
        'yii\jui\JuiAsset',
    ];

}
