<?php

namespace panix\engine\base;

use Yii;
use yii\helpers\ArrayHelper;
class Model extends \yii\base\Model {


    public function attributeLabels() {
        $class = (new \ReflectionClass(get_called_class()));
        $labels = [];

        foreach ($this->attributes as $attr => $val) {
            $labels[$attr] = Yii::t($this->module . '/' . $class->getShortName(), strtoupper($attr));
        }
        return $labels;
    }

    public static function t($message, $params = array()) {
        $class = (new \ReflectionClass(get_called_class()));

        $runClass = new $class->name;
        return Yii::t($runClass->module . '/' . $class->getShortName(), $message, $params);
    }

}
