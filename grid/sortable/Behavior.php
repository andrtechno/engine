<?php

namespace panix\engine\grid\sortable;

use Yii;
use yii\db\ActiveRecord;

class Behavior extends \yii\base\Behavior
{

    public $column = 'ordern';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',

        ];
    }

    public function beforeSave()
    {
        /** @var ActiveRecord $model */
        $model = $this->owner;
        $column = $this->column;
        if ($model->isNewRecord)
            $model->$column = Yii::$app->db->createCommand("SELECT MAX({$this->column}) FROM " . $model->tableName())->queryScalar() + 1;
    }

}
