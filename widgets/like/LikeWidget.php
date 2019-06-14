<?php

namespace panix\engine\widgets\like;

use panix\engine\widgets\like\models\Like;
use Yii;
use panix\engine\data\Widget;

class LikeWidget extends Widget {

    public $model;

    public function init() {

        //echo strtolower(basename(get_class($this->model)));
        $this->setId(strtolower(basename(get_class($this->model))).'-like-'.$this->model->primaryKey);
    }

    public function run() {
        LikeAsset::register($this->getView());
      //  $m = $this->model;
     //   $pk = $m->getObjectPkAttribute();
    //    $counter = $m->getLikes(true);




        $response['likeCount'] = Like::find()->where([
            'object_id' => $this->model->primaryKey,
            'model' => get_class($this->model),
            'value' => 1
        ])->count();


        $response['dislikeCount'] = Like::find()->where([
            'object_id' => $this->model->primaryKey,
            'model' => get_class($this->model),
            'value' => 0
        ])->count();

            return $this->render($this->skin, [
                'response' => $response,
                'object_id' => $this->model->primaryKey,
            ]);

    }
/*
    public function checkUserLiked($modelClass, $object_id) {

        $user = Yii::app()->user;
        if (!$user->isGuest) {
            $model = Like::model()->findByAttributes(array(
                'user_id' => $user->id,
                'model' => $modelClass,
                'object_id' => $object_id));
            if (isset($model)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }*/


}