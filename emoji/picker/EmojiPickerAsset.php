<?php

namespace panix\engine\emoji\picker;

use yii\web\AssetBundle;

class EmojiPickerAsset extends AssetBundle {
  //  public $jsOptions = array(
      //  'position' => \yii\web\View::POS_HEAD
   // );
    public $sourcePath = __DIR__.'/assets';
    public $css = [
        'css/emoji.css',

    ];
    public $js = [
        'js/config.js',
        'js/util.js',
        'js/jquery.emojiarea.js',
        'js/emoji-picker.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
