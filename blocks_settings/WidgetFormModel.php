<?php

namespace panix\engine\blocks_settings;

use Yii;

class WidgetFormModel extends \panix\engine\base\Model {

    public function attributeNames() {
        return array();
    }

    public function getSettings($obj) {
        return Yii::$app->settings->get($obj);
    }

    public function getConfigurationFormHtml($obj) {

        $className = basename(Yii::getAlias($obj));
        $this->attributes = $this->getSettings($className);
        //if (method_exists($this, 'registerScript')) {
        //    $this->registerScript();
        //}
        $form = new WidgetForm($this->getForm(), $this);
        return $form;
    }

    public function saveSettings($obj, $postData) {
        $this->setSettings($obj, $postData[get_class($this)]);
    }

    public function setSettings($obj, $data) {
        if ($data) {
            $className = basename(Yii::getAlias($obj));
            $cache = Yii::$app->cache->get(md5(Yii::$app->cache->keyPrefix . $className));
            if (isset($cache)) {
                Yii::$app->cache->delete($className);
            }
            Yii::$app->settings->set($className, $data);
        }
    }

}
