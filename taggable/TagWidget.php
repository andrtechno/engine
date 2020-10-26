<?php

namespace panix\engine\taggable;

use yii\helpers\Html;
use yii\base\Widget;

/**
 * TagWidget renders an HTML tag cloud or as an ordered/unordered list
 *
 * For example:
 *
 * ```php
 * echo TagWidget::widget([
 *     'items' => $tags,
 *     'url' => ['post/index'],
 *     'urlParam' => 'tag',
 * ]);
 * ```
 */
class TagWidget extends Widget
{
    /**
     * @var array key=>value pairs of tags=>frequency. If multiple
     * TagWidgets will use the same data on the same page, TaggingQuery can
     * be used to first generate the item list which can then be passed to
     * TagWidget for display.
     */
    public $items;
    /**
     * @var integer smallest size to be assigned to a tag in the tag cloud
     */
    public $smallest = 14;
    /**
     * @var integer largest size to be assigned to a tag in the tag cloud
     */
    public $largest = 22;
    /**
     * @var string unit of measure for assigning the smallest and largest font sizes
     */
    public $unit = 'px';
    /**
     * @var string format of the returned tags. Options are 'cloud', 'inline', 'ul', or
     * 'ol'. Cloud will return a tag cloud with font-sizes adjusted according
     * to their count (frequency). 'ul' and 'ol' will return the appropriate
     * list that can be formatted as desired. If 'ol' is specified, a 'type'
     * will likely be desired as a 'ulOptions' value.
     */
    public $format = 'cloud';
    /**
     * @var array the route that will be used as a base onto which 'urlParam'
     * and the tag name will be appended.
     */
    public $url;
    /**
     * @var string the URL parameter that will be used with the tag and
     * appended to URL.
     */
    public $urlParam;
    /**
     * @var array options that are to be assigned to the ul or ol in the tag
     * cloud or list.
     */
    public $listOptions = [];
    /**
     * @var array options that are to be assigned to each list item (li) in
     * the tag cloud or list.
     */
    public $itemOptions = [];
    /**
     * @var array options that are to be assigned to each link (a) in the tag
     * cloud or list.
     */
    public $linkOptions = [];
    /**
     * @var integer the minimum frequency found for all tags which is used as the
     * basis for increasing the font-size of more frequent tags.
     */
    private $_minCount;
    /**
     * @var integer the calculated difference in font when indicating tag frequency.
     */
    private $_fontStep = 1;

    /**
     * Renders the widget
     * @return string the rendering result of the widget.
     */
    public function run()
    {
        return $this->renderItems();
    }

    /**
     * Renders widget items
     * @return string complete list of rendered items
     */
    public function renderItems()
    {
        if (!empty($this->items)) {
            $this->_fontStep = $this->getFontStep();
        }
        $items = [];
        foreach ($this->items as $name => $count) {
            $items[] = $this->renderItem(Html::encode($name), $count);
        }

        if ($this->format == 'inline') {
            return implode(", ", $items);
        } else {
            Html::addCssClass($this->listOptions, ($this->format == 'cloud') ? 'tags_cloud' : 'tags_list');
            $listType = ($this->format == 'ol') ? 'ol' : 'ul';
            return Html::tag($listType, implode("\n", $items), $this->listOptions);
        }
    }

    /**
     * Renders a widget's item
     * @param string $name the item name to be displayed
     * @param integer $count the count or frequency of the item to be displayed
     * @return string a single item within the complete list
     */
    public function renderItem($name, $count)
    {
        $fontSize = ($this->smallest + (($count - $this->_minCount) * $this->_fontStep));

        if ($this->format == 'cloud') {
            Html::addCssStyle($this->itemOptions, 'font-size:' . $fontSize . $this->unit);
        }
        if ($this->format == 'cloud') {
            Html::addCssStyle($this->linkOptions, 'font-size:' . $fontSize . $this->unit);
        }

        $itemTag = ($this->format == 'inline') ? 'span' : 'li';
        if (!empty($this->url)) {
            $url = array_merge($this->url, [$this->urlParam => $name]);
            return Html::tag($itemTag, Html::a($name, $url, $this->linkOptions), $this->itemOptions);
        } else {
            return Html::tag($itemTag, $name, $this->itemOptions);
        }


    }

    /**
     * Determine the step size for font increases
     * @return float|int size of individual font step for increasingly frequent items
     */

    public function getFontStep()
    {

        $this->_minCount = min($this->items);
        $spread = max($this->items) - $this->_minCount;
        if ($spread <= 0) {
            $spread = 1;
        }
        $font_spread = $this->largest - $this->smallest;
        if ($font_spread < 0) {
            $font_spread = 1;
        }
        return $font_spread / $spread;
    }
}