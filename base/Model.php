<?php

namespace panix\engine\base;

use Yii;
use yii\base\InvalidConfigException;
class Model extends \yii\base\Model {

    protected $_attrLabels = [];

    public function attributeLabels() {
        foreach ($this->attributes as $attr => $val) {
            $this->_attrLabels[$attr] = Yii::t($this->module . '/' . basename(get_class($this)), strtoupper($attr));
        }
        return $this->_attrLabels;
    }

    public function init() {
        if (!isset($this->category)) {
            throw new InvalidConfigException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => 'category'
            ]));
        }
        if (!isset($this->module)) {
            throw new InvalidConfigException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => 'module'
            ]));
        }
        $this->setAttributes(Yii::$app->settings->get($this->category));
    }

    public function save() {
        if ($this->validate()) {
            Yii::$app->settings->set($this->category, Yii::$app->request->post(basename(get_class($this))));
            Yii::$app->session->addFlash("success", Yii::t('app', 'SUCCESS_UPDATE'));
            return true;
        } else {
            Yii::$app->session->addFlash("error", Yii::t('app', 'ERROR_UPDATE'));
            return false;
        }
    }

}
