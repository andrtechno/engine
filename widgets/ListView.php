<?php

namespace panix\engine\widgets;

use Yii;
use yii\helpers\ArrayHelper;

class ListView extends \yii\widgets\ListView
{
    public $emptyTextOptions = ['class' => 'alert alert-info'];

    public function init()
    {
        if (!$this->emptyText)
            $this->emptyText = Yii::t('app', 'NO_INFO');

        parent::init();
        $this->pager = ArrayHelper::merge([
            'class' => LinkPager::class
        ], $this->pager);
    }
}