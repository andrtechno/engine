<?php

namespace panix\engine\behaviors\nestedsets\actions;

use Yii;
use yii\rest\Action;
use yii\web\Response;

/**
 * Class DeleteNodeAction
 * @package panix\engine\behaviors\nestedsets\actions
 */
class DeleteNodeAction extends Action
{
    public $successMessage;
    public $disallowIds = [];
    /**
     * Move a node (model) below the parent and in between left and right
     *
     * @param int $id the primaryKey of the moved node
     * @return array
     */
    public function run(int $id)
    {
        if (!$this->successMessage)
            $this->successMessage = Yii::t('app/default', 'SUCCESS_RECORD_DELETE');

        Yii::$app->response->format = Response::FORMAT_JSON;
        $json = [];

        $json['success'] = false;
        if (Yii::$app->request->isAjax) {
            /** @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|\yii\db\ActiveRecord $model */
            $model = $this->findModel($id);
            //Delete if not root node
            if ($model) {
                if (!in_array($model->id, $this->disallowIds)) {
                    foreach (array_reverse($model->descendants()->all()) as $subCategory) {
                        $json['objects'][] = $subCategory->id;
                        /** @var \panix\engine\behaviors\nestedsets\NestedSetsBehavior|\yii\db\ActiveRecord $subCategory */
                        $subCategory->deleteNode();

                    }
                    $json['objects'][] = $id;
                    $json['success'] = true;
                    $json['message'] = $this->successMessage;
                    $model->deleteNode();
                } else {
                    $json['message'] = 'Запрешено удаление данного объекта';
                }
            }
            return $json;
        }
    }
}