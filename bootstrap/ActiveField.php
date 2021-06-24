<?php

namespace panix\engine\bootstrap;

use panix\engine\traits\ActiveFieldTrait;

class ActiveField extends \yii\bootstrap4\ActiveField
{
    use ActiveFieldTrait;

    /**
     * @inheritdoc
     */
    public function dropdownList($items, $options = [])
    {
        if (!isset($options['class'])) {
            $options['class'] = 'custom-select w-auto';
        }
        return parent::dropdownList($items, $options);
    }

}