<?php

namespace panix\engine\validators;

use panix\engine\CMS;
use yii\helpers\Json;
use yii\validators\EmailValidator;
use yii\validators\PunycodeAsset;
use panix\engine\assets\ValidationAsset;
use yii\web\JsExpression;

class EmailListValidator extends EmailValidator
{

    /**
     * @var string
     */
    public $separator = ',';
    private $errors = [];

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $emails = explode($this->separator, $model->$attribute);
        foreach ($emails as $email) {
            $result = $this->validateValue($email);
            if (!empty($result)) {
                $this->addError($model, $attribute, $this->formatMessage($this->message, [
                    'attribute' => $email,
                ]));
            }
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
        //$options = [];
        $js = '';
        $emails = explode($this->separator, $model->$attribute);
        foreach ($emails as $email) {
            if ($this->validateValue($email)) {
                $options = $this->getClientOptions($model, $email);
                $js .= 'yii.validation.email(value, messages, ' . Json::htmlEncode($options) . ');' . PHP_EOL;
            }
        }

        // return $js;
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientOptions($model, $attribute)
    {
        $options = [
            'message' => $this->formatMessage($this->message, [
                'attribute' => $attribute,
            ]),
        ];
        return array_merge(parent::getClientOptions($model, $attribute),$options);
    }
}
