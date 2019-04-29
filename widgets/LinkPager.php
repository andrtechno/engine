<?php

namespace panix\engine\widgets;

use panix\engine\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager as Pager;

/**
 * Class LinkPager
 * @package panix\engine\widgets
 */
class LinkPager extends Pager
{

    public $options = ['class' => 'pagination'];

    public $pageCssClass = 'page-item';

    public $firstPageCssClass = 'page-item first';

    public $lastPageCssClass = 'page-item last';

    public $prevPageCssClass = 'page-item prev';

    public $nextPageCssClass = 'page-item next';

    public $activePageCssClass = 'active';

    public $disabledPageCssClass = 'disabled';

    public $disabledListItemSubTagOptions = ['tag' => 'span', 'class' => 'page-link', 'aria-hidden' => "true"];

    public $linkOptions = ['class' => 'page-link', 'tabindex' => "-1"];

    public $pageType = 'link';

    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = $this->linkContainerOptions;
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');
        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);

        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            $disabledItemOptions = $this->disabledListItemSubTagOptions;
            $tag = ArrayHelper::remove($disabledItemOptions, 'tag', 'span');

            return Html::tag($linkWrapTag, Html::tag($tag, $label, $disabledItemOptions), $options);
        }
        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        if($this->pageType == 'link'){
            return Html::tag($linkWrapTag, Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);
        }else{
            //return Html::tag($linkWrapTag, Html::submitButton($label, ArrayHelper::merge(['value'=>$page+1,'name'=>'page'],$linkOptions)), $options);
            return Html::submitButton($label, ArrayHelper::merge(['value'=>$page+1,'name'=>'page'],$options));
        }


    }

}