<?php

namespace panix\engine\base;

use Yii;


class Model extends \yii\base\Model {

    protected $_attrLabels = [];

    public function attributeLabels() {
        $fileName = (new \ReflectionClass(get_called_class()))->getShortName();
        foreach ($this->attributes as $attr => $val) {
            $this->_attrLabels[$attr] = Yii::t($this->module . '/' . $fileName, strtoupper($attr));
        }
        return $this->_attrLabels;
    }



    public static function t($message, $params = array()) {
        $class = (new \ReflectionClass(get_called_class()));
        $runClass = new $class->name;
        return Yii::t($runClass->module . '/' . $class->getShortName(), $message, $params);
    }

}
