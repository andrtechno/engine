<?php
namespace panix\engine\grid;

use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class GridColumns
 *
 * @property string $grid_id
 * @property string $modelClass
 * @property integer|null $pageSize
 * @property array|string $column_data
 *
 * @package panix\engine\grid
 */
class GridColumns extends ActiveRecord {

    public static function tableName() {
        return '{{%grid_columns}}';
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->column_data = Json::decode($this->column_data);
    }
}
