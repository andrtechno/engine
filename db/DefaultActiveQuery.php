<?php

namespace panix\engine\db;

class DefaultActiveQuery extends \yii\db\ActiveQuery {

    public function init() {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->addOrderBy([$tableName . '.ordern' => SORT_DESC]);
        parent::init();
    }

}
