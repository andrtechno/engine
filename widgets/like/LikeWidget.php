<?php

namespace panix\engine\widgets\like;

use panix\engine\CMS;
use panix\engine\widgets\like\models\Like;
use Yii;
use panix\engine\data\Widget;

class LikeWidget extends Widget
{

    public $model;
    public $hash;
    public function init()
    {
        $this->hash=CMS::hash(get_class($this->model));
        $this->setId(strtolower(basename(get_class($this->model))) . '-like-' . $this->model->primaryKey);
    }

    public function run()
    {
        LikeAsset::register($this->getView());

        $response['likeCount'] = CMS::counterUnit(Like::find()->where([
            'object_id' => $this->model->primaryKey,
            'handler_hash' => $this->hash,
            'value' => 1
        ])->count());


        $response['dislikeCount'] = CMS::counterUnit(Like::find()->where([
            'object_id' => $this->model->primaryKey,
            'handler_hash' => $this->hash,
            'value' => 0
        ])->count());


        $q = Like::find()->where([
            'object_id' => $this->model->primaryKey,
            'handler_hash' => $this->hash,
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