<?php

namespace panix\engine\bootstrap;

use yii\helpers\ArrayHelper;

class ActiveForm extends \yii\bootstrap\ActiveForm {

    public $layout = 'horizontal';

    public function init() {
        $this->fieldConfig = ArrayHelper::merge([
                    'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-4',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-8',
                        'error' => '',
                        'hint' => ''
                    ],
        ],$this->fieldConfig);
        parent::init();
    }

}
