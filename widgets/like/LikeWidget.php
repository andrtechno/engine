<?php

namespace panix\engine\widgets\like;

use panix\engine\CMS;
use panix\engine\widgets\like\models\Like;
use Yii;
use panix\engine\data\Widget;

class LikeWidget extends Widget
{

    public $model;

    public function init()
    {
        $this->setId(strtolower(basename(get_class($this->model))) . '-like-' . $this->model->primaryKey);
    }

    public function run()
    {
        LikeAsset::register($this->getView());

        $response['likeCount'] = CMS::counterUnit(Like::find()->where([
            'object_id' => $this->model->primaryKey,
            'model' => get_class($this->model),
            'value' => 1
        ])->count());


        $response['dislikeCount'] = CMS::counterUnit(Like::find()->where([
            'object_id' => $this->model->primaryKey,
            'model' => get_class($this->model),
            'value' => 0
        ])->count());


        $q = Like::find()->where([
            'object_id' => $this->model->primaryKey,
            'model' => get_class($this->model),
            'user_id' => Yii::$app->user->id
        ])->one();
        $response['activeDislike'] = false;
        $response['activeLike'] = false;

        if ($q) {
            if ($q->value) {
                $response['activeLike'] = true;
                $response['activeDislike'] = false;
            } else {
                $response['activeLike'] = false;
                $response['activeDislike'] = true;
            }
        }
        return $this->render($this->skin, [
            'response' => $response,
            'object_id' => $this->model->primaryKey,
        ]);

    }

}