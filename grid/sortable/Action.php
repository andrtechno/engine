<?php

namespace panix\engine\grid\sortable;

use Yii;
use yii\base\Exception;
use yii\web\Response;

class Action extends \yii\rest\Action
{

    public $column = 'ordern';
    public $successMessage;

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $result['status'] = false;
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            if (isset($_POST['ids']) && is_array($_POST['ids'])) {
                /** @var \yii\db\ActiveRecord $model */
                $model = new $this->modelClass;
                if ($this->modelClass === null)
                    $result['message'] = 'modelClass not found';

                $max = (int)$model::getDb()->createCommand("SELECT MAX({$this->column}) FROM " . $model::tableName() . " WHERE id IN(" . implode(', ', $_POST['ids']) . ")")->queryScalar();

                if (!is_numeric($max) || $max == 0)
                    $this->prepareTable();


                $this->savePositions($_POST['ids'], $max);
                $result['message'] = ($this->successMessage) ? $this->successMessage : Yii::t('app/admin', 'SORT_SUCCESS_MESSAGE');
                $result['status'] = true;
            }
        } else {
            $result['message'] = 'Request support only AJAX and POST query';
        }
        return $result;
    }

    public function savePositions($ids, $start)
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = new $this->modelClass;
        $priorities = array();
        foreach ($ids as $id)
            $priorities[$id] = $start--;

        $model::getDb()->createCommand("UPDATE " . $model::tableName() . " SET {$this->column} = " . $this->_generateCase($priorities) . " WHERE id IN(" . implode(', ', $ids) . ")")->execute();
    }

    /**
     * Prepare table
     */
    public function prepareTable()
    {
        /** @var \yii\db\ActiveRecord $model */
        $model = new $this->modelClass;
        $model::getDb()->createCommand("UPDATE " . $model::tableName() . " SET {$this->column} = id")->execute();
    }

    private function _generateCase($priorities)
    {
        $result = 'CASE `id`';
        foreach ($priorities as $k => $v)
            $result .= ' when "' . $k . '" then "' . $v . '"';
        return $result . ' END';
    }

}
