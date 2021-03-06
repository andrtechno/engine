<?php

namespace panix\engine\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use panix\engine\Html;

class Breadcrumbs extends \yii\widgets\Breadcrumbs {

    public $scheme = true;
    public $lastscheme = true;

    public function run() {
        if ($this->scheme) {
            $this->itemTemplate = '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class="breadcrumb-item">{link}</li>';
            $this->activeItemTemplate = '<li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem" class="breadcrumb-item active">{link}</li>';
        }
        if (empty($this->links)) {
            return;
        }
        $links = [];
        if ($this->homeLink === null) {
            $links[] = $this->renderItems([
                'label' => Yii::t('yii', 'Home'),
                'url' => Yii::$app->homeUrl,
                    ], $this->itemTemplate, 1);
        } elseif ($this->homeLink !== false) {
            $links[] = $this->renderItems($this->homeLink, $this->itemTemplate, 2);
        }

        $count = 2;
        foreach ($this->links as $link) {
            if (!is_array($link)) {
                $link = ['label' => $link];
            }
            $links[] = $this->renderItems($link, isset($link['url']) ? $this->itemTemplate : $this->activeItemTemplate, $count);
            $count++;
        }
        if ($this->scheme) {
            $this->options['itemtype'] = 'http://schema.org/BreadcrumbList';
            $this->options['itemscope'] = '';
        }
        echo Html::tag($this->tag, implode('', $links), $this->options);
    }

    /**
     * Renders a single breadcrumb item.
     * @param array $link the link to be rendered. It must contain the "label" element. The "url" element is optional.
     * @param string $template the template to be used to rendered the link. The token "{link}" will be replaced by the link.
     * @return string the rendering result
     * @throws InvalidConfigException if `$link` does not have "label" element.
     */
    protected function renderItems($link, $template, $count) {
        $encodeLabel = ArrayHelper::remove($link, 'encode', $this->encodeLabels);
        if (array_key_exists('label', $link)) {
            $label = $encodeLabel ? Html::encode($link['label']) : $link['label'];
        } else {
            throw new InvalidConfigException('The "label" element is required for each link.');
        }
        if (isset($link['template'])) {
            $template = $link['template'];
        }
        if (isset($link['url'])) {
            $options = $link;
            unset($options['template'], $options['label'], $options['url']);

            if ($this->scheme) {
                $label = Html::tag('span', $label, ['itemprop' => 'name']);
                $options['itemprop'] = 'item';
            }

            $link = Html::a($label, $link['url'], $options);
        } else {

            /**
             * https://pixelplus.ru/samostoyatelno/stati/vnutrennie-faktory/hlebnye-kroshki.html
             * Включение текущий страницы с отсутствием ссылки на неё в конце дублирующей навигации — является хорошим тоном.
             */
            if ($this->scheme && $this->lastscheme) {
                $options['itemprop'] = 'item';
                $label = Html::a(Html::tag('span', $label, ['itemprop' => 'name']), [Yii::$app->request->url, '#' => 'header'], $options);
            }
            $link = $label;
        }
        if ($this->scheme) {
            $link .= "<meta itemprop=\"position\" content=\"{$count}\">";
        }
        return strtr($template, ['{link}' => $link]);
    }

}
