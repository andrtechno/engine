<?php
namespace lo\shortcodes\bootstrap\widgets;

use yii\bootstrap4\Html;

/**
 * Class Col
 * @package lo\shortcodes\bootstrap\widgets
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class Col extends BootstrapWidget
{
    /** @var string column width */
    public $xl = false;
    public $lg = false;
    public $md = false;
    public $sm = false;


    /** @var string column offset */
    public $xl_offset = false;
    public $lg_offset = false;
    public $md_offset = false;
    public $sm_offset = false;

    /** @var string column pull */
    public $xl_pull = false;
    public $lg_pull = false;
    public $md_pull = 12;
    public $sm_pull = false;

    /** @var string column push */
    public $xl_push = false;
    public $lg_push = false;
    public $md_push = 12;
    public $sm_push = false;

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
        $this->lg ? Html::addCssClass($this->options, 'col-lg-' . $this->lg) : null;
        $this->md ? Html::addCssClass($this->options, 'col-md-' . $this->md) : null;
        $this->sm ? Html::addCssClass($this->options, 'col-sm-' . $this->sm) : null;
        $this->xl ? Html::addCssClass($this->options, 'col-xl-' . $this->xl) : null;

        if(!$this->lg && !$this->md && !$this->sm && !$this->xl){
            Html::addCssClass($this->options, 'col');
        }

        $this->lg_offset ? Html::addCssClass($this->options, 'col-lg-offset-' . $this->lg_offset) : null;
        $this->md_offset ? Html::addCssClass($this->options, 'col-md-offset-' . $this->md_offset) : null;
        $this->sm_offset ? Html::addCssClass($this->options, 'col-sm-offset-' . $this->sm_offset) : null;
        $this->xl_offset ? Html::addCssClass($this->options, 'col-xl-offset-' . $this->xl_offset) : null;

        $this->lg_pull ? Html::addCssClass($this->options, 'col-lg-pull-' . $this->lg_pull) : null;
        $this->md_pull ? Html::addCssClass($this->options, 'col-md-pull-' . $this->md_pull) : null;
        $this->sm_pull ? Html::addCssClass($this->options, 'col-sm-pull-' . $this->sm_pull) : null;
        $this->xl_pull ? Html::addCssClass($this->options, 'col-xl-pull-' . $this->xl_pull) : null;

        $this->lg_push ? Html::addCssClass($this->options, 'col-lg-push-' . $this->lg_push) : null;
        $this->md_push ? Html::addCssClass($this->options, 'col-md-push-' . $this->md_push) : null;
        $this->sm_push ? Html::addCssClass($this->options, 'col-sm-push-' . $this->sm_push) : null;
        $this->xl_push ? Html::addCssClass($this->options, 'col-xl-push-' . $this->xl_push) : null;



        $this->xclass ? Html::addCssClass($this->options, $this->xclass) : null;
    }
}