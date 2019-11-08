<?php

namespace panix\engine\widgets\like\actions;

use panix\engine\CMS;
use panix\engine\widgets\like\models\Like;
use Yii;
use yii\base\Action;
use yii\web\Response;

/**
 * Class LikeAction
 *
 * @property string $modelClass ActiveRecord model
 * @property integer $object_id PrimaryKey model
 *
 * @package panix\engine\widgets\like\actions
 */
class LikeAction extends Action
{
    private $modelClass;

    private $object_id;

    /**
     * @param string $type
     * @param int $id
     * @return array
     */
    public function run($type, $id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        $request = Yii::$app->request;
        $this->object_id = $id;
        $this->modelClass = Yii::$app->request->post('handler_hash');
        if ($request->isAjax && !Yii::$app->user->isGuest) {

            if ($this->modelClass) {
                $value = ($type == 'up') ? 1 : 0;
                $newLike = new Like();
                $newLike->object_id = $this->object_id;
                $newLike->user_id = Yii::$app->user->id;
                $newLike->handler_hash = $this->modelClass;
                $newLike->value = $value;

                /** @var Like $deleteQuery */
                if ($value) {//like

                    $deleteQuery = $this->queryByValue(0)->one();
                    if ($deleteQuery) {
                        $deleteQuery->value = $value;
                        $deleteQuery->save();
                    } else {
                        $deleteQuery = $this->queryByValue($value)->one();
                        if (!$deleteQuery) {
                            $newLike->save();
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
                            $newLike->save();
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
        } else {
            $response['message'] = Yii::t('app/error', 401);
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