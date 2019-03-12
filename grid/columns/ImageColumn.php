<?php

namespace panix\engine\grid\columns;

use panix\engine\Html;
use yii\base\Model;
use yii\grid\DataColumn;

class ImageColumn extends DataColumn
{

    public $format = 'raw';
    public $contentOptions = ['class' => 'text-center image'];
    public $headerOptions = ['class' => 'text-center'];
    public $filter = true;
    public $filterInputOptions = ['label' => 'Только с фото'];

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

            $id = Html::getInputId($model, $this->attribute);

            $html = Html::activeCheckbox($model, $this->attribute, $this->filterInputOptions);

            return $html . $error;

        }

        return parent::renderFilterCellContent();
    }

}
