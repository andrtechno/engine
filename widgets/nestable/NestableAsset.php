<?php

namespace panix\engine\widgets\nestable;

use yii\web\AssetBundle;

/**
 * Class NestableAsset
 * @package panix\engine\widgets\nestable
 */
class NestableAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@vendor/panix/engine/widgets/nestable/assets';

    /**
     * @var array
     */
    public $css = [
        'jquery.nestable.css'
    ];

    /**
     * @var array
     */
    public $js = [
        'jquery.nestable.js'
    ];

    /**
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset',
    ];
}