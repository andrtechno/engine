<?php

namespace panix\engine\grid\sortable;

use panix\engine\grid\sortable\assets\SortableAsset;
use panix\engine\Html;
use yii\web\View;
use yii\helpers\Url;

class Column extends \yii\grid\Column {
    public $headerOptions = ['style' => 'width: 30px;'];
    public $url = null;

    public function init() {
        // if ($this->url == null)
        //    $this->url = '/' . preg_replace('#' . Yii::app()->controller->action->id . '$#', 'sortable', Yii::app()->controller->route);


        $this->url = Url::toRoute($this->url);

        $id = $this->grid->getId();
        SortableAsset::register($this->grid->view);

        $this->grid->view->registerJs("
            $('#{$id} tbody').sortable({
                connectWith: '.sortable-clipboard-area',
                axis: 'y',
                handle: '.sortable-column-handler',
                helper: function (event, ui) {
                    ui.children().each(function () {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                update: function (event, ui) {
                    var ids = [];
                    $('#{$id} .sortable-column').each(function (i) {
                        ids[i] = $(this).data('key');
                    });
                    $.ajax({
                        url: '{$this->url}',
                        type: 'POST',
                        data: ({'ids': ids}),
                        success: function () {
                            common.notify('Success!', 'success');
                        }
                    });

                }
            });", View::POS_READY, 'grid-sortable');
    }

    protected function renderDataCellContent($model, $key, $index) {
        return Html::tag('i', '', array('class' => 'icon-sort sortable-column-handler', 'style' => 'cursor: move;'));
    }

    protected function renderHeaderCellContent() {
        return Html::tag('i', '', array('class' => 'icon-sort'));
    }


}
