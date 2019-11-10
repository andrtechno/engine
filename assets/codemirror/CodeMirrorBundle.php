<?php

namespace panix\engine\assets\codemirror;

use yii\web\AssetBundle;
use yii\web\View;

class CodeMirrorBundle extends AssetBundle
{
    public $jsOptions = [
        'position' => View::POS_END
    ];

    public $sourcePath = '@bower/codemirror';

}