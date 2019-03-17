<?php

namespace panix\engine\plugins\bootstrap\widgets;

use lo\plugins\helpers\ShortcodesHelper;
use yii\helpers\ArrayHelper;

/**
 * Class Accordion
 * @package panix\engine\plugins\bootstrap\widgets
 */
class Accordion extends BootstrapWidget
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @return string
     */
    public function run()
    {
        AccordionAsset::register($this->getView());
        $this->getItemsFromContent();
        return BootstrapAccordion::widget([
            'items' => $this->items,
            'options' => $this->options,
        ]);
    }

    /**
     * populate $items
     */
    protected function getItemsFromContent()
    {
        $pattern = ShortcodesHelper::shortcodeRegex('panel');
        preg_replace_callback($pattern, [$this, 'doShortcodeTag'], $this->content);
    }

    /**
     * @param $m
     */
    protected function doShortcodeTag($m)
    {
        $tag = $m[2];
        $attr = ShortcodesHelper::parseAttributes($m[3]);
        $content = isset($m[5]) ? $m[5] : null;

        $index = count($this->items);

        $this->items[$index] = [
            'content' => $content,
            'label' => ArrayHelper::getValue($attr, 'title', $tag . '-' . $index),
            'headerOptions' => [
                'class' => 'collapsed'
            ],
            'contentOptions' => [
                'class' => ArrayHelper::getValue($attr, 'active') ? 'in' : ''
            ]
        ];
    }
}