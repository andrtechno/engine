<?php

namespace panix\engine\web;

use Yii;

class AssetBundle extends \yii\web\AssetBundle {

    public function init() {
               
        //if ($this->sourcePath !== null) {
            $path = dirname(get_class($this));
            $this->sourcePath = Yii::getAlias("@vendor/{$path}") . '/assets';
        //}
 parent::init();

    }

}
