<?php

namespace panix\engine\data;

class Widget extends \yii\base\Widget {

    public $skin = 'default';

    public function getTitle() {
        return basename(get_class($this));
    }

}
