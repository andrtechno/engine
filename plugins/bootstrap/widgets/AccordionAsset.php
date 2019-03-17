<?php

namespace panix\engine\plugins\bootstrap\widgets;

use yii\web\AssetBundle;

/**
 * Class AccordionAsset
 * @package panix\engine\plugins\bootstrap\widgets
 */
class AccordionAsset extends AssetBundle
{
    public $css = [
        'collapse.css',
    ];

    public $depends = [
        'yii\bootstrap4\BootstrapPluginAsset'
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }
}