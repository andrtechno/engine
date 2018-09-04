<?php

namespace panix\engine\bootstrap;

use yii\base\InvalidConfigException;
use panix\engine\Html;
use yii\helpers\ArrayHelper;

class Dropdown extends \yii\bootstrap4\Dropdown {

    public $subMenuOptions = [];

    /**
     * Initializes the widget
     */
    public function init() {
        parent::init();
        DropdownAsset::register($this->view);
    }

    /**
     * @inherit doc
     */
    protected function renderItems($items, $options = []) {
        $lines = [];
        foreach ($items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                unset($items[$i]);
                continue;
            }
            if (is_string($item)) {
                $lines[] = $item;
                continue;
            }
            if (!isset($item['label'])) {
                throw new InvalidConfigException("The 'label' option is required.");
            }
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $icon = isset($item['icon']) ? Html::icon($item['icon']) . ' ' : '';
            $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $itemOptions = ArrayHelper::getValue($item, 'options', ['class'=>'nav-item']);

            $linkOptions = ArrayHelper::getValue($item, 'linkOptions', ['class'=>'nav-link']);
            $linkOptions['tabindex'] = '-1';
            $url = array_key_exists('url', $item) ? $item['url'] : null;

            if (empty($item['items'])) {
                if ($url === null) {
                    $content = $label;
                    Html::addCssClass($itemOptions, 'dropdown-header');
                } else {
                    $content = Html::a($icon . $label, $url, $linkOptions);
                }
            } else {
                Html::addCssClass($linkOptions, 'nav-link dropdown-toggle');
                $linkOptions['data-toggle'] = 'dropdown';
                $submenuOptions = $options;
                unset($submenuOptions['id']);
                $content = Html::a($icon . $label, $url === null ? '#' : $url, $linkOptions)
                        . $this->renderItems($item['items'], $submenuOptions);
                Html::addCssClass($itemOptions, 'nav-item dropdown dropdown-submenu');
            }

            $lines[] = Html::tag('li', $content, $itemOptions);
        }

        return Html::tag('ul', implode("\n", $lines), $options);
    }

}
