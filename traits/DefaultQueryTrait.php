<?php

namespace panix\engine\traits;
use Yii;
trait DefaultQueryTrait {

    public function init() {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if (Yii::$app->getDb()->getSchema()->getTableSchema($tableName)->getColumn('ordern')) {
            $this->addOrderBy(["{$tableName}.ordern" => SORT_DESC]);
        }
        parent::init();
    }

    public function published($state = 1) {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if (Yii::$app->getDb()->getSchema()->getTableSchema($tableName)->getColumn('switch')) {
            return $this->andWhere(["{$tableName}.switch" => $state]);
        }
    }

}
