<?php

namespace panix\engine\widgets;

use panix\engine\CMS;
use panix\engine\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager as BasePager;

/**
 * Class LinkPager
 * @package panix\engine\widgets
 */
class LinkPager extends BasePager
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
    public $mata = true;

    public function init()
    {
        $this->maxButtonCount = CMS::isMobile() ? 5 : 10;
        parent::init();

    }

    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = $this->linkContainerOptions;
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');
        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);
        $options['id']='page-item-'.($page+1);
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
        $linkOptions['data-page'] = $page + 1;

        if ($this->pageType == 'link') {
            return Html::tag($linkWrapTag, Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);
        } else {
            //return Html::tag($linkWrapTag, Html::submitButton($label, ArrayHelper::merge(['value'=>$page+1,'name'=>'page'],$linkOptions)), $options);
            return Html::submitButton($label, ArrayHelper::merge(['value' => $page + 1, 'name' => 'page'], $options));
        }
    }

    protected function renderPageButtons()
    {
        if ($this->mata) {
            $pageCount = $this->pagination->getPageCount();
            $currentPage = $this->pagination->getPage();

            // prev meta
            if ($this->prevPageLabel !== false) {
                if (($page = $currentPage - 1) < 0) {
                    $page = 0;
                }
                if ($currentPage > 0) {
                    $this->getView()->registerLinkTag([
                        'rel' => 'prev',
                        'type' => 'page',
                        'href' => $this->pagination->createUrl($page)
                    ]);
                }
            }

            // next meta
            if ($this->nextPageLabel !== false) {
                if (($page = $currentPage + 1) >= $pageCount - 1) {
                    $page = $pageCount - 1;
                }
                if ($currentPage < $pageCount - 1) {
                    $this->getView()->registerLinkTag([
                        'rel' => 'next',
                        'type' => 'page',
                        'href' => $this->pagination->createUrl($page)
                    ]);
                }
            }
        }
        return parent::renderPageButtons();
    }

}