<?php

namespace panix\engine\grid\columns\jui;

use panix\engine\Html;
use yii\base\Model;
use yii\grid\DataColumn;
use yii\jui\DatePicker;
use yii\jui\Slider;

class DatepickerColumn extends DataColumn
{


    public $dateFormat = 'yyyy-MM-dd';
    public $options;

    /**
     * {@inheritdoc}
     */
    protected function renderFilterCellContent()
    {
        if (is_string($this->filter)) {
            return $this->filter;
        }

        $model = $this->grid->filterModel;


        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            if ($model->hasErrors($this->attribute)) {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute, $this->grid->filterErrorOptions);
            } else {
                $error = '';
            }



            $html = DatePicker::widget([
                'model' => $model,
                'attribute' => $this->attribute,
                'dateFormat' => 'yyyy-MM-dd',
                'options' => ['class' => 'form-control']
            ]);

            return $html . $error;

        }

        return parent::renderFilterCellContent();
    }

}
