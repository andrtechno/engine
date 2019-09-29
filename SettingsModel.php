<?php

namespace panix\engine;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use panix\engine\base\Model;
use panix\engine\components\Settings;
use yii\helpers\VarDumper;

/**
 * Class SettingsModel
 * @package panix\engine
 */
class SettingsModel extends Model
{
    public static $category = null;

    public static function tableName()
    {
        return Settings::tableName();
    }

    public static function defaultSettings()
    {
        return [];
    }

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

        $this->setAttributes((array)Yii::$app->settings->get(static::$category));
    }


    public function submitButton()
    {
        return Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-success']);
    }

    public function save()
    {
        Yii::$app->settings->set(static::$category, $this->attributes);
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        if (parent::validate($attributeNames, $clearErrors)) {
            Yii::$app->session->addFlash("success", Yii::t('app', 'SUCCESS_UPDATE'));
            return true;
        } else {
            //print_r($this->getErrors());die;
            Yii::$app->session->addFlash("error", Yii::t('app', 'ERROR_UPDATE'));
            return false;
        }
    }

    public function saveOLD()
    {
        //  $shortName = (new \ReflectionClass(get_called_class()))->getShortName();
        //   $this->attributes = ArrayHelper::merge(Yii::$app->request->post($shortName), $this->attributes);
        if ($this->validate()) {
            Yii::$app->settings->set(static::$category, $this->attributes);
            Yii::$app->session->setFlash("success", Yii::t('app', 'SUCCESS_UPDATE'));
            return true;
        } else {
            //print_r($this->getErrors());die;
            Yii::$app->session->setFlash("error", Yii::t('app', 'ERROR_UPDATE'));
            return false;
        }
    }

}
