<?php

Yii::import('ext.attachment.components.AttachmentUpload');
Yii::import('ext.attachment.components.AttachmentSaver');

/**
 *
 * 'class' => 'ext.attachment.components.AttachmentBehavior',
 * 'model'=>"mod.pages.models.Page",
 * 'max_image_size'=>'800x600',
 * 'resize'=>true,
 * 'watermark'=>true,
 * 'watermark_config'=>array(
 *      'offsetX'=>10,
 *      'offsetY'=>10,
 *      'corner'=>4,
 *      'path'=>'watermark path (png)'
 * ),
 * 'config'=>array(
 *      'maxSize'=>10485760,
 *      'extension'=>array('jpg', 'jpeg', 'png', 'gif')
 * )
 *
 *
 */
class AttachmentBehavior extends CActiveRecordBehavior
{

    public $_attachment_uploaded;
    public $attachmentAttributes = array();


    public function afterDelete($event)
    {
        // $images = isset($this->attachmentAttributes['relationName']) ? $this->attachmentAttributes['relationName'] : 'attachments';


        $imagesList = $this->owner->attachments;
        //die(print_r($imagesList));
        if (!empty($imagesList)) {
            foreach ($imagesList as $image) {


                $alias = $this->attachmentAttributes['path'];


                // Delete file
                if (file_exists(Yii::getPathOfAlias("webroot.uploads.{$alias}") . '/' . $image->name))
                    unlink(Yii::getPathOfAlias("webroot.uploads.{$alias}") . '/' . $image->name);

                if (file_exists(Yii::getPathOfAlias("webroot.assets.{$alias}"))) {
                    $listfile = CFileHelper::findFiles(Yii::getPathOfAlias("webroot.assets.{$alias}"), array(
                        'absolutePaths' => true
                    ));
                    foreach ($listfile as $path) {
                        if (strpos($path, $image->name) !== false) {
                            if (file_exists($path)) {
                                unlink($path);
                            }
                        }
                    }
                }
                if ($image->is_main) {
                    // Get first image and set it as main
                    $model = AttachmentModel::model()->find();
                    if ($model) {
                        $model->is_main = 1;
                        $model->save(false, false, false);
                    }
                }

                $image->delete();
            }
        }
        return parent::afterDelete($event);
    }

    /**
     * @param CComponent $owner
     */
    public function attach($owner)
    {

        if (isset($this->attachmentAttributes['path'])) {
            $this->attachmentAttributes['path'] = $this->attachmentAttributes['path'];
        } else {
            $dir = $owner::MODULE_ID;
            $this->attachmentAttributes['path'] = isset($dir) ? "{$dir}" : "";
        }

        $pathAlias = $this->attachmentAttributes['path'];
        if (!file_exists(Yii::getPathOfAlias("webroot.uploads.{$pathAlias}")))
            CFileHelper::createDirectory(Yii::getPathOfAlias("webroot.uploads.{$pathAlias}"), 0777);

        $this->_attachment_uploaded = new AttachmentUpload($this->attachmentAttributes);
        parent::attach($owner);
    }

    public function beforeValidate($event)
    {
        $images = CUploadedFile::getInstancesByName('AttachmentsImages');
        if ($images) {
            if ($this->attachmentAttributes['max'] != -1) {
                $count = AttachmentModel::model()->countByAttributes(array(
                    'object_id' => $this->owner->id,
                    'model' => $this->attachmentAttributes['model']
                ));

                if ($count >= $this->attachmentAttributes['max']) {
                    Yii::import('ext.attachment.AttachmentWidget');
                    $this->owner->addError('attachments_files', Yii::t('AttachmentWidget.default', 'ERROR_MAX_COUNT'));
                }

            }
        }
        parent::beforeValidate($event);
    }

