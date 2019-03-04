<?php
namespace panix\engine\grid;

use yii\db\ActiveRecord;


class GridColumns extends ActiveRecord {

    public static function tableName() {
        return '{{%grid_columns}}';
    }

}
