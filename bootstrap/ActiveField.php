<?php

namespace panix\engine\bootstrap;

use panix\engine\Html;

class ActiveField extends \yii\bootstrap4\ActiveField
{
    public $checkTemplate = "<div class=\"form-check\">\n{input}\n{label}\n{error}\n{hint}\n</div>";

    public $checkHorizontalTemplate = "{label}\n{beginWrapper}\n<div class=\"form-check\">\n{input}\n{error}\n{hint}\n</div>\n{endWrapper}";

    public $checkEnclosedTemplate = "<div class=\"form-check\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";


    /**
     * @inheritdoc
     */
    public function dropdownList($items, $options = [])
    {
        if (!isset($options['class'])) {
            $options['class'] = 'form-control w-auto';
        }
        return parent::dropdownList($items, $options);
    }
}