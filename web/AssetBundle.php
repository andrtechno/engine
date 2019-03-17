<?php

namespace panix\engine\web;

use Yii;

class AssetBundle extends \yii\web\AssetBundle
{

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];

    public function init()
    {
        parent::init();
    }

}
