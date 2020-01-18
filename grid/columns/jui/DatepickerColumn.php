<?php

namespace panix\engine\grid\columns\jui;

use Yii;
use yii\base\Model;
use yii\grid\DataColumn;
use yii\jui\DatePicker;
use panix\engine\Html;

class DatepickerColumn extends DataColumn
{


    public $dateFormat = 'yyyy-MM-dd';
    public $options;
    public $format = 'raw';
    public $headerOptions = ['style' => 'width:150px', 'class' => 'text-center'];
    public $contentOptions = ['class' => 'text-center'];

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
                'dateFormat' => $this->dateFormat,
                'options' => ['class' => 'form-control', 'autocomplete' => 'off']
            ]);

            return $html . $error;

        }

        return parent::renderFilterCellContent();
    }

    public function getDataCellValue($model, $key, $index)
    {

        if ($this->value === null) {
            if ($model->{$this->attribute}) {
                $html = Html::beginTag('span', ['class' => 'bootstrap-tooltip', 'title' => Yii::t('app/default', 'IN') . ' ' . Yii::$app->formatter->asTime($model->{$this->attribute})]);
                $html .= Yii::$app->formatter->asDate($model->{$this->attribute});
                $html .= Html::endTag('span');
                return $html;
            }
        } else {
            return parent::getDataCellValue($model, $key, $index);
        }
    }
}
