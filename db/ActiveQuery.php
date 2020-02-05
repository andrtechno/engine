<?php

namespace panix\engine\db;

use Yii;
use yii\db\ActiveQuery as BaseActiveQuery;

class ActiveQuery extends BaseActiveQuery
{

    public function init2()
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->addOrderBy([$tableName . '.ordern' => SORT_DESC]);
        parent::init();
    }


}
