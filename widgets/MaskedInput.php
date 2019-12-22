<?php

namespace panix\engine\widgets;

/**
 * Class MaskedInput
 * @package panix\engine\widgets
 * @see \yii\widgets\MaskedInput
 */
class MaskedInput extends \yii\widgets\MaskedInput
{

    public function init()
    {
        if (!$this->mask) {
            $this->mask = '+38 (999) 999-99-99';
        }
        parent::init();
    }


}