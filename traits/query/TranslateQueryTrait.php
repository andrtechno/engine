<?php

namespace panix\engine\traits\query;

/**
 * Trait TranslateQueryTrait
 * @package panix\engine\traits\query
 */
trait TranslateQueryTrait
{


    /**
     * @param int $id
     * @return $this /yii/db/Query
     */
    public function translate($id = 1)
    {

        if ($id) {
            $this->joinWith(['translations' => function ($query) use ($id) {

                $model = (new $this->modelClass)->translationClass;
                $translateClass = new $model;

                $query->andWhere([$translateClass::tableName() . '.language_id' => $id]);
            }]);
        }

        return $this;
    }

}
