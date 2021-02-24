<?php

namespace panix\engine\bootstrap;

use yii\base\InvalidConfigException;
use panix\engine\Html;
use yii\helpers\ArrayHelper;

class Dropdown extends \yii\bootstrap4\Dropdown
{

    /**
     * Initializes the widget
     */
    public function init()
    {
        parent::init();
        DropdownAsset::register($this->view);
    }

    /**
     * @inheritdoc
     */
    protected function renderItems($items, $options = [])
    {
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


            $badgeOptions = ['class' => 'badge badge-success'];
            if (isset($item['badgeOptions'])) {
                $badgeOptions = array_merge($badgeOptions, $item['badgeOptions']);
            }

            $badge = '';
            if (isset($item['badge'])) {
                $badge = Html::tag('span', ArrayHelper::getValue($item, 'badge'), $badgeOptions);
            }


            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $icon = isset($item['icon']) ? Html::icon($item['icon']) . ' ' : '';
            $label = $encodeLabel ? Html::encode($item['label']).$badge : $item['label'].$badge;
            $itemOptions = ArrayHelper::getValue($item, 'options', ['class' => 'nav-item']);

            $linkOptions = ArrayHelper::getValue($item, 'linkOptions', ['class' => 'nav-link']);
            $linkOptions['tabindex'] = '-1';
            $url = array_key_exists('url', $item) ? $item['url'] : null;

            if (empty($item['items'])) {
                if ($url) {
                    $content = Html::a($icon . $label, $url, $linkOptions);
                    $lines[] = Html::tag('li', $content, $itemOptions);
                }
            } else {
                Html::addCssClass($linkOptions, 'dropdown-toggle');
                $linkOptions['data-toggle'] = 'dropdown';
                $submenuOptions = $options;

                //unset($submenuOptions['id']);
                $content = Html::a($icon . $label, $url === null ? '#' : $url, $linkOptions)
                    . $this->renderItems($item['items'], $submenuOptions);
                Html::addCssClass($itemOptions, 'dropdown dropdown-submenu');
                $lines[] = Html::tag('li', $content, $itemOptions);
            }
        }

        return Html::tag('ul', implode("\n", $lines), $options);
    }



    protected function renderItems2($items, $options = [])
    {
        $lines = [];
        foreach ($items as $item) {
            if (is_string($item)) {
                $lines[] = ($item === '-')
                    ? Html::tag('div', '', ['class' => 'dropdown-divider'])
                    : $item;
                continue;
            }
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            if (!array_key_exists('label', $item)) {
                throw new InvalidConfigException("The 'label' option is required.");
            }
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $itemOptions = ArrayHelper::getValue($item, 'options', []);
            $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);
            $active = ArrayHelper::getValue($item, 'active', false);
            $disabled = ArrayHelper::getValue($item, 'disabled', false);

            Html::addCssClass($linkOptions, 'dropdown-item');
            if ($disabled) {
                ArrayHelper::setValue($linkOptions, 'tabindex', '-1');
                ArrayHelper::setValue($linkOptions, 'aria-disabled', 'true');
                Html::addCssClass($linkOptions, 'disabled');
            } elseif ($active) {
                Html::addCssClass($linkOptions, 'active');
            }

            $url = array_key_exists('url', $item) ? $item['url'] : null;
            if (empty($item['items'])) {
                if ($url === null) {
                    $content = Html::tag('h6', $label, ['class' => 'dropdown-header']);
                } else {
                    $content = Html::a($label, $url, $linkOptions);
                }
                $lines[] = $content;
            } else {
                $submenuOptions = $this->submenuOptions;
                if (isset($item['submenuOptions'])) {
                    $submenuOptions = array_merge($submenuOptions, $item['submenuOptions']);
                }
                Html::addCssClass($submenuOptions, ['dropdown-submenu']);
                Html::addCssClass($linkOptions, ['dropdown-toggle']);

                $lines[] = Html::beginTag('div', array_merge_recursive(['class' => ['dropdown'], 'aria-expanded' => 'false'], $itemOptions));
                $lines[] = Html::a($label, $url, array_merge([
                    'data-toggle' => 'dropdown',
                    'aria-haspopup' => 'true',
                    'aria-expanded' => 'false',
                    'role' => 'button'
                ], $linkOptions));
                $lines[] = static::widget([
                    'items' => $item['items'],
                    'options' => $submenuOptions,
                    'submenuOptions' => $submenuOptions,
                    'encodeLabels' => $this->encodeLabels
                ]);
                $lines[] = Html::endTag('div');
            }
        }

        return Html::tag('div', implode("\n", $lines), $options);
    }

}
