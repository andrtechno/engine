<?php

namespace panix\engine\widgets\like\actions;

use panix\engine\widgets\like\models\Like;
use Yii;
use yii\base\Action;
use yii\web\Response;

class LikeAction extends Action
{
    //public $model;

    public function run($type, $id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $modelClass = Yii::$app->request->post('model');
            if ($modelClass) {
                $like = new Like();
                $model = new $modelClass;
                $like->user_id = Yii::$app->user->id;
                $like->object_id = $id;
                $like->model = Yii::$app->request->post('model');
                $like->value = ($type == 'up') ? 1 : 0;
                $like->save();
                // print_r($model);
            }
            // echo $type;
            //   echo $id;
            //  print_r($this->model);
            //Yii::$app->response->format = Response::FORMAT_JSON;
        }

        return 'ss';
    }
}