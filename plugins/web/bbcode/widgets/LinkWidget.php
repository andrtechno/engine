<?php

namespace panix\engine\plugins\web\bbcode\widgets;

use lo\plugins\shortcodes\ShortcodeWidget;
use yii\helpers\Html;

/**
 * Class LinkWidget
 * @package panix\engine\plugins\web\bbcode\widgets
 */
class LinkWidget extends ShortcodeWidget
{

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $target;

    /**
     * @var string
     */
    public $alt;

    /**
     * @var string
     */
    public $title;


    public function run()
    {
        $text = (!empty($this->content)) ? $this->content : $this->url;

        $options = [];
        if ($this->target && in_array($this->target, ['_self', '_blank', '_parent', '_top'])) {
            $options['target'] = $this->target;
        }

        if ($this->alt)
            $options['alt'] = $this->alt;

        if ($this->title)
            $options['title'] = $this->title;

        return Html::a($text, $this->url, $options);
    }


}