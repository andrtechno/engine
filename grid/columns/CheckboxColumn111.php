<?php

namespace panix\engine\grid\columns;

use Yii;
use Closure;
use yii\base\InvalidConfigException;
use panix\engine\Html;
use panix\engine\bootstrap\ButtonDropdown;
use yii\web\View;


class CheckboxColumn111 extends \yii\grid\CheckboxColumn
{

    public $headerOptions = ['style' => 'width: 70px;','class' => 'text-center'];

    /**
     * @var string the name of the input checkbox input fields. This will be appended with `[]` to ensure it is an array.
     */
    public $name = 'selection';


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
       // if (empty($this->name)) {
       //     throw new InvalidConfigException('The "name" property must be set.');
      //  }
       // if (substr_compare($this->name, '[]', -2, 2)) {
       //     $this->name .= '[]';
       // }

        //$id = $this->grid->getId();
        //$name = strtr($this->name, array('[' => "\\[", ']' => "\\]"));


        $this->grid->getView()->registerJs("


jQuery(document).on('click','#{$id} .select-on-check-all',function(e) {
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
    
/*
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
*/
jQuery(document).on('click', '#{$id} input[name=\"$name\"]', function(e) {
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
    return false;
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
                'label' => Yii::t('app/default', 'DELETE'),
                'url' => ['delete'],
                'icon' => 'delete',
                'options' => [
                    'class' => 'dropdown-item',
                    'data-confirm' => Yii::t('app/default', 'CONFIRM'),
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
            'data-confirm' => Yii::t('app/default', 'CONFIRM'),
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


       // $this->grid->view->registerJs("jQuery('#$id').yiiGridView('setSelectionColumn', $options);");

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
