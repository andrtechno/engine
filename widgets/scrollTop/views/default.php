<div id="topcontrol">
    <?php if ($this->context->enableTop) { ?>
        <div
        class="backtotop btn btn-secondary" <?php echo(($this->context->opacity > 0 && $this->context->opacity <= 1) ? '' : 'style="display:none;"'); ?>></div>
    <?php } ?>
    <?php if ($this->context->enableBottom) { ?>
        <div
        class="backtobottom" <?php echo(($this->context->opacity > 0 && $this->context->opacity <= 1) ? '' : 'style="display:none;"'); ?>></div>
    <?php } ?>
</div>
<?php
$this->registerJs("

    $(document).ready(function() {
        " . (($this->context->opacity > 0 && $this->context->opacity <= 1) ? "fadeOutTo(); showFadeOnMouseScrollButtons()" : "$(window).scroll(function(){showHideScrollButtons()});") . "
    });
    
    " . (($this->context->opacity > 0 && $this->context->opacity <= 1) ? "" : "showHideScrollButtons();") . "

    " . (($this->context->opacity > 0 && $this->context->opacity <= 1) ? "
        function fadeOutTo(){
            $('.backtotop, .btn').fadeTo(" . $this->context->fadeInTime . ", " . $this->context->opacity . ");
            $('.backtobottom').fadeTo(" . $this->context->fadeInTime . ", " . $this->context->opacity . ");
        }

        function showFadeOnMouseScrollButtons(){
            $('.backtotop').mouseover(function(){
                $('.backtotop').fadeTo(" . $this->context->fadeInTime . ", 1);
            });
            $('.backtotop').mouseout(function(){
                $('.backtotop').fadeTo(" . $this->context->fadeInTime . ", " . $this->context->opacity . ");
            });

            $('.backtobottom').mouseover(function(){
                $('.backtobottom').fadeTo(" . $this->context->fadeInTime . ", 1);
            });
            $('.backtobottom').mouseout(function(){
                $('.backtobottom').fadeTo(" . $this->context->fadeInTime . ", " . $this->context->opacity . ");
            });
        } 
    " : "
        function showHideScrollButtons(){
            if ($(this).scrollTop() > " . $this->context->minHeight . ") {
                $('.backtotop').fadeIn(" . $this->context->fadeInTime . ");
            } else {
                $('.backtotop').fadeOut(" . $this->context->fadeOutTime . ");
            }

            if (($(document).height() - $(this).scrollTop()) > " . $this->context->minDepth . ") {
                $('.backtobottom').fadeIn(" . $this->context->fadeInTime . ");
            } else {
                $('.backtobottom').fadeOut(" . $this->context->fadeOutTime . ");
            }
        }
    ") . "

    
    $('.backtotop').click(function() {
        $(\"html, body\").animate({ scrollTop: 0 }, " . $this->context->scrollTopTime . ");
        return false;
    });

    $('.backtobottom').click(function() {
        $(\"html, body\").animate({ scrollTop: $(document).height() },  " . $this->context->scrollBottomTime . ");
        return false;
    });

", \yii\web\View::POS_READY, 'topBottomScroll-js');

$this->registerCss(' 
    .hide {display:none;}
    
    #topcontrol{
        position: fixed;
        bottom: 50px;
        right: 5px;
        opacity: 1;
        cursor: pointer;
        z-index:1001;
    }

    #topcontrol .btn {

        width: 42px;
        height: 42px;
        position:relative;
    }
    #topcontrol .btn:before{
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

    .backtobottom {
        width: 68px;
        height: 68px;
    }
', [], 'topBottomScroll-css');

