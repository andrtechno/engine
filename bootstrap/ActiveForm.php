<?php

namespace panix\engine\bootstrap;

use yii\helpers\ArrayHelper;

class ActiveForm extends \yii\bootstrap4\ActiveForm {

    public $layout = 'horizontal';

    public $checkTemplate = "<div class=\"ds\"></div><div class=\"form-check\">\n{input}\n{label}\n{error}\n{hint}\n</div>";

    public $checkEnclosedTemplate = "<div class=\"ds\"></div><div class=\"form-check\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";

    public function init() {
        $this->fieldConfig = ArrayHelper::merge([
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-4 col-form-label',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-8',
                        'error' => '',
                        'hint' => '',
                    ],
        ],$this->fieldConfig);
        parent::init();
    }
}
