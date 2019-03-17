<?php

namespace panix\engine\plugins\bootstrap\widgets;

/**
 * Class Container
 * @package panix\engine\plugins\bootstrap\widgets
 */
class Container extends BootstrapWidget
{
    /** @var bool */
    public $fluid;

    /**
     * init widget
     */
    public function init()
    {
        $this->options = [
            'class' => $this->fluid ? 'container-fluid' : 'container'
        ];
        parent::init();
    }
}