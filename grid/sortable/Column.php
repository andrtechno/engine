<?php

namespace panix\engine\grid\sortable;

use Yii;
use panix\engine\Html;
use yii\web\View;
use yii\helpers\Url;

class Column extends \yii\grid\Column
{

    public $headerOptions = ['style' => 'width: 30px;'];
    public $url = ['sortable'];

    public function init()
    {

        $this->url = Url::toRoute($this->url);

        $id = $this->grid->getId();
        SortableAsset::register($this->grid->view);

        $this->grid->view->registerJs("
            $('#{$id} tbody').sortable({
                connectWith: '.sortable-clipboard-area',
                axis: 'y',
                placeholder: 'sortable-column-placeholder',
                handle: '.sortable-column-handler',
                helper: function (event, ui) {
                    ui.children().each(function () {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                update: function (event, ui) {
                    var ids = [];
                    $('#{$id} tbody tr').each(function (i) {
                        ids[i] = $(this).data('key');
                    });
                    $.ajax({
                        url: '{$this->url}',
                        type: 'POST',
                        data: ({'ids': ids}),
                        dataType:'json',
                        success: function (data) {
                            if(data.status){
                                common.notify(data.message, 'success');
                            }else{
                                common.notify(data.message, 'error');
                            }
                        }
                    });

                }
            });", View::POS_READY, 'grid-sortable' . $id);
    }

    protected function renderDataCellContent($model, $key, $index)
    {
        return Html::tag('i', '', ['class' => 'icon-sort sortable-column-handler', 'style' => 'cursor: move;']);
    }

    protected function renderHeaderCellContent()
    {
        return Html::tag('i', '', ['class' => 'icon-sort']);
    }

}
