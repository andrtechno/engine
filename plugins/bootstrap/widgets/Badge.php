<?php

namespace panix\engine\plugins\bootstrap\widgets;

use yii\bootstrap4\Html;

/**
 * Class Badge
 * @package panix\engine\plugins\bootstrap\widgets
 */
class Badge extends BootstrapWidget
{
    /**
     * @var string
     */
    public $type = 'secondary';

    /**
     * @var string
     */
    public $text;

    /**
     * init type
     */
    public function init()
    {
        $this->getCssClass();
        if ($this->text) {
            $this->content = $this->text;
        }
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        return Html::label($this->content, '', $this->options);
    }

    /**
     * css label
     */
    protected function getCssClass()
    {
        $this->type ? Html::addCssClass($this->options, 'badge badge-' . $this->type) : null;
    }
}