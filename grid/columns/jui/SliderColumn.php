<?php

namespace panix\engine\grid\columns\jui;

use Yii;
use panix\engine\Html;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\jui\Slider;

class SliderColumn extends DataColumn
{

    public $prefix = '';
    public $min;
    public $max;
    public $headerOptions = ['style' => 'width:150px'];
    public $range = true;
    public $minCallback,$maxCallback;
    public function init()
    {
        if (is_null($this->min) || is_null($this->max))
            throw new InvalidConfigException(\Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => 'MIN and MAX']));

        if ($this->min > $this->max)
            throw new InvalidConfigException(\Yii::t('yii', '{attribute} must be no less than {min}.', ['attribute' => 'MIN', 'min' => 'MAX']));

        if (!is_int($this->min) || !is_int($this->max))
            throw new InvalidConfigException(\Yii::t('yii', '{attribute} must be an integer.', ['attribute' => 'MIN and MAX']));

        parent::init();
    }

    public function getDataCellMin($value)
    {
        if ($this->minCallback !== null) {
            if (is_string($this->minCallback)) {
                return ArrayHelper::getValue($value, $this->minCallback);
            }

            return call_user_func($this->minCallback, $value);
        } elseif ($this->attribute !== null) {
            return $value;
        }

        return null;
    }

    public function getDataCellMax($value)
    {
        if ($this->maxCallback !== null) {
            if (is_string($this->maxCallback)) {
                return ArrayHelper::getValue($value, $this->maxCallback);
            }

            return call_user_func($this->maxCallback, $value);
        } elseif ($this->attribute !== null) {
            return $value;
        }

        return null;
    }

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

            $values = Yii::$app->request->get($model->formName());

            $inputValueMin = (isset($values[$this->attribute]['min'])) ? $values[$this->attribute]['min'] : $this->min;
            $inputValueMax = (isset($values[$this->attribute]['max'])) ? $values[$this->attribute]['max'] : $this->max;

            if ($this->min !== $this->max) {

                $html = '<span class="float-left mt-3"><span id="' . $id . '_value-min">' . $this->getDataCellMin($inputValueMin) . '</span> ' . $this->prefix . '</span><span class="float-right mt-3"><span id="' . $id . '_value-max">' . $this->getDataCellMax($inputValueMax) . '</span> ' . $this->prefix . '</span>';
                $html .= Slider::widget([
                    'clientOptions' => [
                        'range' => $this->range,
                        'min' => $this->min,
                        'max' => $this->max,
                        'values' => [(int)$inputValueMin, (int)$inputValueMax],

                    ],
                    'clientEvents' => [
                        'slide' => 'function(event, ui) {
                        $("#' . $id . '_min").val(ui.values[0]);
                        $("#' . $id . '_max").val(ui.values[1]);
                        $("#' . $id . '_value-min").text(ui.values[0]);
                        $("#' . $id . '_value-max").text(ui.values[1]);
			        }',
                    'stop' => 'function(event, ui){
                        $("#' . $this->grid->id . '").yiiGridView("applyFilter");
                    }'
                    ],
                ]);

                $html .= Html::hiddenInput(Html::getInputName($model, $this->attribute) . '[min]', $inputValueMin, ['id' => $id . '_min']);
                $html .= Html::hiddenInput(Html::getInputName($model, $this->attribute) . '[max]', $inputValueMax, ['id' => $id . '_max']);

                return '<div class="clearfix">' . $html . '</div>' . $error;
            }
        }

        //  return parent::renderFilterCellContent();
    }

}
