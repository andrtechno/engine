<?php

namespace panix\engine\grid;

use Yii;
use yii\grid\DataColumn;
use panix\mod\admin\models\GridColumns;
use panix\engine\Html;
class GridView extends \yii\grid\GridView {

    public $layoutOptions = [];
    public $emptyTextOptions = ['class' => 'alert alert-info empty'];

    public function init() {

        $modelClass = $this->dataProvider->query->modelClass;

        if (method_exists($modelClass, 'getGridColumns')) {
            $runModel = new $modelClass;
            $model = GridColumns::find()->where([
                        'modelClass' => $modelClass
                    ])->orderBy('ordern ASC')->all();

            $colms = array();
            if (isset($model)) {
                foreach ($model as $k => $col) {
                    $colms[$col->key] = $col->key;
                }
            }
            $this->columns = $runModel->getColumnSearch($colms);
        }



        parent::init();

        $this->layout = $this->render('@admin/views/layouts/_grid_layout', $this->layoutOptions);
    }

}
