<?php

namespace panix\engine\widgets\like\models;

use yii\db\ActiveQuery;
use panix\engine\traits\query\DefaultQueryTrait;

class LikeQuery extends ActiveQuery {

    use DefaultQueryTrait;

    public function orderByName($sort = SORT_ASC) {
        return $this->joinWith('translations')
                        ->addOrderBy(['{{%order__delivery_translate}}.name' => $sort]);
    }

}
