<?php

namespace panix\engine\assets\codemirror;

/**
 * Class CodeMirrorAsset
 * @package panix\engine\assets
 */
class CodeMirrorAsset extends CodeMirrorBundle
{

    public $js = [
        'lib/codemirror.js'
    ];

    public $css = [
        'lib/codemirror.css',
        'theme/dracula.css',
    ];

    /*public $depends = [
        'yii\web\YiiAsset',
    ];*/
}
