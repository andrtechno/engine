<?php

namespace panix\engine\bootstrap;

class DropdownAsset extends \panix\engine\web\AssetBundle {

    public $sourcePath = "@vendor/panix/engine/bootstrap/assets";
    public $css = [
        'css/dropdown.css'
    ];
    public $js = [
        'js/dropdown.js',
        //'js/dropdown-hover.js'
    ];

}
