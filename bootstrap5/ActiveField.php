<?php

namespace panix\engine\bootstrap;

use panix\engine\traits\ActiveFieldTrait;

class ActiveField extends \yii\bootstrap5\ActiveField
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