<?php

namespace panix\engine\traits\query;


use yii\db\Expression;

trait DefaultQueryTrait
{


    /**
     * Default scope
     */
    public function init()
    {
        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if ($modelClass::getDb()->getSchema()->getTableSchema($tableName)->getColumn('ordern')) {
            $this->addOrderBy(["{$tableName}.ordern" => SORT_DESC]);
        }
        parent::init();
    }

    /**
     * @param string $start
     * @param string $end
     * @param string $attribute
     * @return $this
     */
    public function between($start, $end, $attribute = 'created_at')
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        //$this->andWhere(['between', $tableName . '.' . $attribute, new Expression( 'UNIX_TIMESTAMP('.$start.')'), new Expression('UNIX_TIMESTAMP('.$end.')')]);
        $this->andWhere(['between', $tableName . '.' . $attribute, $start, $end]);
        return $this;
    }


    /**
     * @param integer $start
     * @param integer $end
     * @param string $attribute
     * @return $this
     */
    public function int2between($start, $end, $attribute = 'created_at')
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->andWhere(['<=', $tableName . '.' . $attribute, $start]);
        $this->andWhere(['>=', $tableName . '.' . $attribute, $end]);
        return $this;
    }

    /**
     * @param string $attribute
     * @return $this
     */
    public function isNotEmpty($attribute)
    {
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        $this->andWhere(['IS NOT', $tableName . '.' . $attribute, null]);
        $this->andWhere(['!=', $tableName . '.' . $attribute, '']);
        return $this;
    }
    /**
     * @param int $state
     * @return $this
     */
    public function published($state = 1)
    {
        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if ($modelClass::getDb()->getSchema()->getTableSchema($tableName)->getColumn('switch')) {
            $this->andWhere(["{$tableName}.switch" => $state]);
        }
        return $this;
    }


    /**
     * @param int $sort SORT_DESC or SORT_ASC
     * @return $this
     */
    public function sort($sort = SORT_DESC)
    {
        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $this->modelClass;
        $tableName = $modelClass::tableName();
        if ($modelClass::getDb()->getSchema()->getTableSchema($tableName)->getColumn('ordern')) {
            $this->addOrderBy(["{$tableName}.ordern" => $sort]);
        }
        return $this;
    }

}
