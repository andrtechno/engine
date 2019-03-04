<?php
use panix\engine\Html;

echo Html::beginForm('','post',array('id'=>'edit_grid_columns_form'));
echo Html::hiddenInput('grid_id', $grid_id);
echo Html::hiddenInput('module', $module);
echo Html::hiddenInput('model', $modelClass);



echo \panix\engine\grid\GridView::widget([
   // 'id'=>'grid-product',
    'tableOptions' => ['class' => 'table table-striped'],
    'dataProvider' => $provider,
   // 'filterModel' => $searchModel,

    'columns' => [
        [
            'attribute' => 'checkbox',
            'format' => 'raw',
            'header' => '',
           // 'class' => 'IdColumn',
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'header' => 'Название поля',
            'options' => ['class' => 'text-left'],
        ],
        [
            'attribute' => 'sort',
            'format' => 'raw',
            'header' => 'Сортировка',
            'options' => ['class' => 'text-left','style'=>'width:60px'],
        ],
    ],
    'showFooter' => true,
    //   'footerRowOptions' => ['class' => 'text-center'],
    'rowOptions' => ['class' => 'sortable-column']
]);
/*
$this->widget('ext.adminList.GridView', array(
    'dataProvider' => $provider,
    'id'=>'edit_grid_columns_grid',
    
    'itemsCssClass' => 'table table-striped table-bordered',
   // 'summaryText'=>'Показано {start}-{end} ({count})',
    'selectableRows' => false,
    'headerOptions'=>false,
    'enableHeader'=>false,
    'enablePagination' => false,
    'columns' => array(
        array(
            'name' => 'checkbox',
            'type' => 'raw',
            'header' => '',
            'class' => 'IdColumn',
        ),
        array(
            'name' => 'name',
            'type' => 'raw',
            'header' => 'Название поля',
            'htmlOptions' => array('class' => 'text-left'),
        ),
        array(
            'name' => 'sort',
            'type' => 'raw',
            'header' => 'Сортировка',
            'htmlOptions' => array('class' => 'text-left','style'=>'width:60px'),
        ),
    )
        )
);*/
echo Html::endForm();
?>
