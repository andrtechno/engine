<?php

namespace panix\engine\behaviors;

use panix\engine\CMS;
use panix\engine\Html;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @version 1.0
 * @author Andrew S. <andrew.panix@gmail.com>
 * @name $attributes array attributes model
 */
class UploadFileBehavior extends Behavior
{


    public $files = [];
    public $extensions = ['jpg', 'jpeg', 'png', 'gif'];
    private $oldUploadFiles = [];

    public function events()
    {
        return [
            // ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'afterSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
        ];
    }


    public function afterSave()
    {
        foreach ($this->files as $attribute => $dir) {
            if (isset($this->owner->{$attribute})) {
                $this->owner->{$attribute} = $this->uploadFile($attribute, $dir, (isset($this->oldUploadFiles[$attribute])) ? $this->oldUploadFiles[$attribute] : null);
            }
        }
    }

    public function beforeValidate()
    {

        foreach ($this->files as $attribute => $dir) {


        }
        if ($this->owner->{$this->attribute} instanceof UploadedFile) {
            $this->file = $this->owner->{$this->attribute};
            return;
        }
        $this->file = UploadedFile::getInstance($this->owner, $this->attribute);
        if (empty($this->file)) {
            $this->file = UploadedFile::getInstanceByName($this->attribute);
        }
        if ($this->file instanceof UploadedFile) {
            $this->owner->{$this->attribute} = $this->file;
        }
    }

    public function afterFind()
    {
        $owner = $this->owner;
        foreach ($this->files as $attribute => $dir) {
            if (isset($owner->{$attribute})) {
                $this->oldUploadFiles[$attribute] = $owner->{$attribute};
            }
        }

    }

    public function getImageUrl($attribute, $size = false, $options = array())
    {
        $owner = $this->owner;
        if (!empty($owner->{$attribute})) {
            return CMS::processImage($size, $owner->{$attribute}, $this->files[$attribute], $options);
        } else {
            return $imgSource = CMS::placeholderUrl(['size' => $size]);
        }
    }


    public function getFileUrl($attr, $absolute = false)
    {
        $owner = $this->owner;
        if (isset($owner->{$attr})) {
            if ($this->checkExistFile($attr)) {
                return ($absolute) ? $this->getFileAbsolutePath($attr) : $this->getFilePath($attr);
            }
        }
        return false;
    }

    private function checkExistFile($attr)
    {
        if (file_exists($this->getFileAbsolutePath($attr))) {
            return true;
        }
        return false;
    }

    public function getRemoveUrl($attribute)
    {
        if ($this->checkExistFile($attribute)) {

            $owner = $this->owner;
            $params = array();
            $params[] = 'removeFile';

            if ($owner->getUpdateUrl())
                $params['redirect'] = Yii::app()->createAbsoluteUrl($owner->getUpdateUrl());

            $params['attribute'] = $attribute;
            $params['key'] = $owner->getPrimaryKey();

            return Html::a(Html::icon('icon-delete') . ' ' . Yii::t('app', 'DELETE'), $params, array('class' => 'btn btn-sm btn-danger'));
        }
    }

    public function getImageBase64($attr)
    {
        $owner = $this->getOwner();
        if (isset($owner->{$attr})) {
            if ($this->checkExistFile($attr)) {
                $path = $this->getFileAbsolutePath($attr);
                $fileInfo = $this->getFileInfo($attr);
                if (in_array($fileInfo['extension'], array('jpg', 'jpeg', 'gif', 'png'))) {
                    $data = file_get_contents($path);
                    return 'data:image/' . $fileInfo['extension'] . ';base64,' . base64_encode($data);
                }
            }
        }
        return false;
    }

    public function getFileHtmlButton($attribute)
    {
        if ($this->checkExistFile($attribute)) {
            $fileInfo = $this->getFileInfo($attribute);
            $fancybox = false;
            $linkValue = Html::icon('icon-search') . ' Открыть файл';
            if (in_array($fileInfo['extension'], array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'svg'))) {
                $fancybox = true;
            }
            if ($fancybox) {
                $targetClass = 'fancybox-popup-' . $attribute;
                Yii::$app->controller->widget('ext.fancybox.Fancybox', array('target' => '.' . $targetClass));
            }

            return Html::a($linkValue, $this->getFileUrl($attribute), array('class' => 'btn btn-sm btn-primary ' . $targetClass)) . $this->getRemoveUrl($attribute);
        }
    }


    protected function getFileInfo($attribute)
    {
        if ($this->checkExistFile($attribute)) {
            return pathinfo($this->getFileAbsolutePath($attribute));
        }
        return false;
    }

    private function getFilePath($attr)
    {
        $replace = str_replace('.', '/', $this->dir);
        return "/uploads/{$replace}/" . $this->getOwner()->{$attr};
    }

    private function getFileAbsolutePath($attribute)
    {
        $owner = $this->getOwner();
        //$replace = str_replace('.', '/', $this->dir);
        if ($owner->{$attribute}) {
            return Yii::getAlias("webroot.uploads.{$this->dir}") . DS . $owner->{$attribute};
        } else {
            return false;
        }
    }

    /**
     * @param $attribute
     * @param $dir
     * @param null $old_image
     * @return mixed
     */
    public function uploadFile($attribute, $dir, $old_image = null)
    {
        $owner = $this->owner;
        $file = UploadedFile::getInstance($owner, $attribute);
        $path = Yii::getAlias($dir) . DIRECTORY_SEPARATOR;

        if (isset($file)) {
            if ($old_image && file_exists($path . $old_image))
                unlink($path . $old_image);

            $newFileName = CMS::gen(10) . "." . $file->extension;
            if (in_array($file->extension, $this->extensions)) { //Загрузка для изображений
                $img = Yii::$app->img;
                $img->load($file->tempName);
                $img->save($path . $newFileName);
            } else {
                $file->saveAs($path . $newFileName);
            }
            $owner->{$attribute} = (string)$newFileName;
        } else {
            $owner->{$attribute} = (string)$old_image;
        }
        return $owner->{$attribute};
    }

}
