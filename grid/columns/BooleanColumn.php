<?php

namespace panix\engine\grid\columns;

use Yii;
use yii\helpers\Html;

class BooleanColumn extends \yii\grid\DataColumn {

    public $contentOptions = ['class' => 'text-center'];
    public $format = 'html';

    /**
     * @inheritdoc
     */
    public function init() {
        if (extension_loaded('intl')) {
            //TODO: intl
        }

        if ($this->format == 'html') {
            $this->value = function($model) {
                $text = ($model->{$this->attribute}) ? Yii::t('app', 'YES') : Yii::t('app', 'NO');
                $class = ($model->{$this->attribute}) ? 'success' : 'default';
                return Html::tag('span', $text, ['class' => 'label label-' . $class]);
            };
        }
        parent::init();
    }

}
