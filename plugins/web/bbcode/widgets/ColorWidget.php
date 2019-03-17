<?php

namespace panix\engine\plugins\web\bbcode\widgets;

use lo\plugins\shortcodes\ShortcodeWidget;
use yii\helpers\Html;

/**
 * Class LinkWidget
 * @package panix\engine\plugins\web\bbcode\widgets
 */
class ColorWidget extends ShortcodeWidget
{

    /**
     * @var string
     */
    public $color;


    public function run()
    {
       // $test = func_get_args();
       // print_r($test);die;
        $options = [];
        if ($this->color)
            $options['style'] = "color:{$this->color}";
        return Html::tag('span', $this->content, $options);
    }


}