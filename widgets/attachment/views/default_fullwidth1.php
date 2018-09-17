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
            'options' => array(
                'STRING' => array(
                    'remove' => '<i class="icon-delete"></i>',
                )
            )
        ));
        echo Html::error($this->model, 'attachments_files');
        ?>

        <div class="text-muted hidden"><?=
            Yii::t('AttachmentWidget.default', 'HINT_MAXSIZE', array(
                '{maxSize}' => Html::tag('span', array('class' => 'badge badge-secondary'), CMS::files_size($uploaded->maxSize), true),
            ));
            ?></div>
        <div class="text-muted hidden"><?=
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

    <?php
    // Images

    if ($this->model->attachments) {
        ?>

        <div class="table-responsive">
            <table class="table table-striped attachments">
                <tr>
                    <th class="text-center">Изображение</th>
                    <th class="text-center">Детали</th>
                    <th class="text-center">Обложка</th>
                    <th class="text-center">Alt-тег</th>
                    <th class="text-center">Опции</th>
                </tr>
                <?php
                foreach ($this->model->attachments as $image) {
                    $coverClass = ($image->is_main) ? 'bg-success text-white' : '';
                    ?>


                    <tr class="thumbnail <?=$coverClass?>" id="AttachmentsImages<?= $image->id ?>">
                        <td class="text-center">
                            <?= Html::link(Html::image($image->getImageUrl($alias, '100x100'), $image->alt_title, array('class' => 'img-thumbnail')), $image->getImageUrl($alias, '700x700'), array('data-fancybox' => 'gallery')); ?>
                        </td>
                        <td>
                            <div>Дата: <b><?= CMS::date($image->date_create, true, true) ?></b></div>
                            <div>Разместил: <b><?= ($image->user) ? Html::encode($image->user->login) : '' ?></b></div>

                        </td>
                        <td class="text-center <?= $coverClass ?>1">
                            <?=
                            Html::radioButton('AttachmentsMainId', $image->is_main, array(
                                'value' => $image->id,
                                'class' => 'check',
                                'data-toggle' => "tooltip",
                                'title' => Yii::t('AttachmentWidget.default', 'IS_MAIN'),
                                'id' => 'main_image_' . $image->id
                            ));
                            ?>
                        </td>
                        <td>


                            <?= Html::textField('attachment_image_titles[' . $image->id . ']', $image->alt_title, array('class' => 'form-control', 'placeholder' => $image->getAttributeLabel('alt_title'))); ?>

                        </td>
                        <td class="text-center">

                            <div class="btn-group btn-group-sm">
                                <?= Html::link(Html::icon('icon-resize'), $image->getOriginalUrl($alias), array('class' => 'btn btn-secondary attachment-zoom')); ?>
                                <?php
                                echo Html::ajaxLink(Html::icon('icon-delete'), Yii::app()->controller->createUrl('/admin/admin/ajax/attachment.delete', array('id' => $image->id, 'model' => get_class($image), 'alias' => $alias)), array(
                                    'type' => 'POST',
                                    'dataType' => 'json',
                                    'data' => array(Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken),
                                    'success' => 'js:function(data){
                                            if(data.status == "success"){
                                                common.notify(data.message,"success");
                                                $("#AttachmentsImages' . $image->id . '").hide().remove();
                                                common.removeLoader();
                                            }
                                        }',
                                    'beforeSend' => 'js:function(){
                                            common.addLoader();
                                        }'
                                ), array(
                                    'id' => 'AttachmentsImagesDelete' . $image->id,
                                    'class' => 'btn btn-danger',
                                    'title' => Yii::t('app', 'DELETE'),
                                    'confirm' => Yii::t('AttachmentWidget.default', 'CONFIRM'),
                                ));
                                ?>

                            </div>
                        </td>


                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } ?>

</div>

