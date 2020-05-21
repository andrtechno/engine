<?php

namespace panix\engine\grid\columns;


use panix\engine\db\ActiveRecord;
use Yii;
use Closure;
use yii\helpers\Url;
use panix\engine\bootstrap\ButtonDropdown;
use panix\engine\Html;
use yii\web\JsExpression;
use yii\grid\DataColumn;
use yii\web\View;

class ActionColumn extends DataColumn
{

    public $controller;
    public $template = '{switch} {update} {delete}';
    public $buttons = [];
    public $urlCreator;
    public $btnSize = 'btn-sm';
    public $headerOptions = ['style' => 'width:150px;'];
    public $contentOptions = ['class' => 'text-center'];
    public $pjax;
    public $filter = true;

    /**
     * @inheritdoc
     */
    public function init()
    {

        if(!$this->header)
            $this->header = Yii::t('app/default', 'OPTIONS');

        // $this->btnSize = $config['grid_btn_icon_size'];
        // if (!$this->pjax) {
        //    $this->pjax = '#pjax-container';
        //}

        if ($this->filter) {
            if (isset(($this->grid->dataProvider)->query)) {
                if (method_exists(($this->grid->dataProvider)->query->modelClass, 'getGridColumns')) {
                    $items[] = [
                        'label' => Html::icon('table') . ' ' . Yii::t('app/admin', 'EDIT_GRID_COLUMNS'),
                        'url' => '#',
                        'linkOptions' => [
                            // 'data-target' => "#",
                            'class' => 'dropdown-item edit-columns',
                            'data-pjax' => '0',
                            // 'data-grid-id' => $this->grid->id,
                            // 'data-model' => (isset($this->grid->dataProvider->query))?$this->grid->dataProvider->query->modelClass:'s',
                            // 'data-pjax-id' => 'pjax-' . strtolower(basename($this->grid->dataProvider->query->modelClass)),
                        ]
                    ];
                }
            }
            $items[] = [
                'label' => Html::icon('refresh') . ' ' . Yii::t('app/default', 'REFRESH'),
                'url' => '#',
                'linkOptions' => [
                    'class' => 'dropdown-item',
                    'data-pjax' => 0,
                    //'onClick' => '$.pjax({container: "#pjax-'.$this->grid->id.'"})'
                    'onClick' => '$.pjax.reload("#pjax-' . $this->grid->id . '", {timeout : false});',
                ]
            ];

            $this->filter = ButtonDropdown::widget([
                'label' => Html::icon('settings'),
                'encodeLabel' => false,
                'dropdownClass' => 'panix\engine\bootstrap\Dropdown4',
                //'containerOptions' => ['class' => '', 'id' => 'grid-settings'],
                'buttonOptions' => ['class' => 'btn-sm btn-secondary'],
                'dropdown' => [
                    'options' => ['class' => 'dropdown-menu-right'],
                    'encodeLabels' => false,
                    'items' => $items,
                ],
            ]);
        }
        $this->filterOptions = ['class' => 'text-center'];
        $this->initDefaultButtons();

        parent::init();


        $view = $this->grid->getView();


        /*$view->registerJs("
                            $(document).on('click','.delete',function(e){
                                e.preventDefault();
                                if (confirm('" . Yii::t('app/default', 'DELETE_CONFIRM') . "')) {
                                    $.ajax($(this).attr('href'), {
                                        type: 'POST',
                                        dataType:'json',
                                    }).done(function(data) {
                                        $.pjax.reload({container: '#" . $this->grid->id . "'});
                                            console.log(data);
                                            common.notify(data.message,'success');
                                            //$('#{$this->grid->id}').yiiGridView('applyFilter');
                                    });
                                }
                                return false;
                            });", View::POS_END, 'delete');*/

        if (isset(($this->grid->dataProvider)->query)) {
            $classNamePath = '/' . implode('/', explode('\\', ($this->grid->dataProvider)->query->modelClass));

            $view->registerJs("
        $(function() {

            $('.edit-columns').on('click',function(e){
                e.preventDefault();

                $.ajax({
                    type:'POST',
                    url:common.url('/admin/app/default/edit-columns'),
                    data:{
                        grid_id:'" . $this->grid->getId() . "',
                        model:'" . $classNamePath . "',
                    },
                    success:function(data){
                        
                        $('#edit-columns_dialog').html(data);
                        $('#edit-columns_dialog').dialog('open');
                    }
                });
            });
                    
        });
        ", View::POS_END, 'edit-columns_dialog');



            echo \panix\engine\jui\Dialog::widget([
                'id' => 'edit-columns_dialog',
                'clientOptions' => [
                    'modal' => true,
                    'autoOpen' => false,
                    'draggable' => false,
                    'resizable' => false,
                    'dialogClass' => 'edit-columns_dialog',
                    'width' => '50%',
                    'title' => Yii::t('app/admin', 'EDIT_GRID_COLUMNS'),
                    'buttons' => [
                        [
                            'text' => "Ok",
                            'class' => "ui-button",
                            'click' => new JsExpression("function(){
                                var form = $('#edit_grid_columns_form').serialize();
                                $.ajax({
                                    url:common.url('/admin/app/default/edit-columns'),
                                    type:'POST',
                                    data:form,
                                    success:function(){
                                        $('#edit-columns_dialog').dialog('close').remove()
                                        $.pjax.reload('#" . $this->grid->id . "', {timeout: false});
                                    }
                                });
                            }")
                        ]
                    ]
                ]
            ]);
        }
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['switch'])) {
            $this->buttons['switch'] = function ($url, $model) {
                /** @var $model ActiveRecord */
                //unset($url,$key);
                if (isset($model->primaryKey) && isset($model->disallow_switch)) {
                    if (!in_array($model->primaryKey, $model->disallow_switch)) {
                        if (isset($model->switch)) {
                            if ($model->switch) {
                                $icon = 'eye';
                                $class = 'btn-outline-success';
                            } else {
                                $icon = 'eye-close';
                                $class = 'btn-outline-secondary';
                            }

                            $switch_data = $model->switch ? 0 : 1;
                            return Html::a(Html::icon($icon), Url::toRoute(['switch', 'id' => $model->primaryKey, 'value' => $switch_data]), [
                                'title' => Yii::t('app/default', 'GRID_SWITCH'),
                                'class' => 'btn ' . $this->btnSize . ' ' . $class . ' switch', //linkTarget
                                'data-pjax' => 0,
                            ]);
                        }
                    }
                }
            };
        }
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url) {
                return Html::a(Html::icon('search'), $url, [
                    'title' => Yii::t('yii', 'View'),
                    'class' => 'btn ' . $this->btnSize . ' btn-outline-secondary',
                    'data-pjax' => 0,
                ]);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model) {
                /** @var $model ActiveRecord */
                if (isset($model->primaryKey) && isset($model->disallow_update)) {
                    if (!in_array($model->primaryKey, $model->disallow_update)) {
                        return Html::a(Html::icon('edit'), $url, [
                            'title' => Yii::t('yii', 'Update'),
                            'class' => 'btn ' . $this->btnSize . ' btn-outline-secondary',
                           // 'data-pjax' => 0,
                        ]);
                    }
                } else {
                    return Html::a(Html::icon('edit'), $url, [
                        'title' => Yii::t('yii', 'Update'),
                        'class' => 'btn ' . $this->btnSize . ' btn-outline-secondary',
                        'data-pjax' => 0,
                    ]);
                }
            };
        }

        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model) {
                /** @var $model ActiveRecord */
                /* return Html::a('<i class="text-danger icon-delete"></i>', $url, [
                  'title' => Yii::t('yii', 'Delete'),
                  'class' => 'btn ' . $this->btnSize . ' btn-secondary',
                  'data-confirm' => Yii::t('app/default', 'DELETE_ITEM'),
                  'data-method' => 'post',
                  'data-pjax' => '0',
                  ]); */
                if (isset($model->primaryKey) && isset($model->disallow_delete)) {
                    if (!in_array($model->primaryKey, $model->disallow_delete)) {



                        /*$this->grid->view->registerJs("
                            $(document).on('click','.delete',function(e){
                                e.preventDefault();
                                if (confirm('" . Yii::t('app/default', 'DELETE_CONFIRM') . "')) {
                                    $.ajax('$url', {
                                        type: 'POST',
                                        dataType:'json',
                                    }).done(function(data) {
                                        $.pjax.reload({container: '#" . $this->grid->id . "'});
                                            console.log(data);
                                            common.notify(data.message,'success');
                                            //$('#{$this->grid->id}').yiiGridView('applyFilter');
                                    });
                                }
                                //return false;
                            });", View::POS_END, 'delete');*/


                        return Html::a(Html::icon('delete'), $url, [
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'data-pjax' => '0',
                            'class' => 'btn ' . $this->btnSize . ' btn-outline-danger delete',
                            'onclick' => "
                                if (confirm('" . Yii::t('app/default', 'DELETE_CONFIRM') . "')) {
                                    $.ajax('$url', {
                                        type: 'POST',
                                        dataType:'json',
                                    }).done(function(data) {
                                            $.pjax.reload({container: '#{$this->grid->id}'});
                                            console.log(data);
                                            common.notify(data.message,'success');
                                            //$('#{$this->grid->id}').yiiGridView('applyFilter');
                                    });
                                }
                                return false;
                            ",
                        ]);
                    }
                } else {
                    return Html::a(Html::icon('delete'), $url, [
                        'title' => Yii::t('yii', 'Delete'),
                        'class' => 'btn ' . $this->btnSize . ' btn-secondary',
                        'data-confirm' => Yii::t('app/default', 'DELETE_ITEM'),
                        'data-pjax' => '0',
                    ]);
                }
            };
        }
    }

    /**
     * Creates a URL for the given action and model.
     * This method is called for each button and each row.
     * @param string $action the button name (or action ID)
     * @param \yii\db\ActiveRecord $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the current row index
     * @return string the created URL
     */
    public function createUrl($action, $model, $key, $index)
    {
        if ($this->urlCreator instanceof Closure) {
            return call_user_func($this->urlCreator, $action, $model, $key, $index);
        } else {
            $params = is_array($key) ? $key : ['id' => (string)$key];
            $params[0] = $this->controller ? $this->controller . '/' . $action : $action;

            return Url::toRoute($params);
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {

        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];
            if (isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);

                return call_user_func($this->buttons[$name], $url, $model, $key);
            } else {
                return '';
            }
        }, '<div class="btn-group">' . $this->template . '</div>');
    }

}
