<?php
$params = $this->behavior->attachmentAttributes;
$uploaded = $this->behavior->_attachment_uploaded;
$alias = $params['path'];

$multiple = isset($params['multiple']) ? $params['multiple'] : true;
$max = isset($params['max']) ? $params['max'] : -1;
$this->widget('ext.fancybox.Fancybox', array(
    'target' => 'a[data-fancybox=gallery]',
    'config' => array(
        'padding' => 0,
        'transitionIn' => 'none',
        'transitionOut' => 'none',
        'titlePosition' => 'over',
    )
));
?>
    <div class="form-group row">
        <div class="col-sm-4">
            <?= Html::label(Yii::t('AttachmentWidget.default', 'SELECT_FILE', $max), 'attachments_files', array('class' => 'control-label')); ?>
        </div>
        <div class="col-sm-8">


            <?php

            $this->widget('ext.multifile.MultiFileUpload', array(
                'name' => 'AttachmentsImages',
                'model' => $this->model,
                'attribute' => 'attachments_files',
                'max' => $max,
                'htmlOptions' => array('multiple' => $multiple, 'maxlenght' => $max),
                'accept' => implode('|', $uploaded->extension),
                //'remove'=>'<i class="icon-delete"></i>',
                'remove'=>'<i class="icon-delete"></i> '.Yii::t('app','DELETE'),
                'options' => array(
                    'preview'=>true
                 )
            ));
            echo Html::error($this->model, 'attachments_files');
            ?>

            <div class="text-muted"><?=
                Yii::t('AttachmentWidget.default', 'HINT_MAXSIZE', array(
                    '{maxSize}' => Html::tag('span', array('class' => 'badge badge-secondary'), CMS::files_size($uploaded->maxSize), true),
                ));
                ?></div>
            <div class="text-muted"><?=
                Yii::t('AttachmentWidget.default', 'HINT_MAXFILES', array(
                    '{maxFiles}' => ($max == -1) ? Html::tag('span', array('class' => 'badge badge-success'), Yii::t('AttachmentWidget.default', 'NO_LIMIT'), true) : Html::tag('span', array('class' => 'label label-default'), $max, true)
                ));
                ?></div>
            <div class="text-muted"><?=
                Yii::t('AttachmentWidget.default', 'FILES_HINT', array(
                    '{extension}' => Html::tag('span', array('class' => 'badge badge-secondary'), implode(', ', $uploaded->extension), true),
                    '{maxSize}' => Html::tag('span', array('class' => 'badge badge-secondary'), CMS::files_size($uploaded->maxSize), true),
                    '{maxFiles}' => ($max == -1) ? Html::tag('span', array('class' => 'badge badge-success'), Yii::t('AttachmentWidget.default', 'NO_LIMIT'), true) : Html::tag('span', array('class' => 'label label-default'), $max, true)
                ));
                ?></div>

        </div>


    </div>

<?php
$columns = array();
$data = array();

$data2 = array();
foreach ($this->model->attachments as $image) {
    $data2['primaryKey'] = $image->id;
    $data2['is_main'] = $image->is_main;
    $data2['image'] = Html::link(Html::image($image->getImageUrl('100x100'), $image->alt_title, array('class' => 'img-thumbnail')), $image->getImageUrl('700x700'), array('data-fancybox' => 'gallery'));
    $data2['alt'] = Html::textField('attachment_image_titles[' . $image->id . ']', $image->alt_title, array('class' => 'form-control', 'placeholder' => $image->getAttributeLabel('alt_title')));
    $data2['detail'] = '<div>Дата: <b>' . CMS::date($image->date_create, true, true) . '</b></div>
                                <div>Разместил: <b>' . (($image->user) ? Html::encode($image->user->login) : '') . '</b>
                                </div>';
    $data2['main'] = Html::radioButton('AttachmentsMainId', $image->is_main, array(
        'value' => $image->id,
        'class' => 'check',
        'data-toggle' => "tooltip",
        'title' => Yii::t('AttachmentWidget.default', 'IS_MAIN'),
        'id' => 'main_image_' . $image->id
    ));

    $data2['options'] = Html::link(Html::icon('icon-resize'), $image->getOriginalUrl($alias), array('class' => 'btn btn-secondary attachment-zoom'));

    $data2['options'] .= Html::ajaxLink(Html::icon('icon-delete'), Yii::app()->controller->createUrl('/admin/admin/ajax/attachment.delete', array('id' => $image->id, 'model' => get_class($image), 'alias' => $alias)), array(
        'type' => 'POST',
        'dataType' => 'json',
        'data' => array(Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken),
        'success' => 'js:function(data){
            if(data.status == "success"){
                common.notify(data.message,"success");
                $(".attachment-' . $image->id . '").hide().remove();
                common.removeLoader();
            }
        }',
        'beforeSend' => 'js:function(){
            common.addLoader();
        }'
    ), array(
        'id' => 'attachments-delete-' . $image->id,
        'class' => 'btn btn-danger',
        'title' => Yii::t('app', 'DELETE'),
        'confirm' => Yii::t('AttachmentWidget.default', 'CONFIRM'),
    ));

    $data[] = (object)$data2;

}
$columns[] = array(
    'class' => 'ext.sortable.SortableColumn',
    'url' => Yii::app()->createUrl('/admin/ajax/attachmentSortable', array(
        'param' => $params['model'],
        'object_id' => $this->model->id
    ))
);

$columns[] = array(
    'header' => 'Изображение',
    'name' => 'image',
    'type' => 'raw',
    'htmlOptions' => array('class' => 'text-center'),
);
$columns[] = array(
    'header' => 'Alt-тег',
    'name' => 'alt',
    'type' => 'raw',
    'htmlOptions' => array('class' => 'text-center'),
);
$columns[] = array(
    'header' => 'Обложка',
    'name' => 'main',
    'type' => 'raw',
    'htmlOptions' => array('class' => 'text-center'),
);
$columns[] = array(
    'header' => 'Детали',
    'name' => 'detail',
    'type' => 'html',
);
$columns[] = array(
    'header' => Yii::t('app', 'OPTIONS'),
    'name' => 'options',
    'type' => 'raw',
    'htmlOptions' => array('class' => 'text-center'),
);

$data_db = new CArrayDataProvider($data, array(
        'keyField' => true,
        // 'sort' => array(
        // 'attributes' => $sortAttributes,
        //   'defaultOrder' => array('filename' => false),
        //),
        'pagination' => false,
    )
);


$this->widget('ext.adminList.GridView', array(
        'itemsCssClass' => 'table table-striped table-bordered optionsEditTable',
        'dataProvider' => $data_db,
        'selectableRows' => false,
        'enableHeader' => false,
        'autoColumns' => false,
        //'enablePagination' => true,
        'rowHtmlOptionsExpression' => function ($row, $data) {
            // if (!empty($data->status_color)) {
            //     return ':' . $data->status_color . '';
            //} else {
            return "array('das'=>'ads')";
            // }
        },

        'rowCssClassExpression' => function ($row, $data) {
            if ($data->is_main) {
                return 'bg-success text-white attachment-' . $data->primaryKey;
            } else {
                return 'attachment-' . $data->primaryKey;
            }


        },

        'columns' => $columns
    )

);
