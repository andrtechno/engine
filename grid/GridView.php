<?php

namespace panix\engine\grid;

use panix\engine\CMS;
use Yii;
use yii\helpers\Json;

/**
 * Class GridView
 * @package panix\engine\grid
 */
class GridView extends \yii\grid\GridView
{

    public $layoutOptions = [];
    public $emptyTextOptions = ['class' => 'alert alert-info empty'];
    public $enableLayout = true;
    public $enableColumns = true;
    public $layoutPath = '@theme/views/layouts/_grid_layout';
    public $filterErrorOptions = ['class' => 'badge badge-danger'];
    public $pager = ['class' => 'panix\engine\widgets\LinkPager'];

    public function init()
    {
//echo Json::decode('{"image":{"checked":"1","ordern":""},"name":{"checked":"1","ordern":""},"price":{"checked":"1","ordern":""},"categories":{"checked":"1","ordern":""},"commentsCount":{"ordern":""},"created_at":{"ordern":""},"updated_at":{"ordern":""},"razmer":{"ordern":""}');die;
        $pagination = $this->dataProvider->getPagination();
        if (isset($this->dataProvider->query)) {

            $modelClass = $this->dataProvider->query->modelClass;
            if(!$this->id)
                $this->setId('grid-' . strtolower((new \ReflectionClass($modelClass))->getShortName()));
            
            if ($this->enableColumns && method_exists($modelClass, 'getGridColumns')) {
                $runModel = new $modelClass;
                /** @var GridColumns $model */
                $model = GridColumns::findOne(['grid_id' => $this->id]);
                if (isset($model->pageSize)) {
                    $pagination->pageSize = $model->pageSize;
                }
                $columns = [];
                if (isset($model)) {
                   // print_r($model->column_data);die;
                    foreach ($model->column_data as $column => $column_data) {
                        $order = (isset($column_data['ordern']) && $column_data['ordern']) ? $column_data['ordern'] : $column;
                        if(isset($column_data['checked'])){
                            $columns[$order] = $column;
                        }

                    }
                    ksort($columns);
                }

                if (!$this->columns)
                    $this->columns = $runModel->getColumnSearch($columns);
            }
        }
        parent::init();
        if (file_exists(Yii::getAlias($this->layoutPath) . '.' . Yii::$app->view->defaultExtension)) {
            if ($this->enableLayout) {
                $this->layout = $this->render($this->layoutPath, $this->layoutOptions);
            }
        }
    }

}
