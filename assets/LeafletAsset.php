<?php

namespace panix\engine\assets;

use panix\engine\web\AssetBundle;

class LeafletAsset extends AssetBundle
{

    public $sourcePath = '@npm/leaflet/dist';

    public $js = [
        'leaflet.js',
    ];

    public $css = [
        'leaflet.css',
    ];

    public $depends = [
       // 'panix\engine\assets\NumberFormatAsset',
    ];
}
