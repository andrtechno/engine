<?php

namespace panix\engine\plugins\bootstrap\widgets;

use yii\bootstrap4\Html;

/**
 * Class Col
 * @package panix\engine\plugins\bootstrap\widgets
 */
class Col extends BootstrapWidget
{
    /** @var string column width */
    public $xl = false;
    public $lg = false;
    public $md = false;
    public $sm = false;


    /** @var string column offset */
    public $offset = false;
    public $offset_sm = false;
    public $offset_md = false;
    public $offset_lg = false;
    public $offset_xl = false;

    /** @var string column order */
    public $order = false;
    public $order_sm = false;
    public $order_md = false;
    public $order_lg = false;
    public $order_xl = false;

    /**
     * init widget
     */
    public function init()
    {
        $this->getCssClass();
        parent::init();
    }

    /**
     * Genereate css class
     */
    protected function getCssClass()
    {
        $this->sm ? Html::addCssClass($this->options, 'col-sm-' . $this->sm) : null;
        $this->md ? Html::addCssClass($this->options, 'col-md-' . $this->md) : null;
        $this->lg ? Html::addCssClass($this->options, 'col-lg-' . $this->lg) : null;
        $this->xl ? Html::addCssClass($this->options, 'col-xl-' . $this->xl) : null;

        if (!$this->lg && !$this->md && !$this->sm && !$this->xl) {
            Html::addCssClass($this->options, 'col');
        }

        $this->offset ? Html::addCssClass($this->options, 'offset-' . $this->offset) : null;
        $this->offset_sm ? Html::addCssClass($this->options, 'offset-sm-' . $this->offset_sm) : null;
        $this->offset_md ? Html::addCssClass($this->options, 'offset-md-' . $this->offset_md) : null;
        $this->offset_lg ? Html::addCssClass($this->options, 'offset-lg-' . $this->offset_lg) : null;
        $this->offset_xl ? Html::addCssClass($this->options, 'offset-xl-' . $this->offset_xl) : null;


        $this->order ? Html::addCssClass($this->options, 'order-' . $this->order) : null;
        $this->order_sm ? Html::addCssClass($this->options, 'order-sm-' . $this->order_sm) : null;
        $this->order_md ? Html::addCssClass($this->options, 'order-md-' . $this->order_md) : null;
        $this->order_lg ? Html::addCssClass($this->options, 'order-lg-' . $this->order_lg) : null;
        $this->order_xl ? Html::addCssClass($this->options, 'order-xl-' . $this->order_xl) : null;


        $this->xclass ? Html::addCssClass($this->options, $this->xclass) : null;
    }
}