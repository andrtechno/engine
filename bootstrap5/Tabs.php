<?php

namespace panix\engine\bootstrap;


use yii\base\InvalidConfigException;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

class Tabs extends \yii\bootstrap5\Tabs
{
    public $dropdownClass = 'yii\bootstrap5\Dropdown';


    protected function prepareItems(&$items, $prefix = '')
    {
        if (!$this->hasActiveTab()) {
            $this->activateFirstVisibleTab();
        }

        foreach ($items as $n => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'itemOptions', []));
            $options['id'] = ArrayHelper::getValue($options, 'id', $this->options['id'] . $prefix . '-tab' . $n);

            if (!ArrayHelper::remove($item, 'visible', true)) {
                continue;
            }
            if (!array_key_exists('label', $item)) {
                throw new InvalidConfigException("The 'label' option is required.");
            }

            $selected = ArrayHelper::getValue($item, 'active', false);
            if (isset($item['items'])) {
                $this->prepareItems($items[$n]['items'], '-dd' . $n);
                continue;
            } else {
                if (!isset($item['url'])) {
                    ArrayHelper::setValue($items[$n], 'url', '#' . $options['id']);
                    ArrayHelper::setValue($items[$n], 'linkOptions.data.toggle', 'tab');
                    ArrayHelper::setValue($items[$n], 'linkOptions.role', 'tab');
                    ArrayHelper::setValue($items[$n], 'linkOptions.aria-controls', $options['id']);
                    ArrayHelper::setValue($items[$n], 'linkOptions.aria-selected', $selected ? 'true' : 'false');
                } else {

                    continue;
                }
            }

            Html::addCssClass($options, ['widget' => 'tab-pane']);
            if ($selected) {
                Html::addCssClass($options, 'active');
            }

            if ($this->renderTabContent) {
                $tag = ArrayHelper::remove($options, 'tag', 'div');
                $this->panes[] = Html::tag($tag, isset($item['content']) ? $item['content'] : '', $options);
            }
        }
    }

}
