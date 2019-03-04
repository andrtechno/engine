<?php

namespace panix\engine\grid\columns;


use panix\engine\View;
use Yii;
use Closure;
use yii\helpers\Url;
use panix\engine\bootstrap\ButtonDropdown;

use panix\engine\Html;
use yii\web\JsExpression;

// \yii\grid\DataColumn
// \yii\grid\ActionColumn
class ActionColumn extends \yii\grid\DataColumn
{

    public $controller;
    public $template = '{switch} {update} {delete}';
    public $buttons = [];
    public $urlCreator;
    public $btnSize = 'btn-sm';
    public $headerOptions = ['style' => 'min-width:150px;'];
    public $contentOptions = ['class' => 'text-center'];
    public $pjax;
    public $filter = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $config = Yii::$app->settings->get('app');
        $this->header = Yii::t('app', 'OPTIONS');

        // $this->btnSize = $config['grid_btn_icon_size'];
        // if (!$this->pjax) {
        //    $this->pjax = '#pjax-container';
        //}
        if ($this->filter) {
            $this->filter = ButtonDropdown::widget([
                'label' => Html::icon('settings'),
                'encodeLabel' => false,
                'dropdownClass' => 'panix\engine\bootstrap\Dropdown4',
                //'containerOptions' => ['class' => '', 'id' => 'grid-settings'],
                'buttonOptions' => ['class' => 'btn-sm btn-secondary'],
                'dropdown' => [
                    'options' => ['class' => 'dropdown-menu-right'],
                    'encodeLabels' => false,
                    'items' => [
                        [
                            'label' => Html::icon('table') . ' Изменить столбцы таблицы',
                            'url' => '/test',
                            'linkOptions' => [
                                'data-target' => "#",
                                'class' => 'dropdown-item edit-columns',
                                'data-pjax' => '0',
                                // 'data-grid-id' => $this->grid->id,
                                // 'data-model' => (isset($this->grid->dataProvider->query))?$this->grid->dataProvider->query->modelClass:'s',
                                // 'data-pjax-id' => 'pjax-' . strtolower(basename($this->grid->dataProvider->query->modelClass)),
                            ]
                        ],
                        [
                            'label' => Html::icon('refresh') . ' Сбросить',
                            'url' => '#',
                            'options' => [
                                'class' => '',
                                'onClick' => '$.pjax.reload("#pjax-' . strtolower(basename($this->grid->dataProvider->query->modelClass)) . '", {timeout : false});',
                            ]
                        ],
                    ],
                ],
            ]);
        }
        $this->filterOptions = ['class' => 'text-center'];
        $this->initDefaultButtons();

        parent::init();
        // $this->registerScripts();

        $view = $this->grid->getView();

        // $oReflectionClass = ($this->grid->dataProvider->query->modelClass);

        //  print_r($oReflectionClass);die;



        $classNamePath =  '/'.implode('/', explode('\\', $this->grid->dataProvider->query->modelClass));




