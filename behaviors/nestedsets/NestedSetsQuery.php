<?php

namespace panix\engine\behaviors\nestedsets;

use panix\engine\behaviors\nestedsets\NestedSetsQueryBehavior;

class NestedSetsQuery extends \yii\db\ActiveQuery {

    public function behaviors() {
        return [
            [
                'class' => NestedSetsQueryBehavior::className(),
            ]
        ];
    }

}
