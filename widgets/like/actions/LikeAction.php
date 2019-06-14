<?php

namespace panix\engine\widgets\like\actions;

use panix\engine\CMS;
use panix\engine\widgets\like\models\Like;
use Yii;
use yii\base\Action;
use yii\web\Response;

class LikeAction extends Action
{
    private $modelClass;
    private $object_id;

    public function run($type, $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        $request = Yii::$app->request;
        $this->object_id = $id;
        $this->modelClass = Yii::$app->request->post('handler_hash');
        if ($request->isAjax) {

            if ($this->modelClass) {
                $value = ($type == 'up') ? 1 : 0;

                $newlike = new Like();
                $newlike->object_id = $this->object_id;
                $newlike->user_id = Yii::$app->user->id;
                $newlike->handler_hash = $this->modelClass;
                $newlike->value = $value;

                if ($value) {//like
                    $deleteQuery = $this->queryByValue(0)->one();
                    if ($deleteQuery) {
                        $deleteQuery->value = $value;
                        $deleteQuery->save();
                    } else {
                        $deleteQuery = $this->queryByValue($value)->one();
                        if (!$deleteQuery) {
                            $newlike->save();
                        } else {
                            $deleteQuery->delete();
                        }
                    }
                } else {//dislike
                    $deleteQuery = $this->queryByValue(1)->one();
                    if ($deleteQuery) {
                        $deleteQuery->value = $value;
                        $deleteQuery->save();
                    } else {
                        $deleteQuery = $this->queryByValue($value)->one();
                        if (!$deleteQuery) {
                            $newlike->save();
                        } else {
                            $deleteQuery->delete();
                        }
                    }

                }
            }

            $q = Like::find()->where([
                'object_id' => $this->object_id,
                'handler_hash' => $this->modelClass,
                'user_id' => Yii::$app->user->id
            ])->one();
            $response['activeDislike'] = false;
            $response['activeLike'] = false;
            if ($q) {
                if ($q->value) {
                    $response['active'] = true;
                    $response['activeLike'] = true;
                    $response['activeDislike'] = false;
                } else {
                    $response['active'] = false;
                    $response['activeLike'] = false;
                    $response['activeDislike'] = true;
                }
            }


            $response['likeCount'] = CMS::counterUnit((int)$this->queryCount(1)->count());
            $response['dislikeCount'] = CMS::counterUnit((int)$this->queryCount(0)->count());
            $response['ratio'] = $response['likeCount'] - $response['dislikeCount'];
        }
        return $response;
    }


    /**
     * @param int $value
     * @return yii\db\Query
     */
    private function queryByValue($value = 1)
    {
        return Like::find()->where([
            'object_id' => $this->object_id,
            'user_id' => Yii::$app->user->id,
            'handler_hash' => $this->modelClass,
            'value' => $value
        ]);
    }

    /**
     * @param int $value
     * @return yii\db\Query
     */
    private function queryCount($value = 1)
    {
        return Like::find()->where([
            'object_id' => $this->object_id,
            'handler_hash' => $this->modelClass,
            'value' => $value
        ]);
    }
}