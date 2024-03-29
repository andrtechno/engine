<?php

namespace panix\engine\widgets\like;

use Yii;
use panix\engine\data\Widget;
use panix\engine\CMS;
use panix\engine\widgets\like\models\Like;

class LikeWidget extends Widget
{

    public $model;
    public $hash;

    public function init()
    {
        parent::init();

        $reflection = (new \ReflectionClass($this->model));
        $this->hash = CMS::hash($reflection->getShortName());
        $this->setId(strtolower($reflection->getShortName()) . '-like-' . $this->model->primaryKey);
    }

    public function run()
    {
        LikeCssAsset::register($this->getView());
        if (!Yii::$app->user->isGuest) {
            LikeJsAsset::register($this->getView());
        }
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


        $response['activeDislike'] = false;
        $response['activeLike'] = false;
        if (!Yii::$app->user->isGuest) {
            $q = Like::find()->where([
                'object_id' => $this->model->primaryKey,
                'handler_hash' => $this->hash,
                'user_id' => Yii::$app->user->id
            ])->one();
            if ($q) {
                if ($q->value) {
                    $response['activeLike'] = true;
                    $response['activeDislike'] = false;
                } else {
                    $response['activeLike'] = false;
                    $response['activeDislike'] = true;
                }
            }
        }
        return $this->render($this->skin, [
            'response' => $response,
            'object_id' => $this->model->primaryKey,
        ]);

    }

}