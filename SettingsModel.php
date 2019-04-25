<?php

namespace panix\engine;

use Yii;
use panix\engine\base\Model;
use yii\base\InvalidConfigException;

class SettingsModel extends Model
{
    protected $category;
    const NAME = null;

    public function init()
    {
        if (!isset($this->module)) {
            throw new InvalidConfigException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => 'module'
            ]));
        }
        if (!isset($this->category)) {
            $this->category = $this->module;
        }
        $this->setAttributes((array)Yii::$app->settings->get($this->category));
    }

    public function submitButton()
    {
        return Html::submitButton(Yii::t('app', 'SAVE'), ['class' => 'btn btn-success']);
    }

    public function save()
    {
        $shortName = (new \ReflectionClass(get_called_class()))->getShortName();
        if ($this->validate()) {
            Yii::$app->settings->set($this->category, Yii::$app->request->post($shortName));
            Yii::$app->session->setFlash("success", Yii::t('app', 'SUCCESS_UPDATE'));
            return true;
        } else {
            Yii::$app->session->setFlash("error", Yii::t('app', 'ERROR_UPDATE'));
            return false;
        }
    }

}

?>
