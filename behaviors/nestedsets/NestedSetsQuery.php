<?php

namespace panix\engine\behaviors\nestedsets;

use yii\db\ActiveQuery;

class NestedSetsQuery extends ActiveQuery {

    public function behaviors() {
        return [
            [
                'class' => NestedSetsQueryBehavior::class,
            ]
        ];
    }

}
