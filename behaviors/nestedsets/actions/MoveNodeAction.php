<?php

namespace panix\engine\behaviors\nestedsets\actions;


use Yii;
use yii\web\Response;
use yii\rest\Action;
use panix\engine\behaviors\nestedsets\NestedSetsBehavior;

/**
 * Class MoveNodeAction
 * @package panix\engine\behaviors\nestedsets\actions
 */
class MoveNodeAction extends Action
{
    public $successMessage;

    /**
     * @inheritdoc
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$this->successMessage)
            $this->successMessage = Yii::t('app/default', 'NODE_MOVE');


        $json = [];
        $json['success'] = false;
        if (Yii::$app->request->isAjax) {
            /**
             * @var $modelClass \yii\db\ActiveRecord
             * @var $node NestedSetsBehavior|\yii\db\ActiveRecord
             * @var $target NestedSetsBehavior|\yii\db\ActiveRecord
             */
            $modelClass = $this->modelClass;
            $node = $this->findModel(Yii::$app->request->get('id'));
            $target = $modelClass::findOne(Yii::$app->request->get('ref'));

            if(!method_exists($modelClass,'rebuildFullPath')){
                die('no find method rebuildFullPath()');
            }
            $pos = (int)Yii::$app->request->get('position');

            if ($pos > 0) {
                $childs = $target->children()->all();
                if (isset($childs[$pos - 1]) && $childs[$pos - 1]['id'] != $node->id)
                    $node->moveAfter($childs[$pos - 1]);
            } else
                $node->moveAsFirst($target);


            $node->rebuildFullPath();
            $node->saveNode(false);


            $json['success'] = true;
            $json['message'] = $this->successMessage;
        } else {
            $json['message'] = 'error [1]';
        }

        return $json;
    }
}