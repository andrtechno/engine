<?php

namespace panix\engine\validators;

use yii\helpers\Html;

class UrlValidator extends \yii\validators\Validator {

    public $attributeName = 'seo_alias';
    public $attributeCompare = 'title';

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute) {
        if (!$model->isNewRecord) {
            $check = $model::find()
                    ->where([$this->attributeName => $model->$attribute])
                    ->andWhere(['!=', 'id', $model->primaryKey])
                    ->one();
        } else {
            $check = $model::find()
                    ->where([$this->attributeName => $model->$attribute])
                    ->one();
        }

        if (isset($check)) {
            $this->addError($model, $attribute, 'URL занят');
        }

        return null;
    }

    public function clientValidateAttribute($model, $attribute, $view) {
        \panix\engine\assets\ValidationAsset::register($view);
        $options = [
            'model' => get_class($model),
            'pk' => $model->primaryKey,
            'usexhr' => true,
            'AttributeSlugId' => Html::getInputId($model, $attribute),
            'attributeCompareId' => Html::getInputId($model, $this->attributeCompare)
        ];
        $view->registerJs("init_translitter(" . json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ");");
        return null;
    }

}
