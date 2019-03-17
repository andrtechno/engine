<?php

namespace panix\engine\plugins\bootstrap\widgets;

/**
 * Class Row
 * @package lo\shortcodes\bootstrap\widgets
 */
class Row extends BootstrapWidget
{
    /**
     * init widget
     */
    public function init()
    {
        parent::init();

        $this->options = [
            'class' => 'row'
        ];
    }
}