<?php

namespace panix\engine\grid\columns;

use Yii;
use Closure;
use yii\web\View;
use yii\grid\CheckboxColumn as BaseCheckboxColumn;
use panix\engine\Html;
use panix\engine\bootstrap5\ButtonDropdown;

/**
 * Class CheckboxColumn
 *
 * @property array $customActions
 *
 * @package panix\engine\grid\columns
 */
class CheckboxColumn extends BaseCheckboxColumn
{

    public $contentOptions = ['style' => 'width: 50px;', 'class' => 'text-center'];
    public $headerOptions = ['style' => 'width: 50px;', 'class' => 'text-center'];
    public $filterOptions = ['class' => 'text-center'];
    /**
     * @var string the name of the input checkbox input fields. This will be appended with `[]` to ensure it is an array.
     */
    public $name = 'selection';
    public $enableMenu = true;

    public $checkboxOptions = [];

    /**
     * @var boolean whether it is possible to select multiple rows. Defaults to `true`.
     */
    public $multiple = true;
    protected $_customActions;

    /**
     * Registers the needed JavaScript.
     * @since 2.0.8
     */
    public function registerClientScript()
    {
        parent::registerClientScript();
        $this->grid->getView()->registerJs("
            var grid_selections;
            var gridID = '{$this->grid->getId()}';
		", View::POS_HEAD);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {

        parent::init();
        CheckboxColumnAsset::register($this->grid->getView());
        $this->contentOptions = ['class' => 'text-center'];

        // $this->grid->filterRowOptions = ['class' => 'text-center'];

        if ($this->enableMenu) {
            $this->grid->footerRowOptions = ['class' => 'text-center'];
            $this->footer = ButtonDropdown::widget([
                'dropdownClass' => 'panix\engine\bootstrap\Dropdown4',
                'label' => Html::icon('menu'),
                'encodeLabel' => false,
                //'containerOptions' => ['class' => 'dropup hidden', 'id' => 'grid-actions'],
                'buttonOptions' => ['class' => 'btn-sm btn-secondary'],
                'dropdown' => [
                    'encodeLabels' => false,
                    'items' => $this->getCustomActions(),
                ],
            ]);
        }

    }

    protected function renderFilterCellContent()
    {
        return $this->footer;
    }

    public function setCustomActions($actions)
    {
        foreach ($actions as $action) {
            if (!isset($action['linkOptions'])) {
                $action['linkOptions'] = $this->getDefaultActionOptions();
            } else {
                $action['linkOptions'] = array_merge($this->getDefaultActionOptions(), $action['linkOptions']);
            }
            $this->_customActions[] = $action;
        }
    }

    public function getCustomActions()
    {
        $this->customActions = [
            [
                'label' => Yii::t('app/default', 'DELETE'),
                'url' => ['delete'],
                'icon' => 'delete',
                'linkOptions' => [
                    'class' => 'dropdown-item grid-action ',
                    'data-confirm-info' => Yii::t('app/default', 'DELETE_CONFIRM'),
                    //'data-method'=>'POST',
                    'onClick' => 'checkSelected();',
                    'data-pjax' => 0
                ]
            ]
        ];
        return $this->_customActions;
    }

    /**
     * @return array Default linkOptions for footer action.
     */
    public function getDefaultActionOptions()
    {
        return [
            // 'data-confirm' => Yii::t('app/default', 'CONFIRM'),
            'class' => 'dropdown-item',
            //'data-method'=>'POST'
            //'data-pjax' => 0,
            // 'onClick' => 'gridAction(this); return false;'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function renderHeaderCellContent2()
    {
        $name = rtrim($this->name, '[]') . '_all';
        /*$id = $this->grid->options['id'];
        $options = json_encode([
            'name' => $this->name,
            'multiple' => $this->multiple,
            'checkAll' => $name,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);*/


        // $this->grid->view->registerJs("jQuery('#$id').yiiGridView('setSelectionColumn', $options);");

        if ($this->header !== null || !$this->multiple) {
            return parent::renderHeaderCellContent();
        } else {
            return Html::checkBox($name, false, ['class' => 'select-on-check-all']);
        }
    }

    protected function getHeaderCheckBoxName2()
    {
        $name = $this->name;
        if (substr_compare($name, '[]', -2, 2) === 0) {
            $name = substr($name, 0, -2);
        }
        if (substr_compare($name, ']', -1, 1) === 0) {
            $name = substr($name, 0, -1) . '_all]';
        } else {
            $name .= '_all';
        }

        return $name;
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->checkboxOptions instanceof Closure) {
            $options = call_user_func($this->checkboxOptions, $model, $key, $index, $this);
        } else {
            $options = $this->checkboxOptions;
            if (!isset($options['value'])) {
                $options['value'] = is_array($key) ? json_encode($key, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $key;
            }
        }

        return Html::checkbox($this->name, !empty($options['checked']), $options);
    }

}
