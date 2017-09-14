<?php

namespace panix\engine\db;

use Yii;
use panix\engine\grid\sortable\SortableGridBehavior;
use yii\base\Exception;
use yii\helpers\Json;
class ActiveRecord extends \yii\db\ActiveRecord {
    

    public function save($runValidation = true, $attributeNames = null) {
        if (parent::save($runValidation, $attributeNames)) {
            //if ($mSuccess) {
                $message = Yii::t('app', ($this->isNewRecord) ? 'SUCCESS_CREATE' : 'SUCCESS_UPDATE');


                if (Yii::$app->request->isAjax) {
                    //    if(Yii::app()->user->getIsEditMode2()){


                    header('Content-Type: application/json; charset="UTF-8"');
                    echo Json::encode(array(
                        'success' => true,
                        'message' => $message,
                        'valid' => true,
                        // 'is' => Yii::app()->controller->isAdminController,
                        // 'em' => Yii::app()->user->isEditMode,
                        'data' => $this->attributes
                    ));
                    die;
                    //}
                } else {
                   //  if(method_exists(Yii::app()->controller,'setNotify')){
                    //Yii::app()->controller->setNotify($message, 'success');
                    // }
                }
            //}
            return true;
        } else {
           // if ($mError && method_exists(Yii::app()->controller,'setNotify')) {
            //    Yii::app()->controller->setNotify(Yii::t('app', ($this->isNewRecord) ? 'ERROR_CREATE' : 'ERROR_UPDATE'), 'danger');
            //}
            return false;
        }
    }
    //  protected $_attrLabels = array();
    const route_update = 'update';
    const route_delete = 'delete';
    const route_switch = 'switch';
    const route_create = 'create';
    const route = null;
    const MODULE_ID = null;

    public function beforeSave($insert) {

        //if (parent::beforeSave($insert)) {
        //create
        if ($this->isNewRecord) {
            if (isset($this->tableSchema->columns['ip_create'])) {
                //Текущий IP адресс, автора добавление
                $this->ip_create = Yii::$app->request->getUserIP();
            }
            if (isset($this->tableSchema->columns['user_id'])) {
                $this->user_id = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;
            }
            if (isset($this->tableSchema->columns['user_agent'])) {
                $this->user_agent = Yii::$app->request->userAgent;
            }
            if (isset($this->tableSchema->columns['date_create'])) {
                // $this->date_create = date('Y-m-d H:i:s');
            }
            if (isset($this->tableSchema->columns['ordern'])) {
                if (!isset($this->ordern)) {
                    $row = static::find()->select('max(ordern) as maxOrdern')->asArray()->one();
                    $this->ordern = $row['maxOrdern'] + 1;
                }
            }
            //update
        } else {
            if (isset($this->tableSchema->columns['date_update'])) {
                //  echo $this->date_update;
                // die('s');
                //   $this->date_update = date('Y-m-d H:i:s');
            }
        }
        return parent::beforeSave($insert);
    }

    public function afterSave222($insert, $changedAttributes) {
        if (isset($this->tableSchema->columns['date_update'])) {
            //echo $this->date_update;
            //die('s');
            //   $this->date_update = date('Y-m-d H:i:s');
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function attributeLabels() {
        $lang = Yii::$app->language;
        $attrLabels = [];
        foreach ($this->behaviors() as $key => $b) {
            if (isset($b['translationAttributes'])) {
                foreach ($b['translationAttributes'] as $attr) {
                    $attrLabels[$attr] = self::t(strtoupper($attr));
                }
            }
        }
        foreach ($this->attributes as $attr => $val) {
            $attrLabels[$attr] = self::t(strtoupper($attr));
        }
        return $attrLabels;
    }

    public function behaviors() {
        $b = [];
        if (isset($this->tableSchema->columns['ordern'])) {
            $b['dnd_sort'] = [
                'class' => SortableGridBehavior::className(),
                'sortableAttribute' => 'ordern'
            ];
        }
        return $b;
    }

    public static function t($message, $params = array()) {
        $fileName = (new \ReflectionClass(get_called_class()))->getShortName();
        return Yii::t(strtolower(static::MODULE_ID) . '/' . $fileName, $message, $params);
    }

    public function getNextOrPrev($nextOrPrev, $cid = false, $options = array()) {
        $records = NULL;

        if ($nextOrPrev == "prev")
            $order = "id ASC";
        if ($nextOrPrev == "next")
            $order = "id DESC";

        if (!isset($options['select']))
            $options['select'] = [$this::tableName() . '.*'];

        if ($cid) {//TODO: no work need job.
            $options['params'] = array(':cid' => $cid);
        }

        //$modelParams
        $records = $this::find()
                ->select($options['select'])
                ->where(['switch' => 1])

                // ->joinWith('category2')
                ->orderBy($order)
                ->all();

        foreach ($records as $i => $r)
            if ($r->id == $this->id)
                return (isset($records[$i + 1])) ? $records[$i + 1] : NULL;

        return NULL;
    }

    /**
     * Special for widget ext.admin.frontControl
     * 
     * @return string
     */
    public function getCreateUrl() {
        if (static::route) {
            return Yii::$app->urlManager->createUrl(static::route . '/' . static::route_create);
        } else {
            throw new Exception(Yii::t('app', 'NOTFOUND_CONST_AR', [
                'param' => 'route_create',
            ]));
        }
    }
    public function isString($attribute) {
        if (Yii::$app->user->can('admin')) {
            $html = '<form action="' . $this->getUpdateUrl() . '" method="POST">';
            $html .= '<span id="' . basename(get_class($this)) . '[' . $attribute . ']" class="edit_mode_title">' . $this->$attribute . '</span>';
            $html .= '</form>';
            return $html;
        } else {
            return $this->$attribute;
        }
    }

    public function isText($attribute) {
        if (Yii::$app->user->can('admin')) {
            $html = '<form action="' . $this->getUpdateUrl() . '" method="POST">';
            $html .= '<div id="' . basename(get_class($this)) . '[' . $attribute . ']" class="edit_mode_text">' . $this->$attribute . '</div>';
            $html .= '</form>';
            return $html;
        } else {
            return Html::text($this->$attribute);
        }
    }
    /**
     * Special for widget ext.admin.frontControl
     * @return string
     */
    public function getDeleteUrl() {
        if (static::route) {
            return Yii::$app->urlManager->createUrl([static::route . '/' . static::route_delete,
                        'model' => get_class($this),
                        'id' => $this->id
            ]);
        } else {
            throw new Exception(Yii::t('app', 'NOTFOUND_CONST_AR', array(
                'param' => 'route_delete',
                'model' => get_class($this)
            )));
        }
    }

    /**
     * Special for widget ext.admin.frontControl
     * @return string
     */
    public function getUpdateUrl() {
        if (static::route) {
            return Yii::$app->urlManager->createUrl([static::route . '/' . static::route_update,
                        'id' => $this->id
            ]);
        } else {
            throw new Exception(Yii::t('app', 'NOTFOUND_CONST_AR', array(
                'param}' => 'route_update',
                'model}' => get_class($this)
            )));
        }
    }

    /**
     * Special for widget ext.admin.frontControl
     * @return string
     */
    public function getSwitchUrl() {
        if (static::route) {
            return Yii::$app->urlManager->createUrl(static::route . '/' . static::route_switch, array(
                        'model' => get_class($this),
                        'switch' => 0,
                        'id' => $this->id
            ));
        } else {
            throw new Exception(Yii::t('app', 'NOTFOUND_CONST_AR', array(
                'param' => 'route_switch',
                'model' => get_class($this)
            )));
        }
    }

}
