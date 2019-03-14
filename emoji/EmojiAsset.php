<?php

namespace panix\engine\emoji;

use Yii;

class EmojiAsset extends \yii\web\AssetBundle {

    /**
     * @inheritdoc
     */
    public $css = [
        'css/emoji.css',
    ];

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR.'assets';
    }

}
