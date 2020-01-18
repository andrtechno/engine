<?php

namespace panix\engine\behaviors\nestedsets\actions;

use Yii;
use yii\web\Response;
use yii\rest\Action;

/**
 * Class SwitchNodeAction
 * @package panix\engine\behaviors\nestedsets\actions
 */
class SwitchNodeAction extends Action
{
    public $onMessage;
    public $offMessage;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->offMessage)
            $this->offMessage = Yii::t('app/default', 'SWITCH_OFF');

        if (!$this->onMessage)
            $this->onMessage = Yii::t('app/default', 'SWITCH_ON');

        Yii::$app->response->format = Response::FORMAT_JSON;
        $json = [];
        $json['success'] = false;
        if (Yii::$app->request->isAjax) {
            /* @var $modelClass \yii\db\ActiveRecord */
            $modelClass = $this->modelClass;

            $node = $this->findModel(Yii::$app->request->get('id'));
            $node->switch = ($node->switch == 1) ? 0 : 1;
            $node->saveNode();
            return [
                'switch' => $node->switch,
                'message' => ($node->switch) ? $this->onMessage : $this->offMessage
            ];
        } else {
            $json['message'] = 'error [1]';
        }

        return $json;
    }
}