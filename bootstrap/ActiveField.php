<?php

namespace panix\engine\bootstrap;

class ActiveField extends \yii\bootstrap4\ActiveField
{

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