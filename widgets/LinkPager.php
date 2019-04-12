<?php

namespace panix\engine\widgets;

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

}