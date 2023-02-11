<?php

use panix\engine\Html;

echo Html::beginForm('', 'post', ['id' => 'edit_grid_columns_form']);
echo Html::hiddenInput('grid_id', $grid_id);
echo Html::hiddenInput('model', $modelClass);

?>
    <div class="form-group2 mb-4 field-pageSize">
        <?= Html::label('Количество записей на странице', 'pageSize', ['id' => 'pageSize-label', 'class' => 'col-form-label']); ?>
        <?= Html::textInput('pageSize', $pageSize, ['class' => 'form-control', 'id' => 'pageSize', 'placeholder' => 'Укажите число']); ?>
        <div id="pageSize-error" class="invalid-feedback"></div>
    </div>
<?php


echo \panix\engine\grid\GridView::widget([
    'tableOptions' => ['class' => 'table table-striped table-bordered'],
    'dataProvider' => $provider,
    'enableLayout' => false,
    'options' => ['class' => 'grid-view table-responsive'],
    'layout' => "{pager}{items}",
    'columns' => [
        [
            'attribute' => 'checkbox',
            'format' => 'raw',
            'header' => '-',
            'contentOptions' => ['class' => 'text-center', 'style' => 'width:50px'],
        ],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'header' => 'Название поля',
            'headerOptions' => ['class' => 'text-left'],
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

