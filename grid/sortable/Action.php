<?php

namespace panix\engine\grid\sortable;

use Yii;

class Action extends \yii\rest\Action {

    public $column = 'ordern';

    public function run() {
        if (isset($_POST['ids']) && is_array($_POST['ids'])) {
            $model = new $this->modelClass;
            if ($this->modelClass === null)
                throw new Exception('Не указана таблица');

            $max = (int) Yii::$app->db->createCommand("SELECT MAX({$this->column}) FROM " . $model::tableName() . " WHERE id IN(" . implode(', ', $_POST['ids']) . ")")->queryScalar();

            if (!is_numeric($max) || $max == 0)
                $this->prepareTable();


            $this->savePositions($_POST['ids'], $max);
        }
    }

    public function savePositions($ids, $start) {
        $model = new $this->modelClass;
        $priorities = array();
        foreach ($ids as $id)
            $priorities[$id] = $start--;

        Yii::$app->db->createCommand("UPDATE " . $model::tableName() . " SET {$this->column} = " . $this->_generateCase($priorities) . " WHERE id IN(" . implode(', ', $ids) . ")")->execute();
    }

    /**
     * Prepare table
     */
    public function prepareTable() {
        $model = new $this->modelClass;
        Yii::$app->db->createCommand("UPDATE " . $model::tableName() . " SET {$this->column} = id")->execute();
    }

    private function _generateCase($priorities) {
        $result = 'CASE `id`';
        foreach ($priorities as $k => $v)
            $result .= ' when "' . $k . '" then "' . $v . '"';
        return $result . ' END';
    }

}
