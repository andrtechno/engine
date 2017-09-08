<?php


namespace panix\engine\base;

use Yii;
use yii\helpers\ArrayHelper;

trait TranslationTrait {

    public function init_i18n($dir = '', $cat = '') {
        if (empty($cat)) {
            return;
        }

        if (empty($dir)) {
            $reflector = new \ReflectionClass(get_class($this));
            $dir = dirname($reflector->getFileName());
        }
        Yii::setAlias("@{$cat}", $dir);
        $config = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => "@{$cat}/messages",
            'forceTranslation' => true
        ];
        $globalConfig = ArrayHelper::getValue(Yii::$app->i18n->translations, "{$cat}*", []);
        if (!empty($globalConfig)) {
            $config = array_merge($config, is_array($globalConfig) ? $globalConfig : (array) $globalConfig);
        }
        if (!empty($this->i18n) && is_array($this->i18n)) {
            $config = array_merge($config, $this->i18n);
        }
        Yii::$app->i18n->translations["{$cat}*"] = $config;
    }

}
