<?php

namespace panix\engine\validators;

use yii\helpers\Html;
use yii\validators\Validator;
use panix\engine\assets\ValidationAsset;

class UrlValidator extends Validator
{

    public $attributeSlug = 'seo_alias';
    public $attributeCompare = 'title';

    public function init()
    {
        if ($this->message == null) {
            $this->message = 'URL занят';
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        /** @var \yii\db\ActiveRecord $model */
        if (!$model->isNewRecord) {
            $check = $model::find()
                ->where([$this->attributeSlug => $model->$attribute])
                ->andWhere(['!=', 'id', $model->primaryKey])
                ->one();
        } else {
            $check = $model::find()
                ->where([$this->attributeSlug => $model->$attribute])
                ->one();
        }

        if (isset($check)) {
            $this->addError($model, $attribute, $this->message);
        }

        return null;
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        ValidationAsset::register($view);
        $options = [
            'model' => get_class($model),
            'pk' => $model->primaryKey,
            'usexhr' => true,
            'successMessage' => $this->message,
            'AttributeSlug' => $attribute,
            'AttributeSlugId' => Html::getInputId($model, $attribute),
            'attributeCompareId' => Html::getInputId($model, $this->attributeCompare),
        ];
        $view->registerJs("init_translitter(" . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ");");
        return null;
    }

}
