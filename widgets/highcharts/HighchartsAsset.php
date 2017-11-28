<?php

namespace panix\engine\widgets\highcharts;

use panix\engine\web\AssetBundle;

class HighchartsAsset extends AssetBundle {

    public $sourcePath = __DIR__ . '/assets';
    public $css = [

    ];
    public $js = [
        'highcharts.js',

    ];

    /**
     * @inheritdoc
     */
    public $depends = [
    ];

}
