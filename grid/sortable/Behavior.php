<?php
namespace panix\engine\grid\sortable;
use Yii;
use yii\db\ActiveRecord;
class Behavior extends \yii\base\Behavior {
    
    public $column = 'ordern';
    
    public function events() {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',

        ];
    }


    public function beforeFind2($event) {
        $criteria = $this->owner->getDbCriteria();
        $alias = $this->owner->getTableAlias(true);

        if (!$criteria->order)
            $criteria->order = $alias . ".`{$this->column}` DESC";
        //    parent::beforeFind($event);
    }

    public function beforeSave($event) {

        $model = $this->owner;
        $column = $this->column;
        if ($model->isNewRecord)
            $model->$column = Yii::$app->db->createCommand("SELECT MAX({$this->column}) FROM " . $model->tableName())->queryScalar() + 1;
        // parent::beforeSave($event);
    }


}
