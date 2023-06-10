<?php

namespace panix\engine\widgets;

use Yii;
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
    public $firstPageLabel = true;
    public $lastPageLabel = true;
    public $maxMobileButtonCount = 5;
    public $showNext = false;
    public $nextLabel = true;
    public $nextLoadingLabel = true;

    public function init()
    {
        $this->maxButtonCount = CMS::isMobile() ? $this->maxMobileButtonCount : $this->maxButtonCount;
        parent::init();
    }

    public function run()
    {
        if ($this->registerLinkTags) {
            $this->registerLinkTags();
        }
        if ($this->showNext)
            echo $this->renderNextButton();

        echo $this->renderPageButtons();
    }

    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = $this->linkContainerOptions;
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');
        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);
        if (!preg_match('/(next|prev|first|last)/', $class)) {
            $options['id'] = 'page-item-' . ($page + 1);
        }
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
            return Html::tag($linkWrapTag, Html::a('' . $label, $this->pagination->createUrl($page), $linkOptions), $options);
        } else {
            //return Html::tag($linkWrapTag, Html::submitButton($label, ArrayHelper::merge(['value'=>$page+1,'name'=>'page'],$linkOptions)), $options);
            return Html::submitButton($label, ArrayHelper::merge(['value' => $page + 1, 'name' => 'page'], $options));
        }
    }

    protected function renderNextButton()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }
        $currentPage = $this->pagination->getPage();
        if (($page = $currentPage + 1) >= $pageCount - 1) {
            $page = $pageCount - 1;
        }
        if ($currentPage >= $pageCount - 1) {
            return '';
        }
        $this->view->registerJs("
            $(document).on('click', '.ias-trigger',function (event) {
                $(this).addClass('ias-spinner');
                $(this).find('span').html('".Yii::t('default', 'LOADING')."');
            });
        ", $this->view::POS_END);
        return Html::a('<span>'.Yii::t('default', 'LOAD_MORE').'</span>', $this->pagination->createUrl($page), ['class' => 'ias-trigger']);
    }
}
