<?php

namespace panix\engine\behaviors;

use panix\engine\CMS;
use panix\engine\Html;
use panix\ext\fancybox\Fancybox;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * Class UploadFileBehavior
 * @package panix\engine\behaviors
 * @property $files array [attribute => path]
 * @property $oldUploadFiles array
 * @property $extensions array
 * @property $options array
 */
class UploadFileBehavior extends Behavior
{

    public $files = [];
    public $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    public $options = [];
    private $oldUploadFiles = [];
    private $_files = [];
    private $hash;


    public function events()
    {
        return [
            // ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'afterSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function afterDelete()
    {
        foreach ($this->files as $attribute => $dir) {
            $attribute = $this->owner->{$attribute};
            if (isset($attribute) && !empty($attribute)) {
                $path = Yii::getAlias($dir) . DIRECTORY_SEPARATOR . $attribute;
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
    }


    public function afterSave()
    {
        foreach ($this->files as $attribute => $dir) {
            $this->owner->{$attribute} = $this->uploadFile($attribute, $dir, (isset($this->oldUploadFiles[$attribute])) ? $this->oldUploadFiles[$attribute] : null);
        }
    }

    public function beforeValidate()
    {
        /** @var \panix\engine\db\ActiveRecord $owner */
        $owner = $this->owner;

        if ($owner->{$this->attribute} instanceof UploadedFile) {
            $this->file = $this->owner->{$this->attribute};
            return;
        }
        $this->file = UploadedFile::getInstance($owner, $this->attribute);
        if (empty($this->file)) {
            $this->file = UploadedFile::getInstanceByName($this->attribute);
        }
        if ($this->file instanceof UploadedFile) {
            $owner->{$this->attribute} = $this->file;
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

    /**
     * @param $attribute
     * @param bool $size
     * @param array $options
     * @param bool $returnBool
     * @return bool|string
     */
    public function getImageUrl($attribute, $size = false, $options = [], $returnBool = false)
    {

        $options = ArrayHelper::merge($this->options, $options);


        $owner = $this->owner;
        //if ($owner->{$attribute}) {

        return CMS::processImage($size, $owner->{$attribute}, $this->files[$attribute], $options);
        // } else {
        //     if (!$returnBool) {
        //         return false;
        //     }
        //    return CMS::placeholderUrl(['size' => $size]);
        //   }
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
        $file = $this->getFileAbsolutePath($attr);

        if (file_exists($file)) {
            //$exif = exif_read_data($file, 0, true);
            //if (isset($exif['FILE']['FileDateTime'])) {
            //    $this->hash = $exif['FILE']['FileDateTime'];
            //}
            return true;
        }
        return false;
    }

    public function getRemoveUrl($attribute)
    {
        if ($this->checkExistFile($attribute)) {

            $owner = $this->owner;
            $params = [];
            $params[] = 'delete-file';

            if ($owner->getUpdateUrl())
                $params['redirect'] = Url::to($owner->getUpdateUrl());

            //$params['attribute'] = $attribute;
            $params['key'] = $owner->getPrimaryKey();
            $params['attribute'] = $attribute;

            return Html::a(Html::icon('delete') . ' ' . Yii::t('app/default', 'DELETE'), $params, ['class' => 'btn btn-sm btn-outline-danger', 'data-pjax' => 0]);
        }
    }

    public function getImageBase64($attr)
    {
        $owner = $this->owner;
        if (isset($owner->{$attr})) {
            if ($this->checkExistFile($attr)) {
                $path = $this->getFileAbsolutePath($attr);
                $fileInfo = $this->getFileInfo($attr);
                if (in_array($fileInfo['extension'], ['jpg', 'jpeg', 'gif', 'png'])) {
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
            $linkValue = Html::icon('search') . ' Открыть файл';
            if (in_array($fileInfo['extension'], ['jpg', 'jpeg', 'gif', 'png', 'pdf', 'svg'])) {
                $fancybox = true;
            }
            $targetClass = '';
            if ($fancybox) {
                $targetClass = 'fancybox-popup-' . $attribute;
                echo Fancybox::widget(['target' => '.' . $targetClass]);
            }

            return Html::a($linkValue . ' ' . $fileInfo['extension'], $this->getFileUrl($attribute) . '?hash=' . $this->hash, ['class' => 'btn btn-sm btn-outline-primary ' . $targetClass]) . $this->getRemoveUrl($attribute);
        }
        return null;
    }


    public function getFileInfo($attribute)
    {
        if ($this->checkExistFile($attribute)) {
            return pathinfo($this->getFileAbsolutePath($attribute));
        }
        return false;
    }

    public function getFilePath($attribute)
    {
        return str_replace('@', '/', $this->files[$attribute]) . "/" . $this->owner->{$attribute};
    }

    public function getFileAbsolutePath($attribute)
    {
        $owner = $this->owner;
        if ($owner->{$attribute}) {
            return Yii::getAlias($this->files[$attribute]) . DIRECTORY_SEPARATOR . $owner->{$attribute};
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
    public function uploadFile2($attribute, $dir, $old_image = null)
    {
        $owner = $this->owner;
        $path = Yii::getAlias($dir) . DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
            FileHelper::createDirectory($path, $mode = 0775, $recursive = true);
        }
        /** @var $img \panix\engine\components\ImageHandler */

        if (isset($file)) {
            if ($old_image && file_exists($path . $old_image))
                unlink($path . $old_image);


            $fileInfo = pathinfo($file->name);


            $newFileName = CMS::slug($fileInfo['filename']) . '.' . $file->extension;
            if (file_exists($path . $newFileName)) {
                $newFileName = CMS::slug($fileInfo['filename']) . '-' . CMS::gen(10) . '.' . $file->extension;
            }
            if (in_array($file->extension, $this->extensions)) { //Загрузка для изображений
                $img = Yii::$app->img->load($file->tempName);

                if ($img->getHeight() > Yii::$app->params['maxUploadImageSize']['height'] || $img->getWidth() > Yii::$app->params['maxUploadImageSize']['width']) {
                    $img->resize(Yii::$app->params['maxUploadImageSize']['width'], Yii::$app->params['maxUploadImageSize']['height']);
                }
                if ($img->save($path . $newFileName)) {
                    unlink($file->tempName);
                }
            } else {
                $file->saveAs($path . $newFileName);
            }
            $owner->{$attribute} = (string)$newFileName;
        } else {
            $owner->{$attribute} = (string)$old_image;
        }
        return $owner->{$attribute};
    }


    public function uploadFile($attribute, $dir, $old_image = null)
    {
        $owner = $this->owner;
        $file = UploadedFile::getInstance($owner, $attribute);

        $path = Yii::getAlias($dir) . DIRECTORY_SEPARATOR;
        if (!file_exists($path)) {
            FileHelper::createDirectory($path, $mode = 0775, $recursive = true);
        }
        /** @var $img \panix\engine\components\ImageHandler */

        $assetPath = basename($path);

        if (isset($file)) {
            if ($old_image && file_exists($path . $old_image))
                unlink($path . $old_image);

            $files = glob(Yii::getAlias("@app/web/assets/{$assetPath}/*/{$old_image}"));
            foreach ($files as $fileItem) {
                if (is_file($fileItem)) {
                    unlink($fileItem);
                }
            }
            $files = glob(Yii::getAlias("@app/web/assets/${assetPath}/{$old_image}"));
            foreach ($files as $fileItem) {
                if (is_file($fileItem)) {
                    unlink($fileItem);
                }
            }

            $fileInfo = pathinfo($file->name);


            $newFileName = CMS::slug($fileInfo['filename']) . '.' . $file->extension;
            if (file_exists($path . $newFileName)) {
                $newFileName = CMS::slug($fileInfo['filename']) . '-' . CMS::gen(10) . '.' . $file->extension;
            }

            if (in_array($file->extension, $this->extensions)) { //Загрузка для изображений

                $img = Yii::$app->img->load($file->tempName);

                if ($img->getHeight() > Yii::$app->params['maxUploadImageSize']['height'] || $img->getWidth() > Yii::$app->params['maxUploadImageSize']['width']) {
                    $img->resize(Yii::$app->params['maxUploadImageSize']['width'], Yii::$app->params['maxUploadImageSize']['height']);
                }

                $img->save($path . $newFileName);
                //if ($img->save($path . $newFileName)) {
                    //unlink($file->tempName);
                //}

            } else {
                $file->saveAs($path . $newFileName);
            }
            $owner->{$attribute} = (string)$newFileName;
        } else {
            $owner->{$attribute} = (string)$old_image;
        }
        return (!empty($owner->{$attribute})) ? $owner->{$attribute} : NULL;
    }

}
