<?php
namespace panix\engine\widgets;
use yii\web\AssetBundle;
/**
 * This asset bundle provides the javascript files required by [[Pjax]] widget.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class PjaxAsset extends AssetBundle
{
    public $sourcePath = '@vendor/panix/engine/widgets/assets';
    public $js = [
        'jquery.pjax.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}