<?php

/**
 * @link https://github.com/wbraganca/yii2-nested-set-behavior
 * @copyright Copyright (c) 2014 Wanderson Bragança
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

namespace panix\engine\behaviors\nestedsets;

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
     * @return array
     */
    public function dataTree($root = 0, $level = null)
    {
        $data = array_values($this->prepareData2($root, $level));
        return $this->makeData2($data);
    }

    /**
     * @param int $root
     * @param null $level
     * @return array
     */
    private function prepareData2($root = 0, $level = null)
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
                foreach ($root->children()->all() as $childRoot) {
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
                foreach ($root->children()->all() as $childRoot) {
                    $aux = $this->prepareData2($childRoot, null);
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
                foreach ($this->roots()->all() as $rootItem) {
                    if ($level) {
                        $res += $this->prepareData2($rootItem, $level - 1);
                    } elseif (is_null($level)) {
                        $res += $this->prepareData2($rootItem, null);
                    }
                }
            } else {

                $modelClass = $this->owner->modelClass;
                $model = new $modelClass;
                $root = $modelClass::find()
                    ->andWhere([$model->idAttribute => $root])
                    ->one();
                //if ($root) {
                //    $res += $this->prepareData2Fancytree($root, $level);
                //}

                //New by panix
                foreach ($root->children()->all() as $rootItem) {
                    if ($level) {
                        $res += $this->prepareData2($rootItem, $level - 1);
                    } elseif (is_null($level)) {
                        $res += $this->prepareData2($rootItem, null);
                    }
                }
                unset($model);
            }
        }
        return $res;
    }

    /**
     * @param $data
     * @return array
     */
    private function makeData2(&$data)
    {
        $tree = [];
        foreach ($data as $key => &$item) {
            if (isset($item['children'])) {
                $item['children'] = array_values($item['children']);
                $tree[$key] = $this->makeData2($item['children']);
            }
            $tree[$key] = $item;
        }
        return $tree;
    }

}
