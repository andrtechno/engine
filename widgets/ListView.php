<?php

namespace panix\engine\widgets;

use Yii;
use yii\helpers\ArrayHelper;

class ListView extends \yii\widgets\ListView
{
    public $emptyTextOptions = ['class' => 'alert alert-info'];

    public function init()
    {
        parent::init();
        if (!$this->emptyText)
            $this->emptyText = Yii::t('app', 'NO_INFO');

        $pager = [];
        if (!isset($this->pager['class'])) {
            $pager['class'] = LinkPager::class;
        }
        $this->pager = ArrayHelper::merge($this->pager, $pager);

    }
}