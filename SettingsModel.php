<?php

namespace panix\engine;

use Yii;
use panix\engine\base\Model;
use yii\base\InvalidConfigException;

class SettingsModel extends Model {

    public function init() {
        if (!isset($this->module)) {
            throw new InvalidConfigException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => 'module'
            ]));
        }
        $this->setAttributes(Yii::$app->settings->get($this->module));
    }

    public function save() {
        $shortName = (new \ReflectionClass(get_called_class()))->getShortName();
        if ($this->validate()) {
            Yii::$app->settings->set($this->module, Yii::$app->request->post($shortName));
            Yii::$app->session->addFlash("success", Yii::t('app', 'SUCCESS_UPDATE'));
            return true;
        } else {
            Yii::$app->session->addFlash("error", Yii::t('app', 'ERROR_UPDATE'));
            return false;
        }
    }

}

?>
