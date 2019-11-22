<?php

namespace panix\engine\grid\columns;

use Yii;
use Closure;
use yii\web\View;
use yii\grid\CheckboxColumn as BaseCheckboxColumn;
use panix\engine\Html;
use panix\engine\bootstrap\ButtonDropdown;

/**
 * Class CheckboxColumn
 *
 * @property array $customActions
 *
 * @package panix\engine\grid\columns
 */
class CheckboxColumn extends BaseCheckboxColumn
{

    public $headerOptions = ['style' => 'width: 70px;', 'class' => 'text-center'];
    public $filterOptions = ['class' => 'text-center'];
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

        $this->grid->getView()->registerJs("
            $(document).on('click','#{$this->grid->getId()} input[type=\"checkbox\"]',function(e) {
            var keys = $('#{$this->grid->getId()}').yiiGridView('getSelectedRows');
                console.log(keys);
            });

            function runAction(that){
                var keys = $('#{$this->grid->getId()}').yiiGridView('getSelectedRows');
                var url = $(that).attr('href');

                if (confirm($(that).data('confirm'))) {
                    console.log('LALAL',keys,that);
                    $.ajax({
                        url:url,
                        type:'POST',
                        data:{id:keys},
                        success:function(data){
                            console.log(data);
                            $.pjax.reload({container:'#pjax-{$this->grid->getId()}'});
                            //$('#{$this->grid->getId()}').yiiGridView('applyFilter');
                        }
                    });
                }

                return false;
            }
    ", View::POS_END);

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
                'url' => ['delete'],
                'icon' => 'delete',
                'options' => [
                    'class' => 'dropdown-item',
                    'data-confirm' => Yii::t('app', 'CONFIRM'),
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
            'data-confirm' => Yii::t('app', 'CONFIRM'),
            'class' => 'dropdown-item',
            // 'model' => $this->dataProvider->modelClass,
            'onClick' => 'return runAction(this);'
            /*'onClick' => strtr('return $.fn.yiiGridView.runAction(":grid", this);', [
                    ':grid' => $this->grid->options['id']
                ]
            ),*/
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
