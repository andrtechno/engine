<?php

namespace panix\engine\widgets;

use yii\helpers\ArrayHelper;

class ListView extends \yii\widgets\ListView
{

    public function init()
    {
        parent::init();
        $this->pager = ArrayHelper::merge([
            'class' => LinkPager::class
        ], $this->pager);
    }
}