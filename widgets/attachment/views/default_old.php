<div class="form-group">
    <div class="col-sm-4">
        <?= Html::label(Yii::t('AttachmentWidget.default', 'SELECT_IMAGES'), 'files', array('class' => 'control-label')); ?>
    </div>
    <div class="col-sm-8">


        <?php

        $this->widget('ext.multifile.MultiFileUpload', array(
            'name' => 'AttachmentsImages',
            'model' => $this->model,
            'attribute' => 'files',
            'htmlOptions' => array('multiple' => true),
            'accept' => implode('|', $this->behavior->_attachment_uploaded->extension),
            'options' => array(
                'STRING' => array(
                    'remove' => '<i class="icon-delete"></i>',
                )
            )
        ));
        ?>


    </div>

    <?php
// Images
    if ($this->getRelation()) {
        ?>
        <div class="col-sm-4"></div>
        <div class="col-sm-8">
            <div class="attachments">
                <?php
                foreach ($this->getRelation() as $image) {
                    ?>
                    <div class="attachment-item thumbnail" id="AttachmentsImages<?= $image->id ?>">
                     

                            <?= Html::link(Html::image($image->getOriginalImageUrl('name', 'attachments'), '',array()), $image->getOriginalImageUrl('name', 'attachments'), array('class' => '')); ?>


                            <div class="caption">

                                <p><span href="#" class="label label-default" title="<?= CMS::date($image->date_create, true, true) ?>"><?= CMS::date($image->date_create, true, true) ?></span></p>
                                <p><?= ($image->user) ? Html::encode($image->user->login) : '' ?></p>


                                <div class="btn-group btn-group-sm">
                                    <?= Html::link(Html::icon('icon-resize'), $image->getOriginalImageUrl('name', 'attachments'), array('class' => 'btn btn-default attachment-zoom', 'data-fancybox' => 'gallery')); ?>
                                    <?php
                                    echo Html::ajaxLink(Html::icon('icon-delete'), Yii::app()->controller->createUrl('/admin/admin/ajax/attachment.delete', array('id' => $image->id, 'model' => get_class($image))), array(
                                        'type' => 'POST',
                                        'dataType'=>'json',
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
                            </div>
                      
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

