<?php

namespace panix\engine\grid\columns;

use Yii;
use Closure;
use yii\base\InvalidConfigException;
use panix\engine\Html;
use panix\engine\bootstrap\ButtonDropdown;
use yii\web\View;

/**
 * CheckboxColumn displays a column of checkboxes in a grid view.
 *
 * To add a CheckboxColumn to the [[GridView]], add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => 'yii\grid\CheckboxColumn',
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * Users may click on the checkboxes to select rows of the grid. The selected rows may be
 * obtained by calling the following JavaScript code:
 *
 * ```javascript
 * var keys = $('#grid').yiiGridView('getSelectedRows');
 * // keys is an array consisting of the keys associated with the selected rows
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CheckboxColumn extends Column
{

    public $headerOptions = ['style' => 'width: 80px;'];

    /**
     * @var string the name of the input checkbox input fields. This will be appended with `[]` to ensure it is an array.
     */
    public $name = 'selection';

    /**
     * @var array|\Closure the HTML attributes for checkboxes. This can either be an array of
     * attributes or an anonymous function ([[Closure]]) that returns such an array.
     * The signature of the function should be the following: `function ($model, $key, $index, $column)`.
     * Where `$model`, `$key`, and `$index` refer to the model, key and index of the row currently being rendered
     * and `$column` is a reference to the [[CheckboxColumn]] object.
     * A function may be used to assign different attributes to different rows based on the data in that row.
     * Specifically if you want to set a different value for the checkbox
     * you can use this option in the following way (in this example using the `name` attribute of the model):
     *
     * ```php
     * 'checkboxOptions' => function($model, $key, $index, $column) {
     *     return ['value' => $model->name];
     * }
     * ```
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $checkboxOptions = [];

    /**
     * @var boolean whether it is possible to select multiple rows. Defaults to `true`.
     */
    public $multiple = true;
    protected $_customActions;

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException if [[name]] is not set.
     */
    public function init()
    {
        parent::init();
        if (empty($this->name)) {
            throw new InvalidConfigException('The "name" property must be set.');
        }
        if (substr_compare($this->name, '[]', -2, 2)) {
            $this->name .= '[]';
        }

        $id = $this->grid->getId();
        $name = strtr($this->name, array('[' => "\\[", ']' => "\\]"));


        $this->grid->getView()->registerJs("


jQuery(document).on('click','#{$id} .select-on-check-all',function() {
    var checked=this.checked;
    jQuery('#{$id} input[name=\"{$name}\"]:enabled').each(function() {
        this.checked=checked;
        if (checked == this.checked) {
            $(this).closest('table tbody tr').removeClass('active');
            $('#grid-actions').addClass('hidden');
        }
	if (this.checked) {
            $(this).closest('table tbody tr').addClass('active');
            $('#grid-actions').removeClass('hidden');
        }
    });
});
    

jQuery(document).on('click', '#grid-action-delete', function() {
    var keys = $('#{$id}').yiiGridView('getSelectedRows');
    $.ajax({
        url:'/admin/pages/default/delete',
        type:'POST',
        dataType:'json',
        data:{id:keys},
        success:function(data){
            common.notify(data.message,'success');
            $('#{$id}').yiiGridView('applyFilter');
        }
    });
});

jQuery(document).on('click', '#{$id} input[name=\"$name\"]', function() {
    jQuery('#{$id} .select-on-check-all').prop('checked', jQuery(\"input[name='$name']\").length==jQuery(\"input[name='$name']:checked\").length);
    var checked=this.checked;
    this.checked=checked;
    if (checked == this.checked) {
        $(this).closest('table tbody tr').removeClass('active');
        $('#grid-actions').addClass('hidden');
    }
    if (this.checked) {
        $(this).closest('table tbody tr').addClass('active');
        $('#grid-actions').removeClass('hidden');
    }
});

",View::POS_END);

        //print_r($this->getCustomActions());die;
        $this->contentOptions = ['class' => 'text-center'];
        $this->grid->footerRowOptions = ['class' => 'text-center'];
       // $this->grid->filterRowOptions = ['class' => 'text-center'];


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
                'label' => Yii::t('app', 'DELETE'),
                'url' => Yii::$app->urlManager->createUrl('delete'),
                'icon' => 'delete',
                'options' => [
                    'class' => 'dropdown-item',
                    'data-question' => Yii::t('app', 'CONFIRM'),
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
            //'data-token' => Yii::app()->request->csrfToken,
            'data-question' => Yii::t('app', 'CONFIRM'),
            'class' => 'dropdown-item',
            // 'model' => $this->dataProvider->modelClass,
            'onClick' => strtr('return $.fn.yiiGridView.runAction(":grid", this);', [
                    ':grid' => $this->grid->options['id']
                ]
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function renderHeaderCellContent()
    {
        $name = rtrim($this->name, '[]') . '_all';
        $id = $this->grid->options['id'];
        $options = json_encode([
            'name' => $this->name,
            'multiple' => $this->multiple,
            'checkAll' => $name,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


        $this->grid->view->registerJs("jQuery('#$id').yiiGridView('setSelectionColumn', $options);");

        if ($this->header !== null || !$this->multiple) {
            return parent::renderHeaderCellContent();
        } else {
            return Html::checkBox($name, false, ['class' => 'select-on-check-all']);
        }
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
