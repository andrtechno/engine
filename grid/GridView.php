<?php

namespace panix\engine\grid;

use Yii;
use yii\grid\DataColumn;
use panix\mod\admin\models\GridColumns;
use panix\engine\Html;

class GridView extends \yii\grid\GridView {

    public $layoutOptions = [];
    public $emptyTextOptions = ['class' => 'alert alert-info empty'];
    public $enableLayout = true;
    public $enableColumns = true;
    public function init() {
        if (isset($this->dataProvider->query)) {
            $modelClass = $this->dataProvider->query->modelClass;

            if ($this->enableColumns && method_exists($modelClass, 'getGridColumns')) {
                $runModel = new $modelClass;
                $model = GridColumns::find()->where([
                            'modelClass' => DIRECTORY_SEPARATOR.$modelClass
                        ])->orderBy(['ordern'=>SORT_ASC])->all();

                $colms = array();
                if (isset($model)) {
                    foreach ($model as $k => $col) {
                        $colms[$col->column_key] = $col->column_key;
                    }
                }
                $this->columns = $runModel->getColumnSearch($colms);
            }
        }


        parent::init();
        if ($this->enableLayout) {
            $this->layout = $this->render('@admin/views/layouts/_grid_layout', $this->layoutOptions);
        }
    }

}
