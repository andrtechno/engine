<?php
namespace panix\engine\plugins\bootstrap\widgets;

use panix\mod\plugins\helpers\ShortcodesHelper;
use yii\bootstrap4\Tabs as BootstrapTabs;
use yii\helpers\ArrayHelper;

/**
 * Class Tabs
 * @package panix\engine\plugins\bootstrap\widgets
 */
class Tabs extends BootstrapWidget
{
    /**
     * specifies the Bootstrap tab styling.
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @return string
     */
    public function run()
    {
        $this->getItemsFromContent();
        return BootstrapTabs::widget([
            'navType' => 'nav-'.$this->type,
            'items' => $this->items
        ]);
    }

    /**
     * populate $items
     */
    protected function getItemsFromContent()
    {
        $pattern = ShortcodesHelper::shortcodeRegex('tab');
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
            'label' => ArrayHelper::getValue($attr, 'title', $tag.'-'.$index),
            'active' => ArrayHelper::getValue($attr, 'active') ? true : false
        ];
    }
}