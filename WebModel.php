<?php

namespace panix\engine;

use Yii;
use yii\db\ActiveRecord;
use panix\engine\grid\sortable\SortableGridBehavior;

class WebModel extends ActiveRecord {
    /*     * public function behaviors() {
      if (isset($this->tableSchema->columns['ordern'])) {
      return [
      'sort' => [
      'class' => \panix\engine\grid\sortable\SortableGridBehavior::className(),
      'sortableAttribute' => 'ordern'
      ],
      ];
      }
      } */

    protected $_attrLabels = array();

    const MODULE_ID = null;

    public function beforeSave($insert) {
        //if (parent::beforeSave($insert)) {
        //create
        if ($this->isNewRecord) {
            if (isset($this->tableSchema->columns['ip_create'])) {
                //Текущий IP адресс, автора добавление
                $this->ip_create = Yii::$app->request->userHostAddress;
            }
            if (isset($this->tableSchema->columns['user_id'])) {
                $this->user_id = (Yii::$app->user->isGuest) ? 0 : Yii::$app->user->id;
            }
            if (isset($this->tableSchema->columns['user_agent'])) {
                $this->user_agent = Yii::$app->request->userAgent;
            }
            if (isset($this->tableSchema->columns['date_create'])) {
                $this->date_create = date('Y-m-d H:i:s');
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
                $this->date_update = date('Y-m-d H:i:s');
            }
        }
        return parent::beforeSave($insert);
        //    return true;
        // } else {
        //     return false;
        // }
    }

    public function attributeLabels() {
        $lang = Yii::$app->languageManager->active->code;
        $model = get_class($this);
        $module_id = static::MODULE_ID;
        $filePath = Yii::getAlias("panix/{$module_id}/messages/{$lang}") . DS . $model . '.php';
         foreach ($this->behaviors() as $key => $b) {
          if (isset($b['translationAttributes'])) {
          foreach ($b['translationAttributes'] as $attr) {
          $this->_attrLabels[$attr] = self::t(strtoupper($attr));
          }
          }
          } 
        foreach ($this->attributes as $attr => $val) {
            $this->_attrLabels[$attr] = self::t(strtoupper($attr));
        }
        //if (!file_exists($filePath)) {
        //    Yii::app()->user->setFlash('warning', 'Модель "' . $model . '", не может найти файл переводов: <b>' . $filePath . '</b> ');
        //}
        return $this->_attrLabels;
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
        return Yii::t(strtolower(static::MODULE_ID) . '/' . basename(get_called_class()), $message, $params);
    }

}

?>
