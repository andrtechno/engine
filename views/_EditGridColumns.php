<?php
use panix\engine\Html;

echo Html::beginForm('', 'post', ['id' => 'edit_grid_columns_form']);
echo Html::hiddenInput('grid_id', $grid_id);
echo Html::hiddenInput('model', $modelClass);

?>
    <div class="form-group row">
        <div class="col-sm-4 col-lg-4">
            <?= Html::label('Количество записей на странице', 'pageSize', ['class' => 'col-form-label']); ?>
        </div>
        <div class="col-sm-8 col-lg-8">
            <?= Html::textInput('pageSize', $pageSize, ['class' => 'form-control', 'id' => 'pageSize']); ?>
        </div>
    </div>
<?php


echo \panix\engine\grid\GridView::widget([
    'tableOptions' => ['class' => 'table table-striped table-bordered'],
    'dataProvider' => $provider,
    'enableLayout' => false,
    'layout' => "{pager}{items}",
    'columns' => [
        [
            'attribute' => 'checkbox',
            'format' => 'raw',
            'header' => null,
            'contentOptions' => ['class' => 'text-center', 'style' => 'width:60px'],
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