    public function afterSave($event)
    {
        $images = CUploadedFile::getInstancesByName('AttachmentsImages');
        if ($images && sizeof($images) > 0) {
            foreach ($images as $image) {
                if (!$this->_attachment_uploaded->hasErrors($image)) {
                    $this->upload($image);
                } else {

                    die('err upload AttachmentBehavior');
                    //$this->setFlashMessage(Yii::t('ShopModule.admin', 'ERR_LOAD_IMAGE', array('{NAME}' => $image->getName())));
                }
            }
        }
        $this->updateMainImage();
        $this->updateImageTitles();
        return parent::afterSave($event);
    }

    /**
     * @param CUploadedFile $image
     */
    public function upload(CUploadedFile $image)
    {

        $name = $this->_attachment_uploaded->createName($this->owner, $image);
        $fullPath = $this->_attachment_uploaded->getSavePath() . '/' . $name;



        if (!file_exists($this->_attachment_uploaded->getSavePath())) {
            CFileHelper::createDirectory($this->_attachment_uploaded->getSavePath());
        }


        $image->saveAs($fullPath);
        @chmod($fullPath, 0666);
        // Check if product has main image
        $is_main = (int)AttachmentModel::model()->countByAttributes(array(
            'object_id' => $this->owner->id,
            'model' => $this->attachmentAttributes['model'],
            'is_main' => 1
        ));

        $imageModel = new AttachmentModel;
        $imageModel->object_id = $this->owner->id;
        $imageModel->model = $this->attachmentAttributes['model'];
        $imageModel->name = $name;
        $imageModel->dir = $this->attachmentAttributes['path'];
        //$imageModel->user_id = Yii::app()->user->id;
        $imageModel->is_main = ($is_main == 0) ? true : false;
        $imageModel->save(false, false);

$condigApp = Yii::app()->settings->get('app');
        //if (isset($this->attachmentAttributes['resize']) ? $this->attachmentAttributes['resize'] : true) {
        //    $this->resize($fullPath);
        //}

        //if (isset($this->attachmentAttributes['watermark']) ? $this->attachmentAttributes['watermark'] : false) {
        //    $this->watermark($fullPath);
        //}
    }

    protected function resize($fullPath)
    {
        $maxsize = isset($this->attachmentAttributes['max_image_size']) ? $this->attachmentAttributes['max_image_size'] : '1280x960';
        $sizes = explode('x', $maxsize);
        Yii::app()->img
            ->load($fullPath)
            ->thumb($sizes[0], $sizes[1])
            ->save();
    }

    /**
     * $corner  угол
     *
     * 1 Левый верхний
     * 2 Правый верхний
     * 3 Левый нижний
     * 4 Правый нижний
     * 5 Центр изображения
     * @param string $fullPath
     */
    protected function watermark($fullPath)
    {
        $offsetX = isset($this->attachmentAttributes['watermark_offsetX']) ? $this->attachmentAttributes['watermark_offsetX'] : 10;
        $offsetY = isset($this->attachmentAttributes['watermark_offsetY']) ? $this->attachmentAttributes['watermark_offsetY'] : 10;
        $corner = isset($this->attachmentAttributes['watermark_corner']) ? $this->attachmentAttributes['watermark_corner'] : 4;
        $path = isset($this->attachmentAttributes['watermark_path']) ? $this->attachmentAttributes['watermark_path'] : Yii::getPathOfAlias('webroot.uploads') . '/watermark.png';
        Yii::app()->img
            ->load($fullPath)
            ->watermark($path, $offsetX, $offsetY, $corner, false)
            ->save();
    }

    protected function updateMainImage()
    {
        if (Yii::app()->request->getPost('AttachmentsMainId')) {
            // Clear current main image
            AttachmentModel::model()->updateAll(array('is_main' => 0), 'object_id=:pid', array(':pid' => $this->owner->id));
            // Set new main image
            AttachmentModel::model()->updateByPk(Yii::app()->request->getPost('AttachmentsMainId'), array('is_main' => 1));
        }
    }

    protected function updateImageTitles()
    {
        if (sizeof(Yii::app()->request->getPost('attachment_image_titles', array()))) {
            foreach (Yii::app()->request->getPost('attachment_image_titles', array()) as $id => $title) {
                AttachmentModel::model()->updateByPk($id, array(
                    'alt_title' => $title
                ));
            }
        }
    }

}
