<?php

namespace panix\engine\traits;

use Yii;

trait DefaultQueryTrait
{


    public function published($state = 1)
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if (Yii::$app->getDb()->getSchema()->getTableSchema($tableName)->getColumn('switch')) {
            $this->andWhere(["{$tableName}.switch" => $state]);

        }
        return $this;
    }


    public function sort($sort = SORT_DESC)
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if (Yii::$app->getDb()->getSchema()->getTableSchema($tableName)->getColumn('ordern')) {
            $this->addOrderBy(["{$tableName}.ordern" => $sort]);

        }
        return $this;
    }

}
