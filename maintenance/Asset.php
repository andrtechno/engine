<?php

namespace panix\engine\maintenance;

use yii\web\AssetBundle;

/**
 * Class Asset
 * @package panix\engine\maintenance
 */
class Asset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__ . '/assets';
    /**
     * @inheritdoc
     */
    public $css = [
        YII_ENV_DEV ? 'css/styles.css' : 'css/styles.min.css',
    ];
    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
    ];
}