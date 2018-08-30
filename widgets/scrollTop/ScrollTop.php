<?php
namespace panix\engine\widgets\scrollTop;

use panix\engine\data\Widget;

class ScrollTop extends Widget {

    public $minDepth;
    public $minHeight;
    public $fadeInTime;
    public $fadeOutTime;
    public $opacity;
    public $scrollTopTime;
    public $scrollBottomTime;
    public $enableTop=true;
    public $enableBottom=false;


    public function init()
    {
        if (!isset($this->minDepth))
            $this->minDepth = 800;
        if (!isset($this->minHeight))
            $this->minHeight = 500;
        if (!isset($this->fadeInTime))
            $this->fadeInTime = 700;
        if (!isset($this->fadeOutTime))
            $this->fadeOutTime = 700;
        if (!isset($this->opacity))
            $this->opacity = 0;
        if (!isset($this->scrollTopTime))
            $this->scrollTopTime = 800;
        if (!isset($this->scrollBottomTime))
            $this->scrollBottomTime = 800;
        parent::init();
    }

    public function run() {

        return $this->render($this->skin);
    }

}
