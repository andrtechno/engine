<?php

namespace panix\engine\web;

use Yii;
use yii\web\View;

class AssetBundle extends \yii\web\AssetBundle
{

    public $jsOptions = [
        'position' => View::POS_END
    ];

    public function init()
    {
        parent::init();
    }

}
