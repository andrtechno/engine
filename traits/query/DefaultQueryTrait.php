<?php

namespace panix\engine\traits\query;

use Yii;

/**
 * Trait DefaultQueryTrait
 * @package panix\engine\traits\query
 */
trait DefaultQueryTrait
{

    /**
     * @param int $state
     * @return $this
     */
    public function published($state = 1)
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if (Yii::$app->getDb()->getSchema()->getTableSchema($tableName)->getColumn('switch')) {
            $this->andWhere(["{$tableName}.switch" => $state]);

        }
        return $this;
    }


    /**
     * @param int $sort
     * @return $this
     */
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
