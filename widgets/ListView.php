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
            $this->emptyText = Yii::t('app/default', 'NO_INFO');

        $this->pager = ArrayHelper::merge(['class' => '\panix\engine\widgets\LinkPager'], $this->pager);
    }
}