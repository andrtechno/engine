<?php

namespace panix\engine\assets;

use panix\engine\web\AssetBundle;

class LeafletAsset extends AssetBundle
{

    public $sourcePath = '@npm/leaflet/dist';

    public $js = [
        'Leaflet.js',
    ];

    public $css = [
        'leaflet.css',
    ];

}
