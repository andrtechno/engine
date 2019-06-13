<?php

namespace panix\engine\widgets\like;

use Yii;
use panix\engine\data\Widget;

class LikeWidget extends Widget {

    public $model;

    public function init() {
        $this->publishAssets();
    }

    public function run() {
        $m = $this->model;
        $pk = $m->getObjectPkAttribute();
        $counter = $m->getLikes(true);


        if ($m->checkUserLiked()) {
            $this->render('voted', array(
                'counter' => $counter,
                'object_id' => $m->primaryKey,
            ));
        } else {
            echo 'access denie widget.like';
        }
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

    public function publishAssets() {
        $assets = dirname(__FILE__) . '/assets';
        $baseUrl = Yii::app()->assetManager->publish($assets);
        if (is_dir($assets)) {
            Yii::app()->clientScript->registerScriptFile($baseUrl . '/js/like.js', CClientScript::POS_HEAD);
            Yii::app()->clientScript->registerCssFile($baseUrl . '/css/like.css');
        } else {
            throw new Exception('Like - Error: Couldn\'t find assets to publish.');
        }
    }

}