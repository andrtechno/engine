<?php

namespace panix\engine\controllers;

use panix\engine\grid\GridColumns;
use panix\engine\Html;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Exception;
use yii\web\ForbiddenHttpException;
//use yii2mod\rbac\filters\AccessControl;
use yii\filters\AccessControl;
use yii\web\UnauthorizedHttpException;

class AdminController extends WebController
{

    public $buttons = [];
    public $layout = '@vendor/panix/mod-admin/views/layouts/main';
    public $dashboard = true;

    public function behaviors2()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                // 'allowActions' => [
                //     'index',
                // The actions listed here will be allowed to everyone including guests.
                // ]
            ],
        ];
    }

    public function beforeAction($event)
    {
        if (Yii::$app->user->isGuest && get_class($this) !== 'panix\mod\admin\controllers\AuthController') {
            Yii::$app->response->redirect(['/admin/auth']);
        }

        return parent::beforeAction($event);
    }

    public function init()
    {
        // Yii::$app->assetManager->bundles['yii\jui\JuiAsset']['css'] = [];
        if (!empty(Yii::$app->user) && !Yii::$app->user->can("admin") && get_class($this) !== 'panix\mod\admin\controllers\AuthController' && get_class($this) !== 'panix\mod\admin\controllers\DefaultController') {
            throw new ForbiddenHttpException(Yii::t('app', 'ACCESS_DENIED'));
        }

        //Yii::setAlias('@admin', Yii::getAlias('@vendor/panix/mod-admin'));

        parent::init();
    }


    public function actionCreate()
    {
        return $this->actionUpdate(true);
    }

    public function actionEditColumns()
    {
        if (Yii::$app->request->isAjax) {
            //Yii::app()->clientScript->registerCoreScript('jquery.ui');
            // Yii::$app->clientScript->scriptMap = array(
            //     'jquery.js' => false,
            //     'jquery.ba-bbq.js' => false,
            // );
            $modelClass = str_replace('/', '\\', Yii::$app->request->post('model'));

            $grid_id = Yii::$app->request->post('grid_id');
            $mod = Yii::$app->request->post('module');
            $getGrid = Yii::$app->request->post('GridColumns');
            $upMod = ucfirst($mod);
            //Yii::import("mod.{$mod}.models.{$modelClass}");
            // Yii::import("mod.{$mod}.{$upMod}Module");
            if ($getGrid) {
                GridColumns::deleteAll(['grid_id' => $grid_id]);
                if ($getGrid['check']) {

                    foreach ($getGrid['check'] as $key => $post) {
                        $model = new GridColumns();
                        $model->grid_id = $grid_id;
                        $model->modelClass = $modelClass;
                        $model->ordern = $getGrid['ordern'][$key];
                        $model->column_key = $key;

                        //try {
                            $model->save(false);
                       // } catch (Exception $e) {
                            //error
                       // }
                    }
                }
            }

            $data = array();
            /* $cr = new CDbCriteria;
             $cr->order = '`t`.`ordern` DESC';
             $cr->condition = '`t`.`grid_id`=:grid';
             $cr->params = array(
                 ':grid' => $grid_id,
             );
             $model = GridColumns::model()->findAll($cr);*/

            $model = GridColumns::find()
                ->where(['grid_id' => $grid_id])
                ->orderBy(['ordern' => SORT_DESC])
                ->all();


            $m = array();
            foreach ($model as $r) {
                $m[$r->column_key]['ordern'] = $r->ordern;
                $m[$r->column_key]['key'] = $r->column_key;
            }
            $mClass = (new $modelClass());
            $columsArray = $mClass->getGridColumns();

            unset($columsArray['DEFAULT_COLUMNS'], $columsArray['DEFAULT_CONTROL']);
            if (isset($columsArray)) {
                foreach ($columsArray as $key => $column) {

                    // print_r($column);die;
                    if (isset($column['header'])) {
                        $name = $column['header'];
                    } else {
                        $name = $mClass->getAttributeLabel((isset($column['attribute'])) ? $column['attribute'] : $key);
                    }
                    if (isset($m[$key])) {
                        $isChecked = ($m[$key]['key'] == $key) ? true : false;
                    } else {
                        $m[$key] = 1;
                        $isChecked = false;
                    }

                    $data[] = array(
                        'checkbox' => Html::checkbox('GridColumns[check][' . $key . ']', $isChecked, array('value' => $name)),
                        'name' => $name,
                        'sort' => Html::textInput('GridColumns[ordern][' . $key . ']', $m[$key]['ordern'], array('class' => 'form-control text-center'))
                    );
                }
            }

            // $provider = new ArrayDataProvider($data, ['pagination' => false]);
            $provider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => false
            ]);
            return $this->renderPartial('@panix/engine/views/_EditGridColumns', [
                'modelClass' => $modelClass,
                'provider' => $provider,
                'grid_id' => $grid_id,
                'module' => $mod
            ]);
        } else {
            throw new UnauthorizedHttpException(401);
        }
    }
}