        //echo $classNamePath;
       // die;
        $view->registerJs("
        $(function() {

            $('.edit-columns').click(function(e){
                e.preventDefault();

                $.ajax({
                    type:'POST',
                    url:'/admin/app/default/edit-columns',
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
        ", \panix\engine\View::POS_END, 'edit-columns_dialog');

        echo \yii\jui\Dialog::widget([
            'id' => 'edit-columns_dialog',
            'clientOptions' => [
                'modal' => true,
                'autoOpen' => false,
                'draggable' => false,
                'resizable' => false,
                'dialogClass' => 'test111111111',
                'width' => '50%',
                'buttons' => [
                    [
                        'text' => "Ok",
                        'click' => new JsExpression("function(){
                            var form = $('#edit_grid_columns_form').serialize();
                            $.ajax({
                                url:'/admin/app/default/edit-columns',
                                type:'POST',
                                data:form,
                                success:function(){
                               //$(\'#\'+grid.dialog_id).remove();
                                //$.fn.yiiGridView.update(gridid);//,{url: window.location.href}
                                //$(\'#dialog-overlay\').remove();
                                }
                            });
                        }")
                    ]
                ]
            ]
        ]);
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['switch'])) {
            $this->buttons['switch'] = function ($url, $model, $key) {

                if (isset($model->primaryKey)) {
                    if (!in_array($model->primaryKey, $model->disallow_switch)) {
                        if (isset($model->switch)) {
                            if ($model->switch) {
                                $icon = 'eye';
                                $class = 'btn-outline-success';
                                $switch = 0;
                            } else {
                                $icon = 'eye-close';
                                $class = 'btn-outline-secondary';
                                $switch = 1;
                            }
                            /* return Html::a('<i class="' . $icon . '"></i>', Url::toRoute(['switch', 'id' => $model->primaryKey, 's' => ($model->switch) ? 0 : 1]), [
                             'title' => Yii::t('app', 'GRID_SWITCH'),
                             'class' => "btn ' . $this->btnSize . ' " . $class . " switch",
                             'data-pk' => $model->primaryKey,
                             'data-switch' => ($model->switch) ? 0 : 1,
                             'data-method' => 'post',
                             'data-pjax' => '#pjax-languages',
                             ]);*/

                            $switch_data = $model->switch ? 0 : 1;
                            return Html::a(Html::icon($icon), Url::toRoute(['switch', 'id' => $model->primaryKey, 's' => $switch_data]), [
                                'title' => Yii::t('app', 'GRID_SWITCH'),
                                'class' => 'btn ' . $this->btnSize . ' ' . $class . ' switch linkTarget',
                                'data-pjax' => false,
                                // 'data-method'=>"post",
                                'onclick' => "setSwitch('{$url}',{$model->primaryKey}, {$switch_data}, 'pjax-" . strtolower(basename(get_class($model))) . "'); return false;",
                            ]);


                            /*return Html::a(Html::icon($icon), Url::toRoute(['switch', 'id' => $model->primaryKey, 's' => ($model->switch) ? 0 : 1]), [
                                'title' => Yii::t('app', 'GRID_SWITCH'),
                                'class' => 'btn ' . $this->btnSize . ' ' . $class . ' switch',
                                'data-pjax' => false,
                                'data-push' => false,
                               'data-replace'=>false,
                               // 'data-method'=>"post",
                                'onclick' => "
                                         $.ajax('$url', {
                                             type: 'POST',
                                             //dataType:'json',
                                             data:{
                                                id:$model->primaryKey,
                                                s:$switch,
                                                " . Yii::$app->request->csrfParam . ":'" . Yii::$app->request->csrfToken . "'
                                            },
                                         }).done(function(data) {
                                                 //common.notify(data.message,'success');
                                                 $.pjax.reload({container: '#pjax-" . strtolower(basename(get_class($model))) . "});
                                                // $('#{$this->grid->id}').yiiGridView('applyFilter');
                                         });
                                     return false;
                                 ",
                            ]);*/
                        }
                    }
                }
            };
        }
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                return Html::a(Html::icon('view'), $url, [
                    'title' => Yii::t('yii', 'View'),
                    'class' => 'btn ' . $this->btnSize . ' btn-outline-secondary linkTarget',
                    'data-pjax' => '0',
                ]);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {

                if (isset($model->primaryKey)) {
                    if (!in_array($model->primaryKey, $model->disallow_update)) {
                        return Html::a(Html::icon('edit'), $url, [
                            'title' => Yii::t('yii', 'Update'),
                            'class' => 'btn ' . $this->btnSize . ' btn-outline-secondary linkTarget',
                            'data-pjax' => '0',
                        ]);
                    }
                } else {
                    return Html::a(Html::icon('edit'), $url, [
                        'title' => Yii::t('yii', 'Update'),
                        'class' => 'btn ' . $this->btnSize . ' btn-outline-secondary linkTarget',
                        'data-pjax' => '0',
                    ]);
                }
            };
        }

        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                /* return Html::a('<i class="text-danger icon-delete"></i>', $url, [
                  'title' => Yii::t('yii', 'Delete'),
                  'class' => 'btn ' . $this->btnSize . ' btn-secondary',
                  'data-confirm' => Yii::t('app', 'DELETE_ITEM'),
                  'data-method' => 'post',
                  'data-pjax' => '0',
                  ]); */
                if (isset($model->primaryKey)) {
                    if (!in_array($model->primaryKey, $model->disallow_delete)) {
                        return Html::a(Html::icon('delete'), '#', [
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'class' => 'btn ' . $this->btnSize . ' btn-outline-danger',
                            'onclick' => "
                                if (confirm('" . Yii::t('app', 'DELETE_CONFIRM') . "')) {
                                    $.ajax('$url', {
                                        type: 'POST',
                                        dataType:'json',
                                    }).done(function(data) {
                                        //$.pjax.reload({container: '" . $this->pjax . "'});
                                            console.log(data);
                                            common.notify(data.message,'success');
                                            $('#{$this->grid->id}').yiiGridView('applyFilter');
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
                        'data-confirm' => Yii::t('app', 'DELETE_ITEM'),
                        'data-method' => 'post',
                        'data-pjax' => '0',
                    ]);
                }
            };
        }
    }

    private function registerScripts()
    {
        //print_r($this->grid->getView());die;
        $this->grid->view->registerJs("alert('dsa')", View::POS_END, 'my-options');
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
