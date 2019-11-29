<?php

namespace panix\engine\jui;

use panix\engine\Html;
use yii\jui\Widget;

/**
 * @inheritdoc
 */
class Dialog extends Widget
{
    public function init()
    {
        parent::init();
        echo Html::beginTag('div', $this->options) . "\n";

        //Fix for closing icon (x) not showing up in dialog
        $this->getView()->registerJs("
            if ($.fn.button && $.fn.button.noConflict !== undefined) {
                var bootstrapButton = $.fn.button.noConflict(); 
                $.fn.bootstrapBtn = bootstrapButton;
            }",
            \yii\web\View::POS_READY
        );
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo Html::endTag('div') . "\n";
        $this->registerWidget('dialog');
    }
}
