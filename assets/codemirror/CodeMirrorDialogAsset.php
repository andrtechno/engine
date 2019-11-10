<?php

namespace panix\engine\assets\codemirror;

/**
 * Class CodeMirrorDialogAsset
 * @package panix\engine\assets
 */
class CodeMirrorDialogAsset extends CodeMirrorBundle
{

    public $js = [
        'addon/dialog/dialog.js',
    ];
    public $css = [
        'addon/dialog/dialog.css',
    ];

    public $depends = [
        'panix\engine\assets\codemirror\CodeMirrorAsset',
    ];
}
