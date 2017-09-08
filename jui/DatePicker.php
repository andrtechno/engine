<?php

namespace panix\engine\jui;

class DatePicker extends \yii\jui\DatePicker {

    public function init() {
        if ($this->view->context instanceof \panix\engine\controllers\AdminController) {
            if (!isset($this->options['class']))
                $this->options['class'] = 'form-control';
            
            if (!isset($this->options['style']))
                $this->options['style'] = 'width:auto;';
        }
        parent::init();
    }

}
