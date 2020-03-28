<?php

namespace panix\engine;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use panix\engine\base\Model;
use panix\engine\components\Settings;
use yii\helpers\VarDumper;

/**
 * Class ThemeSettingsModel
 * @package panix\engine
 */
class ThemeSettingsModel extends SettingsModel
{

    public function init()
    {
        if (!isset($this->module)) {
            throw new InvalidConfigException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => 'module'
            ]));
        }
        if (static::$category == null) {
            static::$category = $this->module;
        }

        $this->setAttributes((array)Yii::$app->view->theme->get());
    }

    public function attributeLabels()
    {
        return [];
    }

    public function save()
    {
        Yii::$app->view->theme->set(Yii::$app->settings->get('app', 'theme'), $this->attributes);
    }


}
