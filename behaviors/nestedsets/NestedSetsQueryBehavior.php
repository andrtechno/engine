<?php

namespace panix\engine\behaviors\nestedsets;

use panix\engine\CMS;
use yii\base\Behavior;
use yii\helpers\Url;

/**
 * @author Wanderson Bragança <wanderson.wbc@gmail.com>
 */
class NestedSetsQueryBehavior extends Behavior
{

    /**
     * @var \yii\db\ActiveQuery the owner of this behavior.
     */
    public $owner;

    /**
     * Gets root node(s).
     * @return \yii\db\ActiveRecord the owner.
     */
    public function roots()
    {
        /** @var $modelClass \yii\db\ActiveRecord */
        $modelClass = $this->owner->modelClass;
        $model = new $modelClass;
        $this->owner->andWhere($modelClass::getDb()->quoteColumnName($model->leftAttribute) . '=1');
        unset($model);
        return $this->owner;
    }

    /**
     * @param int $root
     * @param null $level
     * @return array
     */
    public function options($root = 0, $level = null)
    {
        $res = [];

        if (is_object($root)) {
            $res[$root->{$root->idAttribute}] = str_repeat('—', $root->{$root->levelAttribute} - 1)
                . ((($root->{$root->levelAttribute}) > 1) ? '›' : '')
                . $root->{$root->titleAttribute};

            if ($level) {
                foreach ($root->children()->all() as $childRoot) {
                    $res += $this->options($childRoot, $level - 1);
                }
            } elseif (is_null($level)) {
                foreach ($root->children()->all() as $childRoot) {
                    $res += $this->options($childRoot, null);
                }
            }
        } elseif (is_scalar($root)) {
            if ($root == 0) {
                foreach ($this->roots()->all() as $rootItem) {
                    if ($level) {
                        $res += $this->options($rootItem, $level - 1);
                    } elseif (is_null($level)) {
                        $res += $this->options($rootItem, null);
                    }
                }
            } else {
                $modelClass = $this->owner->modelClass;
                $model = new $modelClass;
                $root = $modelClass::find()->andWhere([$model->idAttribute => $root])->one();
                if ($root) {
                    $res += $this->options($root, $level);
                }
                unset($model);
            }
        }
        return $res;
    }

    /**
     * @param int $root
     * @param null $level
     * @param array $wheres
     * @return array
     */
    public function dataTree($root = 0, $level = null, $wheres = null, $key = false)
    {
        $data = array_values($this->prepareData2($root, $level, $wheres));
        return $this->makeData2($data, $key);
    }

    /**
     * @param int $root
     * @param null $level
     * @param array $wheres
     * @return array
     */
    private function prepareData2($root = 0, $level = null, $wheres = null)
    {
        $res = [];
        if (is_object($root)) {
            $res[$root->{$root->idAttribute}]['key'] = $root->{$root->idAttribute};
            $res[$root->{$root->idAttribute}]['title'] = $root->{$root->titleAttribute};
            if (method_exists($root, 'getUrl'))
                $res[$root->{$root->idAttribute}]['url'] = Url::to($root->getUrl());
            if (isset($root->switch))
                $res[$root->{$root->idAttribute}]['switch'] = $root->switch;

            if ($level) {
                /** @var NestedSetsBehavior $root */
                $query = $root->children();
                if ($wheres) {
                    if (is_array($wheres)) {
                        $query->andWhere($wheres);
                    }
                }

                $result = $query->all();
                foreach ($result as $childRoot) {
                    $aux = $this->prepareData2($childRoot, $level - 1);

                    if (isset($res[$root->{$root->idAttribute}]['children']) && !empty($aux)) {
                        $res[$root->{$root->idAttribute}]['folder'] = true;
                        $res[$root->{$root->idAttribute}]['children'] += $aux;
                    } elseif (!empty($aux)) {
                        $res[$root->{$root->idAttribute}]['folder'] = true;
                        $res[$root->{$root->idAttribute}]['children'] = $aux;
                    }
                }
            } elseif (is_null($level)) {
                /** @var NestedSetsBehavior $root */

                $query = $root->children();
                if ($wheres) {
                    if (is_array($wheres)) {
                        $query->andWhere($wheres);
                    }
                }
                $result = $query->all();
                foreach ($result as $childRoot) {
                    $aux = $this->prepareData2($childRoot, null, $wheres);
                    if (isset($res[$root->{$root->idAttribute}]['children']) && !empty($aux)) {
                        $res[$root->{$root->idAttribute}]['folder'] = true;
                        $res[$root->{$root->idAttribute}]['children'] += $aux;

                    } elseif (!empty($aux)) {
                        $res[$root->{$root->idAttribute}]['folder'] = true;
                        $res[$root->{$root->idAttribute}]['children'] = $aux;
                    }
                }
            }
        } elseif (is_scalar($root)) {

            if ($root == 0) {
                $query = $this->roots();
                if ($wheres) {
                    if (is_array($wheres)) {
                        $query->andWhere($wheres);
                    }
                }

                $result = $query->all();
                foreach ($result as $rootItem) {
                    if ($level) {
                        $res += $this->prepareData2($rootItem, $level - 1, $wheres);
                    } elseif (is_null($level)) {
                        $res += $this->prepareData2($rootItem, null, $wheres);
                    }
                }
            } else {

                $modelClass = $this->owner->modelClass;
                $model = new $modelClass;
                $root = $modelClass::find()
                    ->andWhere([$model->idAttribute => $root])
                    ->one();
                /** @var NestedSetsBehavior $root */
                //New by panix

                $query = $root->children();
                if ($wheres) {
                    if (is_array($wheres)) {
                        $query->andWhere($wheres);
                    }
                }
                $result = $query->all();


                foreach ($result as $rootItem) {
                    if ($level) {
                        $res += $this->prepareData2($rootItem, $level - 1, $wheres);
                    } elseif (is_null($level)) {
                        $res += $this->prepareData2($rootItem, null, $wheres);
                    }
                }
                unset($model);
            }
        }
        return $res;
    }

    /**
     * @param array $data
     * @param boolean $key
     * @return array
     */
    public function makeData2(&$data, $keyPk = false)
    {
        $tree = [];
        foreach ($data as $key => &$item) {
            $keyItem = ($keyPk) ? $item['key'] : $key;
            if (isset($item['children'])) {
                $item['children'] = ($keyPk) ? $item['children'] : array_values($item['children']);
                $tree[$keyItem] = $this->makeData2($item['children'], $keyPk);
            }

            $tree[$keyItem] = $item;
        }
        return $tree;
    }

}
