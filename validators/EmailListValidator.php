<?php

namespace panix\engine\validators;

use panix\engine\CMS;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\validators\EmailValidator;
use yii\validators\PunycodeAsset;
use yii\validators\Validator;
use panix\engine\assets\ValidationAsset;
use yii\web\JsExpression;

class EmailListValidator extends EmailValidator
{


    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $emails = explode(',', $model->$attribute);
        foreach ($emails as $email) {
            if (!$this->validate($email))
                $this->addError($model, $attribute, $this->formatMessage($this->message, [
                    'attribute' => $email,
                ]));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        ValidationAsset::register($view);
        if ($this->enableIDN) {
            PunycodeAsset::register($view);
        }
        $emails = explode(',', $model->$attribute);
        foreach ($emails as $email) {
            if (!$this->validate($email)) {
                $options = $this->getClientOptions($model, $email);
                return 'yii.validation.email(value, messages, ' . Json::htmlEncode($options) . ');';
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getClientOptions($model, $attribute)
    {
        $options = [
            'pattern' => new JsExpression($this->pattern),
            'fullPattern' => new JsExpression($this->fullPattern),
            'allowName' => $this->allowName,
            'message' => $this->formatMessage($this->message . 'zz', [
                'attribute' => $attribute,
            ]),
            'enableIDN' => (bool)$this->enableIDN,
        ];
        if ($this->skipOnEmpty) {
            $options['skipOnEmpty'] = 1;
        }

        return $options;
    }
}
