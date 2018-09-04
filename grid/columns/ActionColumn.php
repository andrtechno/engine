<?php

namespace panix\engine\grid\columns;

use Yii;
use Closure;
use panix\engine\Html;
use yii\helpers\Url;
use panix\engine\bootstrap\ButtonDropdown;

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
                //'containerOptions' => ['class' => '', 'id' => 'grid-settings'],
                //'options' => ['class' => 'btn-sm btn-secondary'],
                'dropdown' => [
                    'options' => ['class' => 'dropdown-menu-right'],
                    'encodeLabels' => false,
                    'items' => [
                        [
                            'label' => Html::icon('table') . ' Изменить столбцы таблицы',
                            'url' => 'javascript:void(0)',
                            'options' => [
                                'class' => 'editgrid',
                                'data-grid-id' => $this->grid->id,
                                'data-model' => $this->grid->dataProvider->query->modelClass,
                                'data-pjax-id' => 'pjax-' . strtolower(basename($this->grid->dataProvider->query->modelClass)),
                            ]
                        ],
                        /* [
                          'label' => Html::icon('refresh') . ' Сбросить',
                          'url' => 'javascript:void(0)',
                          'options' => [
                          'class' => '',
                          'onClick'=>'$.pjax.reload("#pjax-'. strtolower(basename($this->grid->dataProvider->query->modelClass)).'", {timeout : false});',
                          ]
                          ], */
                    ],
                ],
            ]);
        }
        $this->filterOptions = ['class' => 'text-center'];
        $this->initDefaultButtons();
        parent::init();
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['switch'])) {
            $this->buttons['switch'] = function ($url, $model, $key) {

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

                        $switch_data = $model->switch?0:1;
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
            };
        }
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                return Html::a('<i class="icon-view"></i>', $url, [
                    'title' => Yii::t('yii', 'View'),
                    'class' => 'btn ' . $this->btnSize . ' btn-outline-secondary linkTarget',
                    'data-pjax' => '0',
                ]);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                if (!in_array($model->primaryKey, $model->disallow_update)) {
                    return Html::a('<i class="icon-edit"></i>', $url, [
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
                if (!in_array($model->primaryKey, $model->disallow_delete)) {
                    return Html::a('<i class="icon-delete"></i>', '#', [
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
