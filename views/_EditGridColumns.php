<?php
use panix\engine\Html;

echo Html::beginForm('', 'post', array('id' => 'edit_grid_columns_form'));
echo Html::hiddenInput('grid_id', $grid_id);
echo Html::hiddenInput('module', $module);
echo Html::hiddenInput('model', $modelClass);


echo \panix\engine\grid\GridView::widget([
    'tableOptions' => ['class' => 'table table-striped table-bordered'],
    'dataProvider' => $provider,
    'enableLayout' => false,
    'layout' => "{pager}{items}",
    'columns' => [
        [
            'attribute' => 'checkbox',
            'format' => 'raw',
            'header' => '',
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'header' => 'Название поля',
            'contentOptions' => ['class' => 'text-left'],
        ],
        [
            'attribute' => 'sort',
            'format' => 'raw',
            'header' => 'Сортировка',
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:60px'],
        ],
    ],
    'showFooter' => true,
    //   'footerRowOptions' => ['class' => 'text-center'],
    'rowOptions' => ['class' => 'sortable-column']
]);

echo Html::endForm();
?>
