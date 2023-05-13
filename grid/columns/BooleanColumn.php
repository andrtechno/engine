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
                $text = ($model->{$this->attribute}) ? Yii::t('app/default', 'YES') : Yii::t('app/default', 'NO');
                $class = ($model->{$this->attribute}) ? 'success' : 'secondary';
                return Html::tag('span', $text, ['class' => 'badge bg-' . $class]);
            };
        }
        parent::init();
    }

}
