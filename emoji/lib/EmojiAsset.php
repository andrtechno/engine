<?php

/**
 * EmojiAsset
 *
 * @package vendor.tangniyuqi.yii2-emoji
 * @author tangming <tangniyuqi@163.com>
 * @copyright DNA <http://www.Noooya.com/>
 * @version 1.0.0
 * @since 2017-05-18 Create
 * @todo N/A
 */

namespace panix\engine\emoji\lib;

use Yii;

class EmojiAsset extends \yii\web\AssetBundle {

    /**
     * @inheritdoc
     */
    public $css = [
        'emoji.css',
    ];

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        $this->sourcePath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}
