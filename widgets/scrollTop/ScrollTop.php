<?php

namespace panix\engine\widgets\scrollTop;

use panix\engine\data\Widget;

class ScrollTop extends Widget
{

    public $minDepth;
    public $minHeight;
    public $fadeInTime;
    public $fadeOutTime;
    public $opacity;
    public $scrollTopTime;
    public $scrollBottomTime;
    public $enableTop = true;
    public $enableBottom = false;


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

    public function run()
    {

        $this->getView()->registerJs("

    $(document).ready(function() {
        " . (($this->opacity > 0 && $this->opacity <= 1) ? "fadeOutTo(); showFadeOnMouseScrollButtons()" : "$(window).scroll(function(){showHideScrollButtons()});") . "
    });
    
    " . (($this->opacity > 0 && $this->opacity <= 1) ? "" : "showHideScrollButtons();") . "

    " . (($this->opacity > 0 && $this->opacity <= 1) ? "
        function fadeOutTo(){
            $('.scroll-to').fadeTo(" . $this->fadeInTime . ", " . $this->opacity . ");
            //$('.backtobottom').fadeTo(" . $this->fadeInTime . ", " . $this->opacity . ");
        }

        function showFadeOnMouseScrollButtons(){
            $('.scroll-to.top').mouseover(function(){
                $('.scroll-to.top').fadeTo(" . $this->fadeInTime . ", 1);
            });
            $('.scroll-to.top').mouseout(function(){
                $('.scroll-to.top').fadeTo(" . $this->fadeInTime . ", " . $this->opacity . ");
            });

            $('.scroll-to.bottom').mouseover(function(){
                $('.scroll-to.bottom').fadeTo(" . $this->fadeInTime . ", 1);
            });
            $('.scroll-to.bottom').mouseout(function(){
                $('.scroll-to.bottom').fadeTo(" . $this->fadeInTime . ", " . $this->opacity . ");
            });
        } 
    " : "
        function showHideScrollButtons(){
            if ($(this).scrollTop() > " . $this->minHeight . ") {
                $('.scroll-to.top').fadeIn(" . $this->fadeInTime . ");
            } else {
                $('.scroll-to.top').fadeOut(" . $this->fadeOutTime . ");
            }

            if (($(document).height() - $(this).scrollTop()) > " . $this->minDepth . ") {
                $('.scroll-to.bottom').fadeIn(" . $this->fadeInTime . ");
            } else {
                $('.scroll-to.bottom').fadeOut(" . $this->fadeOutTime . ");
            }
        }
    ") . "

    
    $('.scroll-to.top').click(function() {
        $(\"html, body\").animate({ scrollTop: 0 }, " . $this->scrollTopTime . ");
        return false;
    });

    $('.scroll-to.bottom').click(function() {
        $(\"html, body\").animate({ scrollTop: $(document).height() },  " . $this->scrollBottomTime . ");
        return false;
    });

", \yii\web\View::POS_END, 'topBottomScroll-js');

        $this->getView()->registerCss(' 
    #scroll-to{
        position: fixed;
        bottom: 50px;
        right: 5px;
        opacity: 1;
        cursor: pointer;
        z-index:1001;
    }

    #scroll-to .scroll-to {
        width: 44px;
        height: 44px;
        position:relative;
    }
    #scroll-to .scroll-to.top:before{
        position:absolute;
        font-family: "Pixelion";
        content:"\f007";
        width: 100%;
        font-size: 20px;
        left:0;
        top: 6px;
        text-align: center;
        display:block;
    }
', [], 'topBottomScroll-css');
        return $this->render($this->skin);
    }

}
