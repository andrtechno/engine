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
    <?= Html::label(Yii::t('AttachmentWidget.default', 'SELECT_FILE', $max), 'attachments_files', array('class' => 'col-sm-4 col-form-label')); ?>
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
                ),
            )
        ));
        echo Html::error($this->model, 'attachments_files');
        ?>
        <div class="text-muted"><?= Yii::t('AttachmentWidget.default', 'FILES_HINT', array(
                '{extension}' => implode(', ', $uploaded->extension),
                '{maxSize}' => CMS::files_size($uploaded->maxSize),
                '{maxFiles}' => ($max == -1) ? Html::tag('span', array('class' => 'badge badge-success'), Yii::t('AttachmentWidget.default', 'NO_LIMIT'), true) : Html::tag('span', array('class' => 'badge badge-secondary'), $max, true)
            )); ?></div>


        <?php
        // Images

        if ($this->model->attachments) {
            ?>


            <div class="table-responsive">
                <table class="table table-striped attachments">
                    <tr>
                        <th class="text-center">
                            <?= Yii::t('AttachmentWidget.default', 'TH_FILE', ($this->type == 'image') ? 1 : 2); ?>
                        </th>
                        <th class="text-center"><?= Yii::t('AttachmentWidget.default', 'TH_DETAIL'); ?></th>
                        <?php if ($this->type == 'image') { ?>
                            <th class="text-center">Обложка</th>
                            <th class="text-center">Alt-тег</th>
                        <?php } ?>
                        <th class="text-center"><?= Yii::t('app', 'OPTIONS'); ?></th>
                    </tr>


                    <?php
                    foreach ($this->model->attachments as $image) {
                    $coverClass = ($image->is_main) ? 'bg-success' : '';
                    ?>

                    <tr class="thumbnail" id="AttachmentsImages<?= $image->id ?>">
                        <td class="text-center">
                            <?php if ($this->type == 'image') { ?>
                                <?= Html::link(Html::image($image->getImageUrl('100x'), $image->alt_title, array('width' => 100, 'class' => 'img-thumbnail')), $image->getOriginalUrl(),array('data-fancybox'=>'gallery')); ?>
                            <?php } else { ?>
                                <?= $image->name ?>
                            <?php } ?>
                        </td>
                        <td>
                            <div>Дата: <b><?= CMS::date($image->date_create, true, true) ?></b></div>
                            <div>Разместил: <b><?= ($image->user) ? Html::encode($image->user->login) : '' ?></b></div>

                        </td>
                        <?php if ($this->type == 'image') { ?>
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
                        <?php } ?>
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

                        <?php } ?>
                </table>

            </div>
        <?php } ?>
    </div>
</div>

