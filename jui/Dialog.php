<?php

namespace panix\engine\jui;

use yii\jui\Dialog as BaseDialog;

/**
 * @inheritdoc
 */
class Dialog extends BaseDialog
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        //Fix for closing icon (x) not showing up in dialog
        $this->getView()->registerJs("
            if ($.fn.button && $.fn.button.noConflict !== undefined) {
                var bootstrapButton = $.fn.button.noConflict(); 
                $.fn.bootstrapBtn = bootstrapButton;
            }",
            \yii\web\View::POS_READY
        );
    }

}
