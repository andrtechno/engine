<?php

namespace panix\engine\assets\codemirror;

/**
 * Class CodeMirrorSearchAsset
 * @package panix\engine\assets
 */
class CodeMirrorSearchAsset extends CodeMirrorBundle
{

    public $js = [
        'addon/search/search.js',
        'addon/search/searchcursor.js',
        'addon/search/jump-to-line.js'

    ];

    public $depends = [
        'panix\engine\assets\codemirror\CodeMirrorAsset',
        'panix\engine\assets\codemirror\CodeMirrorDialogAsset',
    ];
}
