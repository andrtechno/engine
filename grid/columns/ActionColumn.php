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
    public $editColumnsUrl;
    public $enableEditColumns = true;

    /**
     * @inheritdoc
     */
    public function init()
    {

        if (!$this->header)
            $this->header = Yii::t('app/default', 'OPTIONS');

        if (!$this->editColumnsUrl) {
            $this->editColumnsUrl = Url::to('/admin/app/default/edit-columns');
        }
        // $this->btnSize = $config['grid_btn_icon_size'];
        // if (!$this->pjax) {
        //    $this->pjax = '#pjax-container';
        //}
        $gid = $this->grid->id;
        if ($this->filter) {
            if (isset(($this->grid->dataProvider)->query) && $this->enableEditColumns) {
                if (method_exists(($this->grid->dataProvider)->query->modelClass, 'getGridColumns')) {

                    /*$items[] = [
                        'label' => Html::icon('table') . ' ' . Yii::t('app/admin', 'EDIT_GRID_COLUMNS'),
                        'url' => $this->editColumnsUrl,
                        'linkOptions' => [
                            // 'data-target' => "#",
                            'class' => 'dropdown-item edit-columns',
                            'data-pjax' => '0',
                            'data-grid-id' => $this->grid->id,
                            // 'data-model' => (isset($this->grid->dataProvider->query))?$this->grid->dataProvider->query->modelClass:'s',
                            // 'data-pjax-id' => 'pjax-' . strtolower(basename($this->grid->dataProvider->query->modelClass)),
                        ]
                    ];*/
                    $items[] = [
                        'label' => Html::icon('table') . ' ' . Yii::t('app/admin', 'EDIT_GRID_COLUMNS'),
                        'url' => $this->editColumnsUrl,
                        'linkOptions' => [
                            'data-target' => "#columnsModal",
                            'data-toggle' => "modal",
                            'class' => 'dropdown-item edit-columns',
                            'data-pjax' => '0',
                            'data-grid-id' => $this->grid->id,
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
        echo $view->render('@vendor/panix/engine/grid/_modal', []);

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

            if (!Yii::$app->request->isPjax) {
                $view->registerJs("
                var modalColumns = $('#columnsModal');
                $(document).on('click','#columnsModal .modal-footer button',function(e){
                    var ps = parseInt($('#pageSize').val());
                    var valid = true;
    
                    if(!ps){
                        valid=false;
                        $('#pageSize').addClass('is-invalid');
                        $('#pageSize-error').html('" . Yii::t('yii', '{attribute} must be an integer.', ['attribute' => '']) . "');
                    }
                    if($('#pageSize').val() === ''){
                        valid=true;
                    }
                    if(valid){
                        $('#pageSize').removeClass('is-invalid');
                        $('#pageSize-error').html('');
                        $.ajax({
                            url: '" . $this->editColumnsUrl . "',
                            type: 'POST',
                            data: modalColumns.find('form').serialize(),
                            success:function(){
                                modalColumns.modal('hide');
                                $.pjax.reload('#pjax-" . $this->grid->id . "', {timeout: false});
                            }
                        });
                    }
                });", View::POS_END, 'edit-columns_pjax');
            }

            $view->registerJs("
                modalColumns.on('hidden.bs.modal', function (event) {
                    //$('#columnsModal .modal-body').html('');
                });
                $('.edit-columns').on('click',function(e){
                    e.preventDefault();
                    var isHtml = $.trim($('#columnsModal .modal-body').html());
                    if(!isHtml){
                        $.ajax({
                            type:'POST',
                            url:$(this).attr('href'),
                            data:{
                                grid_id:$(this).data('grid-id'),
                                model:'" . $classNamePath . "',
                            },
                            beforeSend:function(){
                                modalColumns.modal('show');
                                modalColumns.addClass('pjax-loading');
                            },
                            success:function(data){
                                $('#columnsModal .modal-body').html(data);
                                modalColumns.removeClass('pjax-loading');
                            }
                        });
                    }else{
                        modalColumns.modal('show');
                    }
                    return false;
                });", View::POS_END, 'edit-columns_modal');

        }
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        $controller = Yii::$app->controller;
        $module = $controller->module;

        if (!isset($this->buttons['switch'])) {
            if (Yii::$app->user->can("/{$module->id}/{$controller->id}/*") || Yii::$app->user->can("/{$module->id}/{$controller->id}/switch")) {
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
                                $url['value']=$switch_data;
                                return Html::a(Html::icon($icon), Url::toRoute($url), [
                                    'title' => Yii::t('app/default', 'GRID_SWITCH'),
                                    'class' => 'd-flex align-items-center btn ' . $this->btnSize . ' ' . $class . ' switch', //linkTarget
                                    'data-pjax' => 0,
                                ]);
                            }
                        }
                    }
                };
            }
        }
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url) {
                return Html::a(Html::icon('search'), $url, [
                    'title' => Yii::t('yii', 'View'),
                    'class' => 'd-flex align-items-center btn ' . $this->btnSize . ' btn-outline-secondary',
                    'data-pjax' => 0,
                ]);
            };
        }
        if (!isset($this->buttons['update'])) {
            if (Yii::$app->user->can("/{$module->id}/{$controller->id}/*") || Yii::$app->user->can("/{$module->id}/{$controller->id}/update")) {
                $this->buttons['update'] = function ($url, $model) {
                    /** @var $model ActiveRecord */
                    if (isset($model->primaryKey) && isset($model->disallow_update)) {
                        if (!in_array($model->primaryKey, $model->disallow_update)) {
                            return Html::a(Html::icon('edit'), $url, [
                                'title' => Yii::t('yii', 'Update'),
                                'class' => 'd-flex align-items-center btn ' . $this->btnSize . ' btn-outline-secondary',
                                'data-pjax' => 0,
                            ]);
                        }
                    } else {
                        return Html::a(Html::icon('edit'), $url, [
                            'title' => Yii::t('yii', 'Update'),
                            'class' => 'd-flex align-items-center btn ' . $this->btnSize . ' btn-outline-secondary',
                            'data-pjax' => 0,
                        ]);
                    }
                };
            }
        }

        if (!isset($this->buttons['delete'])) {
            if (Yii::$app->user->can("/{$module->id}/{$controller->id}/*") || Yii::$app->user->can("/{$module->id}/{$controller->id}/delete")) {
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


                            $this->grid->view->registerJs("
                                $(document).on('click','.delete',function(e){
                                var that = $(this);
                                e.preventDefault();

            yii.confirm = function(message, ok, cancel) {
                bootbox.confirm({
                    message:message,
                    buttons: {
                        confirm: {
                            label: '<i class=\"icon-check\"></i>  '+common.message.ok,
                            className: 'btn-success'
                        },
                        cancel: {
                            label: '<i class=\"icon-cancel\"></i>  '+common.message.cancel,
                            className: 'btn-outline-secondary'
                        }
                    },
                    callback:function(result) {
                        if (result) { 
                            $.ajax(that.attr('href'), {
                                type: 'POST',
                                dataType:'json',
                            }).done(function(data) {
                                $.pjax.reload({container: '#" . $this->grid->id . "'});
                                common.notify(data.message,'success');
                            });
                        } else {
                            !cancel || cancel();
                        }
                    }
                });
            }
                              return false;
                                });", View::POS_END, 'delete');


                            return Html::a(Html::icon('delete'), $url, [
                                'title' => Yii::t('yii', 'Delete'),
                                'aria-label' => Yii::t('yii', 'Delete'),
                                'data-pjax' => '0',
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('app/default', 'DELETE_CONFIRM'),
                                'class' => 'btn ' . $this->btnSize . ' btn-outline-danger delete',
                                'onclick' => 'return false;'
                                /*    'onclick' => "

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
                                ",*/
                            ]);
                        }
                    } else {
                        return Html::a(Html::icon('delete'), $url, [
                            'title' => Yii::t('yii', 'Delete'),
                            'class' => 'd-flex align-items-center btn ' . $this->btnSize . ' btn-secondary',
                            'data-method' => 'POST',
                            'data-confirm' => Yii::t('app/default', 'DELETE_ITEM'),
                            'data-pjax' => '0',
                        ]);
                    }
                };
            }
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
