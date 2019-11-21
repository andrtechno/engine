<?php

namespace panix\engine\grid\sortable;

use yii\web\AssetBundle;

class SortableAsset extends AssetBundle
{

    public $sourcePath = __DIR__ . '/assets';

    public $css = [
        'styles.css',
    ];
    public $depends = [
        'panix\engine\assets\CommonAsset',
    ];
}
