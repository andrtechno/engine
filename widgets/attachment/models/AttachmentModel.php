<?php

class AttachmentModel extends ActiveRecord
{
/*
    public function getAttachmentImageUrl($dir, $size = false, $resize = 'resize')
    {
        $redir = (strpos($dir, '.')) ? str_replace('.', '/', $dir) : $dir;
        $attrname = $this->name;
        if (!empty($attrname)) {
            if ($size !== false) {
                return CMS::resizeImage($size, $attrname, 'attachments.' . $dir, 'attachments/' . $redir, $resize);
            }
        } else {
            return false;
        }
    }
*/

    /**
     * @param string|bool $size
     * @param array $options
     * @return bool|string
     */
    public function getImageUrl($size = false, $options = array())
    {
        //$redir = (strpos($dir, '.')) ? str_replace('.', '/', $dir) : $dir;
        $attrname = $this->name;
        $params = array();
        $params['id'] = $this->id;
        if (!empty($attrname)) {
            if ($size) {
                $params['size'] = $size;
                //return CMS::assetImage($size, $attrname, 'attachments.' . $dir, 'attachments/' . $redir, $options);
            }
            return Yii::app()->createUrl('/core/attachment', $params);
        } else {
            return false;
        }
    }


    public function getImageUrl2($size = '50x50', $options = array())
    {
        //$redir = (strpos($dir, '.')) ? str_replace('.', '/', $dir) : $dir;
        $attrname = $this->name;
        $params = array();
        $params['id'] = $this->id;
        if (!empty($attrname)) {
            if ($size !== false) {
                $params['size'] = $size;
                //if (isset($options['mod'])) {
                //    $params['mod'] = $options['mod'];
                //}
                return Yii::app()->createUrl('/core/attachment', $params);

                //return CMS::assetImage($size, $attrname, 'attachments.' . $dir, 'attachments/' . $redir, $options);
            }
        } else {
            return false;
        }
    }

    public function getOriginalUrl($dir, $absolute = false)
    {
        if (!$absolute) {
            $redir = (strpos($dir, '.')) ? str_replace('.', '/', $dir) : $dir;
            return "/uploads/attachments/{$redir}/" . $this->name;
        } else {
            return Yii::getPathOfAlias("webroot.uploads.attachments.{$dir}") . DS . $this->name;
        }
    }

    /**
     * Returns the static model of the specified AR class.
     * @return AttachmentModel the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{attachments}}';
    }

    public function attributeLabels()
    {
        return array(
            'object_id' => Yii::t('AttachmentWidget.default', 'OBJECT_ID'),
            'model' => Yii::t('AttachmentWidget.default', 'MODEL'),
            'name' => Yii::t('AttachmentWidget.default', 'FILENAME'),
            'is_main' => Yii::t('AttachmentWidget.default', 'IS_MAIN'),
            'user_id' => Yii::t('AttachmentWidget.default', 'USER_ID'),
            'date_create' => Yii::t('AttachmentWidget.default', 'DATE_CREATE'),
            'alt_title' => Yii::t('AttachmentWidget.default', 'ALT_TAG'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $a = array();
        $a['timezone'] = array(
            'class' => 'app.behaviors.TimezoneBehavior',
            'attributes' => array('date_create'),
        );
        return CMap::mergeArray($a,parent::behaviors());
    }

    public function defaultScope()
    {
        return array(
            'order' => 'ordern DESC',
        );
    }
    public function beforeSave()
    {
       // if (parent::beforeSave()) {
            //create
            if ($this->isNewRecord) {
                if (isset($this->tableSchema->columns['user_id'])) {
                    $this->user_id = (Yii::app()->user->isGuest) ? NULL : Yii::app()->user->id;
                }
                if (isset($this->tableSchema->columns['date_create'])) {
                    $this->date_create = date('Y-m-d H:i:s');
                }

                if (isset($this->tableSchema->columns['ordern'])) {
                    if (!isset($this->ordern)) {
                        $row = $this->model()->find(array(
                            'select' => 'max(ordern) AS maxOrdern',
                            'condition'=>'model=:model AND object_id=:object_id',
                            'params'=>array(
                                //':model'=>'mod.shop.models.ShopProduct',
                                ':model'=>$this->model,
                                ':object_id'=>$this->object_id
                            )
                        ));
                        $this->ordern = $row['maxOrdern'] + 1;
                    }
                }
            }
            return true;
        //} else {
       //     return false;
       // }
    }
}
