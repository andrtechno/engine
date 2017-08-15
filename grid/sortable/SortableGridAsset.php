<?php
/**
 * @link https://github.com/himiklab/yii2-sortable-grid-view-widget
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace panix\engine\grid\sortable;

use yii\web\AssetBundle;

class SortableGridAsset extends AssetBundle
{
    public $sourcePath = '@vendor/panix/engine/grid/sortable/assets';

    public $js = [
        'js/jquery.sortable.gridview.js',
    ];

    public $depends = [
        'yii\jui\JuiAsset',
    ];
}
